<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Booking\ApproveOverbookingAction;
use App\Actions\Booking\CancelBookingAction;
use App\Actions\Booking\CreateBookingAction;
use App\Actions\Booking\ExportBookingsAction;
use App\Actions\Booking\ExportBookingsPdfAction;
use App\Actions\Booking\RejectOverbookingAction;
use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Partner;
use App\Models\PickupPoint;
use App\Models\Tour;
use App\Models\TourDeparture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Handles booking management for admin panel.
 */
final class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request): View
    {
        // Add create button flag for view
        $canCreate = true;

        $query = Booking::with(['partner', 'tourDeparture.tour', 'passengers'])
            ->whereHas('tourDeparture.tour')
            ->orderBy('created_at', 'desc');

        // Search by booking code or partner name
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('partner', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by partner
        if ($partnerId = $request->query('partner')) {
            $query->where('partner_id', $partnerId);
        }

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Filter by tour
        if ($tourId = $request->query('tour')) {
            $query->whereHas('tourDeparture', function ($q) use ($tourId) {
                $q->where('tour_id', $tourId);
            });
        }

        // Filter by date range
        if ($dateFrom = $request->query('date_from')) {
            $query->whereHas('tourDeparture', function ($q) use ($dateFrom) {
                $q->where('date', '>=', $dateFrom);
            });
        }

        if ($dateTo = $request->query('date_to')) {
            $query->whereHas('tourDeparture', function ($q) use ($dateTo) {
                $q->where('date', '<=', $dateTo);
            });
        }

        $bookings = $query->paginate(20);

        // Get filter options
        $partners = Partner::active()->orderBy('name')->get();
        $tours = Tour::active()->orderBy('name')->get();
        $statuses = BookingStatus::cases();

        // Get pending approval count
        $pendingCount = Booking::where('status', BookingStatus::SUSPENDED_REQUEST)->count();

        return view('admin.bookings.index', compact(
            'bookings',
            'partners',
            'tours',
            'statuses',
            'pendingCount',
            'canCreate'
        ));
    }

    /**
     * Export bookings to Excel.
     */
    public function export(Request $request, ExportBookingsAction $action): BinaryFileResponse
    {
        return $action->execute($request);
    }

    /**
     * Export bookings to PDF.
     */
    public function exportPdf(Request $request, ExportBookingsPdfAction $action): Response
    {
        return $action->execute($request);
    }

    /**
     * Show the form for creating a new booking on behalf of a partner.
     */
    public function create(): View
    {
        $partners = Partner::active()->orderBy('name')->get();

        $tours = Tour::active()
            ->whereHas('departures', function ($query) {
                $query->open()->future();
            })
            ->orderBy('name')
            ->get();

        $pickupPoints = PickupPoint::active()->ordered()->get();

        return view('admin.bookings.create', compact('partners', 'tours', 'pickupPoints'));
    }

    /**
     * Get available departures for a tour (AJAX).
     */
    public function getDepartures(Request $request, Tour $tour): JsonResponse
    {
        $date = $request->query('date');

        $departures = TourDeparture::where('tour_id', $tour->id)
            ->open()
            ->future()
            ->when($date, function ($query, $date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->map(function ($departure) {
                return [
                    'id' => $departure->id,
                    'date' => $departure->date->format('Y-m-d'),
                    'date_formatted' => $departure->date->format('d/m/Y'),
                    'time' => $departure->time,
                    'capacity' => $departure->capacity,
                    'booked' => $departure->booked_seats,
                    'remaining' => $departure->remaining_seats,
                    'past_cutoff' => $departure->isPastCutoff(),
                ];
            });

        return response()->json($departures);
    }

    /**
     * Store a newly created booking on behalf of a partner.
     */
    public function store(StoreBookingRequest $request, CreateBookingAction $action): RedirectResponse
    {
        $partner = Partner::findOrFail($request->validated('partner_id'));
        $departure = TourDeparture::findOrFail($request->validated('tour_departure_id'));

        try {
            $booking = $action->execute(
                $departure,
                $partner,
                $request->user(),
                $request->validated('passengers')
            );

            $message = $booking->status === BookingStatus::SUSPENDED_REQUEST
                ? __('bookings.created_as_overbooking', ['code' => $booking->booking_code])
                : __('bookings.created_successfully', ['code' => $booking->booking_code]);

            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking): View
    {
        $booking->load([
            'partner',
            'tourDeparture.tour',
            'passengers.pickupPoint',
            'creator',
            'payments',
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking): View
    {
        $booking->load([
            'partner',
            'tourDeparture.tour',
            'passengers.pickupPoint',
        ]);

        return view('admin.bookings.edit', compact('booking'));
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking->update($validated);

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking, CancelBookingAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $action->execute($booking, $validated['reason'] ?? null);

            $message = 'Booking cancelled successfully.';
            if ($booking->penalty_amount > 0) {
                $message .= ' A penalty of '.number_format($booking->penalty_amount, 2).' was applied.';
            }

            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('success', $message);
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Approve an overbooking request.
     */
    public function approve(Booking $booking, ApproveOverbookingAction $action): RedirectResponse
    {
        try {
            $action->execute($booking);

            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('success', 'Overbooking request approved. Booking is now confirmed.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reject an overbooking request.
     */
    public function reject(Request $request, Booking $booking, RejectOverbookingAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $action->execute($booking, $validated['reason'] ?? null);

            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('success', 'Overbooking request rejected.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        // Only allow deletion of cancelled/rejected/expired bookings
        if (! in_array($booking->status, [
            BookingStatus::CANCELLED,
            BookingStatus::REJECTED,
            BookingStatus::EXPIRED,
        ])) {
            return redirect()
                ->route('admin.bookings.index')
                ->with('error', 'Only cancelled, rejected, or expired bookings can be deleted.');
        }

        $booking->delete();

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }
}
