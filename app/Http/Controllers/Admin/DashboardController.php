<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Partner;
use App\Models\TourDeparture;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Admin Dashboard Controller.
 * Provides real-time statistics and data for the admin dashboard.
 */
final class DashboardController extends Controller
{
    public function index(): InertiaResponse
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        // Stats Cards
        $todaysBookingsCount = Booking::whereDate('created_at', $today)
            ->whereNotIn('status', [BookingStatus::CANCELLED, BookingStatus::EXPIRED])
            ->count();

        $weeklyPassengersCount = BookingPassenger::whereHas('booking', function ($query) use ($weekStart, $weekEnd) {
            $query->whereHas('tourDeparture', function ($q) use ($weekStart, $weekEnd) {
                $q->whereBetween('date', [$weekStart, $weekEnd]);
            })->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::SUSPENDED_REQUEST]);
        })->count();

        $pendingRequestsCount = Booking::where('status', BookingStatus::SUSPENDED_REQUEST)->count();

        $totalOutstanding = Booking::where('status', BookingStatus::CONFIRMED)
            ->where('payment_status', '!=', PaymentStatus::PAID)
            ->selectRaw('SUM(total_amount - COALESCE((
                SELECT SUM(amount) FROM booking_payment WHERE booking_payment.booking_id = bookings.id
            ), 0)) as outstanding')
            ->value('outstanding') ?? 0;

        // Pending Overbooking Requests
        $pendingRequests = Booking::with(['partner', 'tourDeparture.tour', 'passengers'])
            ->where('status', BookingStatus::SUSPENDED_REQUEST)
            ->whereHas('tourDeparture.tour')
            ->orderBy('suspended_until', 'asc')
            ->paginate(3, ['*'], 'pending_page');

        // Today's Departures
        $todaysDepartures = TourDeparture::with(['tour', 'bookings' => function ($query) {
                $query->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::SUSPENDED_REQUEST]);
            }, 'bookings.passengers'])
            ->whereHas('tour')
            ->where('date', $today)
            ->orderBy('time', 'asc')
            ->paginate(5, ['*'], 'departures_page');

        // Recent Bookings
        $recentBookings = Booking::with(['partner', 'tourDeparture.tour', 'passengers'])
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::SUSPENDED_REQUEST])
            ->whereHas('tourDeparture.tour')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'bookings_page');

        // Partner Outstanding Balances
        $partnerOutstanding = Partner::select('partners.*')
            ->selectRaw('(
                SELECT COALESCE(SUM(
                    b.total_amount - COALESCE((
                        SELECT SUM(bp.amount) FROM booking_payment bp WHERE bp.booking_id = b.id
                    ), 0)
                ), 0)
                FROM bookings b
                WHERE b.partner_id = partners.id
                AND b.status = ?
                AND b.payment_status != ?
                AND b.deleted_at IS NULL
            ) as outstanding_balance', [BookingStatus::CONFIRMED->value, PaymentStatus::PAID->value])
            ->selectRaw('(
                SELECT COUNT(*) FROM bookings b
                WHERE b.partner_id = partners.id
                AND b.status = ?
                AND b.payment_status != ?
                AND b.deleted_at IS NULL
            ) as unpaid_bookings', [BookingStatus::CONFIRMED->value, PaymentStatus::PAID->value])
            ->whereRaw('(
                SELECT COALESCE(SUM(
                    b.total_amount - COALESCE((
                        SELECT SUM(bp.amount) FROM booking_payment bp WHERE bp.booking_id = b.id
                    ), 0)
                ), 0)
                FROM bookings b
                WHERE b.partner_id = partners.id
                AND b.status = ?
                AND b.payment_status != ?
                AND b.deleted_at IS NULL
            ) > 0', [BookingStatus::CONFIRMED->value, PaymentStatus::PAID->value])
            ->orderByDesc('outstanding_balance')
            ->paginate(5, ['*'], 'partners_page');

        return Inertia::render('admin/dashboard', compact(
            'todaysBookingsCount',
            'weeklyPassengersCount',
            'pendingRequestsCount',
            'totalOutstanding',
            'pendingRequests',
            'todaysDepartures',
            'recentBookings',
            'partnerOutstanding'
        ));
    }

    private function formatTimeRemaining(int $minutes): string
    {
        if ($minutes <= 0) {
            return 'Expired';
        }

        if ($minutes < 60) {
            return "{$minutes} min";
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return "{$hours}h {$mins}m";
    }

    private function getUrgencyClass(int $minutes): string
    {
        if ($minutes <= 30) {
            return 'text-danger';
        }
        if ($minutes <= 60) {
            return 'text-warning';
        }
        return 'text-muted';
    }

    private function getDepartureStatus(int $booked, int $pending, int $capacity): string
    {
        if ($booked >= $capacity) {
            return $pending > 0 ? 'Overbooking' : 'Full';
        }
        if ($booked > 0) {
            return 'Open';
        }
        return 'Empty';
    }

    private function getDepartureStatusClass(int $booked, int $pending, int $capacity): string
    {
        if ($pending > 0 && $booked >= $capacity) {
            return 'badge-light-warning';
        }
        if ($booked >= $capacity) {
            return 'badge-light-success';
        }
        if ($booked > 0) {
            return 'badge-light-primary';
        }
        return 'badge-light-secondary';
    }

    private function getProgressClass(int $percent): string
    {
        if ($percent >= 100) {
            return 'bg-success';
        }
        if ($percent >= 75) {
            return 'bg-primary';
        }
        if ($percent >= 50) {
            return 'bg-warning';
        }
        return 'bg-danger';
    }
}
