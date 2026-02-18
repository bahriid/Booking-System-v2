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
use Illuminate\View\View;

/**
 * Handles reporting and analytics for admin panel.
 */
final class ReportsController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index(Request $request): View
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

        return view('admin.reports.index', compact(
            'period',
            'stats',
            'bookingsByStatus',
            'revenueByTour',
            'topPartners',
            'bookingsTrend',
            'upcomingCapacity'
        ));
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

        return [
            'confirmed_bookings' => $confirmedBookings,
            'total_revenue' => $totalRevenue,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => $outstandingAmount,
            'total_passengers' => $totalPassengers,
            'cancelled_bookings' => $cancelledBookings,
            'penalty_amount' => $penaltyAmount,
            'active_partners' => $activePartners,
            'avg_booking_value' => $confirmedBookings > 0 ? $totalRevenue / $confirmedBookings : 0,
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
            ->mapWithKeys(function ($item) {
                return [$item->status->value => $item->count];
            })
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
            ->selectRaw('SUM(CASE WHEN bookings.payment_status = ? THEN bookings.total_amount ELSE 0 END) as paid_revenue', [PaymentStatus::PAID->value])
            ->join('tour_departures', 'tours.id', '=', 'tour_departures.tour_id')
            ->join('bookings', 'tour_departures.id', '=', 'bookings.tour_departure_id')
            ->where('bookings.status', BookingStatus::CONFIRMED)
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->groupBy('tours.id', 'tours.name', 'tours.code')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
    }

    /**
     * Get top partners by booking count.
     */
    private function getTopPartners(Carbon $startDate, Carbon $endDate): \Illuminate\Support\Collection
    {
        return Partner::select('partners.id', 'partners.name', 'partners.type')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->selectRaw('SUM(bookings.total_amount) as total_revenue')
            ->selectRaw('SUM(CASE WHEN bookings.payment_status = ? THEN bookings.total_amount ELSE 0 END) as paid_amount', [PaymentStatus::PAID->value])
            ->join('bookings', 'partners.id', '=', 'bookings.partner_id')
            ->where('bookings.status', BookingStatus::CONFIRMED)
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->groupBy('partners.id', 'partners.name', 'partners.type')
            ->orderByDesc('bookings_count')
            ->limit(10)
            ->get();
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

        // Fill in missing dates
        $result = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $result[$date] = $data[$date] ?? 0;
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
                    'id' => $departure->id,
                    'tour' => $departure->tour?->name ?? 'N/A',
                    'tour_code' => $departure->tour?->code ?? '-',
                    'date' => $departure->date->format('d/m/Y'),
                    'time' => $departure->time,
                    'capacity' => $departure->capacity,
                    'booked' => $departure->booked_seats,
                    'remaining' => $departure->remaining_seats,
                    'utilization' => $utilizationPercent,
                ];
            });
    }
}
