<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Sends a daily recap email to admin summarizing all bookings received that day.
 */
class SendDailyBookingRecapCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:daily-recap
                            {--date= : Specific date to recap (Y-m-d format, defaults to today)}
                            {--dry-run : Show stats without sending email}';

    /**
     * The console command description.
     */
    protected $description = 'Send a daily booking recap email to admin summarizing all bookings received';

    public function __construct(
        private readonly EmailService $emailService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateOption = $this->option('date');
        $dryRun = $this->option('dry-run');

        try {
            $date = $dateOption
                ? Carbon::createFromFormat('Y-m-d', $dateOption)->startOfDay()
                : Carbon::today();
        } catch (\Exception $e) {
            $this->error('Invalid date format. Please use Y-m-d (e.g. 2026-02-17).');

            return Command::FAILURE;
        }

        $this->info("Generating daily booking recap for {$date->format('Y-m-d')}...");

        $bookings = Booking::query()
            ->whereDate('created_at', $date)
            ->with(['partner', 'tourDeparture.tour', 'passengers'])
            ->orderBy('created_at')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings found for this date. Skipping email.');

            return Command::SUCCESS;
        }

        $stats = $this->calculateStats($bookings);

        $this->info("Found {$stats['total_bookings']} booking(s):");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Bookings', $stats['total_bookings']],
                ['Confirmed', $stats['confirmed']],
                ['Overbooking Requests', $stats['overbooking_requests']],
                ['Cancelled', $stats['cancelled']],
                ['Total Passengers', $stats['total_passengers']],
                ['Total Revenue', '€'.number_format($stats['total_revenue'], 2)],
            ]
        );

        if (! empty($stats['by_tour'])) {
            $this->newLine();
            $this->info('Breakdown by Tour:');
            $this->table(
                ['Tour', 'Bookings', 'Pax'],
                collect($stats['by_tour'])->map(fn (array $t) => [$t['name'], $t['bookings'], $t['passengers']])->toArray()
            );
        }

        if (! empty($stats['by_partner'])) {
            $this->newLine();
            $this->info('Breakdown by Partner:');
            $this->table(
                ['Partner', 'Bookings', 'Pax', 'Revenue'],
                collect($stats['by_partner'])->map(fn (array $p) => [$p['name'], $p['bookings'], $p['passengers'], '€'.number_format($p['revenue'], 2)])->toArray()
            );
        }

        if ($dryRun) {
            $this->warn('DRY RUN - Email not sent.');

            return Command::SUCCESS;
        }

        $sent = $this->emailService->sendDailyBookingRecap($date, $bookings, $stats);

        if ($sent) {
            $this->info('Daily recap email sent successfully.');

            Log::info('Daily booking recap sent', [
                'date' => $date->format('Y-m-d'),
                'total_bookings' => $stats['total_bookings'],
                'total_revenue' => $stats['total_revenue'],
            ]);
        } else {
            $this->error('Failed to send daily recap email.');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Calculate aggregate statistics from the bookings collection.
     *
     * @return array<string, mixed>
     */
    private function calculateStats(\Illuminate\Support\Collection $bookings): array
    {
        $stats = [
            'total_bookings' => $bookings->count(),
            'confirmed' => $bookings->where('status', BookingStatus::CONFIRMED)->count(),
            'overbooking_requests' => $bookings->where('status', BookingStatus::SUSPENDED_REQUEST)->count(),
            'cancelled' => $bookings->where('status', BookingStatus::CANCELLED)->count(),
            'total_passengers' => $bookings->sum(fn (Booking $b) => $b->passengers->count()),
            'total_revenue' => $bookings->sum('total_amount'),
            'by_tour' => [],
            'by_partner' => [],
        ];

        // Group by tour
        $byTour = $bookings->groupBy(fn (Booking $b) => $b->tourDeparture?->tour?->id ?? 0);
        foreach ($byTour as $tourId => $tourBookings) {
            $tour = $tourBookings->first()->tourDeparture?->tour;
            $stats['by_tour'][] = [
                'name' => $tour?->name ?? 'Unknown',
                'bookings' => $tourBookings->count(),
                'passengers' => $tourBookings->sum(fn (Booking $b) => $b->passengers->count()),
            ];
        }

        // Group by partner
        $byPartner = $bookings->groupBy('partner_id');
        foreach ($byPartner as $partnerId => $partnerBookings) {
            $partner = $partnerBookings->first()->partner;
            $stats['by_partner'][] = [
                'name' => $partner?->name ?? 'Unknown',
                'bookings' => $partnerBookings->count(),
                'passengers' => $partnerBookings->sum(fn (Booking $b) => $b->passengers->count()),
                'revenue' => $partnerBookings->sum('total_amount'),
            ];
        }

        return $stats;
    }
}
