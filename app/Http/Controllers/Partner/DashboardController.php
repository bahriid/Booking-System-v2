<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles partner dashboard.
 */
final class DashboardController extends Controller
{
    /**
     * Display the partner dashboard.
     */
    public function index(Request $request): View
    {
        $partner = $request->user()->partner;

        // Stats for this month
        $bookingsThisMonth = Booking::forPartner($partner->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $passengersThisMonth = Booking::forPartner($partner->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
            ->withCount('passengers')
            ->get()
            ->sum('passengers_count');

        $pendingRequests = Booking::forPartner($partner->id)
            ->where('status', BookingStatus::SUSPENDED_REQUEST)
            ->count();

        // Recent bookings (paginated)
        $recentBookings = Booking::with(['tourDeparture.tour' => fn ($q) => $q->withTrashed(), 'passengers'])
            ->forPartner($partner->id)
            ->whereHas('tourDeparture')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'recent_page');

        // Upcoming tours (next 7 days, confirmed only, paginated)
        $upcomingBookings = Booking::with(['tourDeparture.tour' => fn ($q) => $q->withTrashed(), 'passengers'])
            ->forPartner($partner->id)
            ->whereIn('bookings.status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
            ->whereHas('tourDeparture', function ($query) {
                $query->whereBetween('date', [now()->toDateString(), now()->addDays(7)->toDateString()]);
            })
            ->join('tour_departures', 'bookings.tour_departure_id', '=', 'tour_departures.id')
            ->orderBy('tour_departures.date', 'asc')
            ->select('bookings.*')
            ->paginate(4, ['*'], 'upcoming_page');

        return view('partner.dashboard', compact(
            'partner',
            'bookingsThisMonth',
            'passengersThisMonth',
            'pendingRequests',
            'recentBookings',
            'upcomingBookings'
        ));
    }
}
