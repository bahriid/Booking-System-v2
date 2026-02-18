<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Automatically expires overbooking requests that have been pending for more than 2 hours.
 */
class ExpireOverbookingRequestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:expire-overbooking
                            {--hours=2 : Hours after which to expire requests}
                            {--dry-run : Run without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Expire overbooking requests that have been pending for more than 2 hours';

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
        $hours = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');
        $cutoffTime = Carbon::now()->subHours($hours);

        $this->info("Looking for SUSPENDED_REQUEST bookings older than {$hours} hours...");
        $this->info("Cutoff time: {$cutoffTime->toDateTimeString()}");

        $pendingBookings = Booking::query()
            ->where('status', BookingStatus::SUSPENDED_REQUEST)
            ->where('created_at', '<', $cutoffTime)
            ->with(['partner', 'tourDeparture.tour'])
            ->get();

        if ($pendingBookings->isEmpty()) {
            $this->info('No expired overbooking requests found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pendingBookings->count()} booking(s) to expire.");

        if ($dryRun) {
            $this->warn('DRY RUN - No changes will be made.');
            foreach ($pendingBookings as $booking) {
                $this->line("  Would expire: {$booking->booking_code} (created: {$booking->created_at})");
            }
            return Command::SUCCESS;
        }

        $expiredCount = 0;
        $errorCount = 0;

        foreach ($pendingBookings as $booking) {
            try {
                DB::transaction(function () use ($booking) {
                    $booking->update([
                        'status' => BookingStatus::EXPIRED,
                        'expired_at' => now(),
                    ]);

                    // Send expiry notification email
                    $this->sendExpiryNotification($booking);
                });

                $this->info("Expired: {$booking->booking_code}");
                $expiredCount++;

                Log::info('Overbooking request expired', [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'partner_id' => $booking->partner_id,
                    'created_at' => $booking->created_at,
                ]);
            } catch (\Exception $e) {
                $this->error("Failed to expire {$booking->booking_code}: {$e->getMessage()}");
                $errorCount++;

                Log::error('Failed to expire overbooking request', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("Summary: {$expiredCount} expired, {$errorCount} errors.");

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Send expiry notification email to partner and admin.
     */
    private function sendExpiryNotification(Booking $booking): void
    {
        try {
            $this->emailService->sendOverbookingExpired($booking);
        } catch (\Exception $e) {
            Log::warning('Failed to send expiry notification email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
