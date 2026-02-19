<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Partner;
use App\Models\Tour;
use App\Models\TourDeparture;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Handles reporting and analytics for admin panel.
 */
final class ReportsController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index(Request $request): InertiaResponse
    {
        $period = $request->query('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Summary statistics
        $stats = $this->getStatistics($startDate, $endDate);

        // Bookings by status
        $bookingsByStatus = $this->getBookingsByStatus($startDate, $endDate);

        // Revenue by tour
        $revenueByTour = $this->getRevenueByTour($startDate, $endDate);

        // Top partners
        $topPartners = $this->getTopPartners($startDate, $endDate);

        // Bookings trend (daily for last 30 days)
        $bookingsTrend = $this->getBookingsTrend();

        // Upcoming departures capacity
        $upcomingCapacity = $this->getUpcomingCapacity();

        return Inertia::render('admin/reports/index', [
            'period' => $period,
            'stats' => $stats,
            'bookingsByStatus' => $bookingsByStatus,
            'revenueByTour' => $revenueByTour,
            'topPartners' => $topPartners,
            'bookingsTrend' => $bookingsTrend,
            'upcomingCapacity' => $upcomingCapacity,
            'filters' => $request->only(['period']),
        ]);
    }

    /**
     * Get the start date based on the selected period.
     */
    private function getStartDate(string $period): Carbon
    {
        return match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            'all' => Carbon::create(2020, 1, 1),
            default => now()->subMonth(),
        };
    }

    /**
     * Get summary statistics.
     */
    private function getStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $confirmedBookings = Booking::where('status', BookingStatus::CONFIRMED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalRevenue = Booking::where('status', BookingStatus::CONFIRMED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $paidAmount = Booking::where('status', BookingStatus::CONFIRMED)
            ->where('payment_status', PaymentStatus::PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $outstandingAmount = Booking::where('status', BookingStatus::CONFIRMED)
            ->where('payment_status', PaymentStatus::UNPAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalPassengers = Booking::where('status', BookingStatus::CONFIRMED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->withCount('passengers')
            ->get()
            ->sum('passengers_count');

        $cancelledBookings = Booking::where('status', BookingStatus::CANCELLED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $penaltyAmount = Booking::where('status', BookingStatus::CANCELLED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('penalty_amount');

        $activePartners = Partner::where('is_active', true)
            ->whereHas('bookings', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();

        $totalBookings = $confirmedBookings + $cancelledBookings;
        $cancellationRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;

        return [
            'totalBookings' => $confirmedBookings,
            'totalPassengers' => $totalPassengers,
            'totalRevenue' => (float) $totalRevenue,
            'avgBookingValue' => $confirmedBookings > 0 ? (float) $totalRevenue / $confirmedBookings : 0,
            'cancellationRate' => round($cancellationRate, 1),
        ];
    }

    /**
     * Get bookings grouped by status.
     */
    private function getBookingsByStatus(Carbon $startDate, Carbon $endDate): array
    {
        return Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status->value,
                    'count' => (int) $item->count,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get revenue by tour.
     */
    private function getRevenueByTour(Carbon $startDate, Carbon $endDate): \Illuminate\Support\Collection
    {
        return Tour::select('tours.id', 'tours.name', 'tours.code')
            ->selectRaw('COUNT(DISTINCT bookings.id) as bookings_count')
            ->selectRaw('SUM(bookings.total_amount) as total_revenue')
            ->join('tour_departures', 'tours.id', '=', 'tour_departures.tour_id')
            ->join('bookings', 'tour_departures.id', '=', 'bookings.tour_departure_id')
            ->where('bookings.status', BookingStatus::CONFIRMED)
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->groupBy('tours.id', 'tours.name', 'tours.code')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get()
            ->map(fn ($t) => [
                'tour_name' => $t->name,
                'revenue' => (float) $t->total_revenue,
                'bookings' => (int) $t->bookings_count,
            ])
            ->values();
    }

    /**
     * Get top partners by booking count.
     */
    private function getTopPartners(Carbon $startDate, Carbon $endDate): \Illuminate\Support\Collection
    {
        return Partner::select('partners.id', 'partners.name', 'partners.type')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->selectRaw('SUM(bookings.total_amount) as total_revenue')
            ->join('bookings', 'partners.id', '=', 'bookings.partner_id')
            ->where('bookings.status', BookingStatus::CONFIRMED)
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->groupBy('partners.id', 'partners.name', 'partners.type')
            ->orderByDesc('bookings_count')
            ->limit(10)
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'bookings' => (int) $p->bookings_count,
                'revenue' => (float) $p->total_revenue,
            ])
            ->values();
    }

    /**
     * Get bookings trend for the last 30 days.
     */
    private function getBookingsTrend(): array
    {
        $data = Booking::where('status', BookingStatus::CONFIRMED)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Fill in missing dates and return as array of objects
        $result = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $result[] = [
                'date' => $date,
                'count' => $data[$date] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Get upcoming departures capacity utilization.
     */
    private function getUpcomingCapacity(): \Illuminate\Support\Collection
    {
        return TourDeparture::with('tour')
            ->whereHas('tour')
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where('date', '<=', now()->addDays(7)->format('Y-m-d'))
            ->where('status', 'open')
            ->orderBy('date')
            ->orderBy('time')
            ->limit(15)
            ->get()
            ->map(function ($departure) {
                $utilizationPercent = $departure->capacity > 0
                    ? round(($departure->booked_seats / $departure->capacity) * 100)
                    : 0;

                return [
                    'tour_name' => $departure->tour?->name ?? 'N/A',
                    'date' => $departure->date->format('Y-m-d'),
                    'capacity' => (int) $departure->capacity,
                    'booked' => (int) $departure->booked_seats,
                ];
            });
    }
}
