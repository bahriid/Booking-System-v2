<?php

declare(strict_types=1);

namespace App\Http\Controllers\Driver;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\TourDeparture;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Handles driver dashboard and shift display.
 */
final class DashboardController extends Controller
{
    /**
     * Display the driver dashboard with assigned shifts.
     */
    public function index(Request $request): InertiaResponse
    {
        $driver = $request->user();
        $selectedDate = $request->query('date')
            ? \Carbon\Carbon::parse($request->query('date'))
            : now();

        // Today's shifts assigned to this driver (paginated)
        $todaysShifts = TourDeparture::with([
            'tour',
            'bookings' => function ($query) {
                $query->whereIn('status', [
                    BookingStatus::CONFIRMED,
                    BookingStatus::COMPLETED,
                ]);
            },
            'bookings.passengers.pickupPoint',
            'bookings.partner',
        ])
            ->whereHas('tour')
            ->forDriver($driver->id)
            ->whereDate('date', $selectedDate->toDateString())
            ->orderBy('time')
            ->paginate(6, ['*'], 'today_page')
            ->through(function ($departure) {
                $departure->total_passengers = $departure->bookings->sum(fn($b) => $b->passengers->count());
                $departure->pax_counts = $departure->bookings
                    ->flatMap(fn($b) => $b->passengers)
                    ->groupBy('pax_type')
                    ->map->count();

                // Group passengers by pickup point
                $departure->pickup_summary = $departure->bookings
                    ->flatMap(fn($b) => $b->passengers)
                    ->groupBy(fn($p) => $p->pickupPoint?->name ?? 'Not specified')
                    ->map->count();

                return $departure;
            });

        // Upcoming shifts (next 30 days, excluding today/selected date, paginated)
        $upcomingShifts = TourDeparture::with([
            'tour',
            'bookings' => function ($query) {
                $query->whereIn('status', [
                    BookingStatus::CONFIRMED,
                    BookingStatus::COMPLETED,
                ]);
            },
            'bookings.passengers',
        ])
            ->whereHas('tour')
            ->forDriver($driver->id)
            ->where('date', '>', $selectedDate->toDateString())
            ->where('date', '<=', $selectedDate->copy()->addDays(30)->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->paginate(10, ['*'], 'upcoming_page')
            ->through(function ($departure) {
                $departure->total_passengers = $departure->bookings->sum(fn($b) => $b->passengers->count());
                $departure->pax_counts = $departure->bookings
                    ->flatMap(fn($b) => $b->passengers)
                    ->groupBy('pax_type')
                    ->map->count();

                return $departure;
            });

        return Inertia::render('driver/dashboard', [
            'driver' => $driver,
            'selectedDate' => $selectedDate->toDateString(),
            'todaysShifts' => $todaysShifts,
            'upcomingShifts' => $upcomingShifts,
        ]);
    }

    /**
     * Get manifest data for a specific departure (AJAX).
     */
    public function manifest(Request $request, TourDeparture $departure): View
    {
        $driver = $request->user();

        // Ensure the driver is assigned to this departure
        if ((int) $departure->driver_id !== (int) $driver->id) {
            abort(403, 'You are not assigned to this departure.');
        }

        $departure->load([
            'tour',
            'bookings' => function ($query) {
                $query->whereIn('status', [
                    BookingStatus::CONFIRMED,
                    BookingStatus::COMPLETED,
                ]);
            },
            'bookings.passengers.pickupPoint',
            'bookings.partner',
        ]);

        // Collect all passengers with booking info
        $passengers = $departure->bookings->flatMap(function ($booking) {
            return $booking->passengers->map(function ($passenger) use ($booking) {
                $passenger->booking_code = $booking->booking_code ?? '-';
                $passenger->partner_name = $booking->partner?->name ?? '-';

                return $passenger;
            });
        })->sortBy([
            fn($a, $b) => strcmp($a->pickupPoint?->name ?? 'ZZZ', $b->pickupPoint?->name ?? 'ZZZ'),
            fn($a, $b) => strcmp($a->last_name ?? '', $b->last_name ?? ''),
        ])->values();

        // Pax summary
        $paxCounts = $passengers->groupBy(fn($p) => $p->pax_type?->value ?? 'adult')->map->count();

        // Passengers with allergies
        $allergiesCount = $passengers->filter(fn($p) => !empty($p->allergies))->count();

        // Pickup summary
        $pickupSummary = $passengers
            ->groupBy(fn($p) => $p->pickupPoint?->name ?? 'Not specified')
            ->map(function ($group) {
                $time = $group->first()->pickupPoint?->default_time;

                return [
                    'count' => $group->count(),
                    'time' => $time ? (string) $time : '-',
                ];
            });

        return view('driver.partials.manifest', compact(
            'departure',
            'passengers',
            'paxCounts',
            'allergiesCount',
            'pickupSummary'
        ));
    }
}
