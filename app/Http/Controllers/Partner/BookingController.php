<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner;

use App\Actions\Booking\CancelBookingAction;
use App\Actions\Booking\CreateBookingAction;
use App\Actions\Booking\UpdateBookingAction;
use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreBookingRequest;
use App\Models\Booking;
use App\Models\PickupPoint;
use App\Models\Tour;
use App\Models\TourDeparture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Handles booking operations for partner portal.
 */
final class BookingController extends Controller
{
    /**
     * Display a listing of partner's bookings.
     */
    public function index(Request $request): InertiaResponse
    {
        $partner = $request->user()->partner;

        $bookings = Booking::with(['tourDeparture.tour', 'passengers'])
            ->whereHas('tourDeparture.tour')
            ->forPartner($partner->id)
            ->when($request->query('search'), function ($query, $search) {
                $query->where('booking_code', 'like', "%{$search}%");
            })
            ->when($request->query('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->query('tour'), function ($query, $tourId) {
                $query->whereHas('tourDeparture', fn ($q) => $q->where('tour_id', $tourId));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $tours = Tour::active()->orderBy('name')->get();
        $statuses = BookingStatus::cases();

        return Inertia::render('partner/bookings/index', [
            'bookings' => $bookings,
            'tours' => $tours,
            'statuses' => collect($statuses)->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()]),
            'filters' => $request->only(['search', 'status', 'tour']),
        ]);
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create(Request $request): InertiaResponse
    {
        $tours = Tour::active()
            ->whereHas('departures', function ($query) {
                $query->open()->future();
            })
            ->orderBy('name')
            ->get();

        $pickupPoints = PickupPoint::active()->ordered()->get();

        return Inertia::render('partner/bookings/create', compact('tours', 'pickupPoints'));
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
     * Store a newly created booking.
     */
    public function store(StoreBookingRequest $request, CreateBookingAction $action): RedirectResponse
    {
        $partner = $request->user()->partner;
        $departure = TourDeparture::findOrFail($request->validated('tour_departure_id'));

        try {
            $booking = $action->execute(
                $departure,
                $partner,
                $request->user(),
                $request->validated('passengers')
            );

            $message = $booking->status === BookingStatus::SUSPENDED_REQUEST
                ? 'Booking created as overbooking request. Awaiting admin approval (2 hours).'
                : "Booking {$booking->booking_code} confirmed successfully.";

            return redirect()
                ->route('partner.bookings.show', $booking)
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
    public function show(Request $request, Booking $booking): InertiaResponse
    {
        $partner = $request->user()->partner;

        // Ensure partner can only view their own bookings
        if ($booking->partner_id !== $partner->id) {
            abort(403, 'You can only view your own bookings.');
        }

        $booking->load(['tourDeparture.tour', 'passengers.pickupPoint']);

        return Inertia::render('partner/bookings/show', compact('booking'));
    }

    /**
     * Show the form for editing the booking.
     */
    public function edit(Request $request, Booking $booking): InertiaResponse|RedirectResponse
    {
        $partner = $request->user()->partner;

        // Ensure partner can only edit their own bookings
        if ($booking->partner_id !== $partner->id) {
            abort(403, 'You can only edit your own bookings.');
        }

        // Check if booking can be modified
        if (! $booking->status->canBeModified()) {
            return redirect()
                ->route('partner.bookings.show', $booking)
                ->with('error', 'This booking cannot be modified.');
        }

        // Check cut-off time
        if ($booking->tourDeparture->isPastCutoff()) {
            return redirect()
                ->route('partner.bookings.show', $booking)
                ->with('error', 'This booking cannot be modified after the cut-off time.');
        }

        $booking->load(['tourDeparture.tour', 'passengers.pickupPoint']);
        $pickupPoints = PickupPoint::active()->ordered()->get();

        return Inertia::render('partner/bookings/edit', compact('booking', 'pickupPoints'));
    }

    /**
     * Update the booking.
     */
    public function update(Request $request, Booking $booking, UpdateBookingAction $action): RedirectResponse
    {
        $partner = $request->user()->partner;

        // Ensure partner can only update their own bookings
        if ($booking->partner_id !== $partner->id) {
            abort(403, 'You can only update your own bookings.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'passengers' => ['nullable', 'array'],
            'passengers.*.id' => ['required', 'exists:booking_passengers,id'],
            'passengers.*.first_name' => ['required', 'string', 'max:100'],
            'passengers.*.last_name' => ['required', 'string', 'max:100'],
            'passengers.*.phone' => ['nullable', 'string', 'max:50'],
            'passengers.*.allergies' => ['nullable', 'string', 'max:255'],
            'passengers.*.notes' => ['nullable', 'string', 'max:500'],
            'passengers.*.pickup_point_id' => ['nullable', 'exists:pickup_points,id'],
            'new_passengers' => ['nullable', 'array'],
            'new_passengers.*.pax_type' => ['required', 'string', 'in:adult,child,infant'],
            'new_passengers.*.first_name' => ['required', 'string', 'max:100'],
            'new_passengers.*.last_name' => ['required', 'string', 'max:100'],
            'new_passengers.*.phone' => ['nullable', 'string', 'max:50'],
            'new_passengers.*.allergies' => ['nullable', 'string', 'max:255'],
            'new_passengers.*.notes' => ['nullable', 'string', 'max:500'],
            'new_passengers.*.pickup_point_id' => ['required', 'exists:pickup_points,id'],
            'removed_passengers' => ['nullable', 'array'],
            'removed_passengers.*' => ['integer', 'exists:booking_passengers,id'],
        ]);

        try {
            $result = $action->execute(
                $booking,
                $validated['passengers'] ?? [],
                $validated['notes'] ?? null,
                $validated['new_passengers'] ?? [],
                $validated['removed_passengers'] ?? []
            );

            $message = empty($result['changes'])
                ? 'No changes were made.'
                : 'Booking updated successfully.';

            return redirect()
                ->route('partner.bookings.show', $booking)
                ->with('success', $message);
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('partner.bookings.show', $booking)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking, CancelBookingAction $action): RedirectResponse
    {
        $partner = $request->user()->partner;

        // Ensure partner can only cancel their own bookings
        if ($booking->partner_id !== $partner->id) {
            abort(403, 'You can only cancel your own bookings.');
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $action->execute($booking, $validated['reason'] ?? null);

            $message = 'Booking cancelled successfully.';
            if ($booking->penalty_amount > 0) {
                $message .= ' A penalty of '.number_format((float) $booking->penalty_amount, 2).' was applied.';
            }

            return redirect()
                ->route('partner.bookings.show', $booking)
                ->with('success', $message);
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('partner.bookings.show', $booking)
                ->with('error', $e->getMessage());
        }
    }
}
