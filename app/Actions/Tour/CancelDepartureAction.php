<?php

declare(strict_types=1);

namespace App\Actions\Tour;

use App\Enums\BookingStatus;
use App\Enums\TourDepartureStatus;
use App\Models\Booking;
use App\Models\TourDeparture;
use App\Services\EmailService;

/**
 * Action to cancel a tour departure and notify affected partners.
 */
final class CancelDepartureAction
{
    public function __construct(
        private readonly EmailService $emailService
    ) {}

    /**
     * Execute the cancellation.
     *
     * @param TourDeparture $departure The departure to cancel
     * @param string|null $reason The cancellation reason
     * @param bool $isBadWeather Whether this is a bad weather cancellation (full credit)
     * @param bool $notifyPartners Whether to notify partners via email
     * @return array{cancelled: bool, affected_bookings: int, notifications_sent: int}
     */
    public function execute(
        TourDeparture $departure,
        ?string $reason = null,
        bool $isBadWeather = false,
        bool $notifyPartners = true
    ): array {
        // Check if already cancelled
        if ($departure->status === TourDepartureStatus::CANCELLED) {
            return [
                'cancelled' => false,
                'affected_bookings' => 0,
                'notifications_sent' => 0,
            ];
        }

        // Get affected bookings before cancelling
        $affectedBookings = $departure->bookings()
            ->whereIn('status', [
                BookingStatus::CONFIRMED->value,
                BookingStatus::SUSPENDED_REQUEST->value,
            ])
            ->with('partner')
            ->get();

        // Update departure status
        $departure->update([
            'status' => TourDepartureStatus::CANCELLED,
            'notes' => $this->buildCancellationNote($departure->notes, $reason, $isBadWeather),
        ]);

        $notificationsSent = 0;

        // Process affected bookings
        foreach ($affectedBookings as $booking) {
            // Update booking status
            $booking->update([
                'status' => BookingStatus::CANCELLED,
                'cancellation_reason' => $reason ?? ($isBadWeather ? 'Bad weather' : 'Tour departure cancelled'),
            ]);

            // If bad weather, record credit (in real implementation, would apply credit to partner account)
            if ($isBadWeather) {
                // The credit handling would be done in the accounting module
                // For now, we just note it in the audit log via the Auditable trait
            }

            // Send notification to partner
            if ($notifyPartners) {
                $sent = $this->emailService->sendDepartureCancelled(
                    $departure,
                    $booking,
                    $reason,
                    $isBadWeather
                );

                if ($sent) {
                    $notificationsSent++;
                }
            }
        }

        return [
            'cancelled' => true,
            'affected_bookings' => $affectedBookings->count(),
            'notifications_sent' => $notificationsSent,
        ];
    }

    /**
     * Build the cancellation note.
     */
    private function buildCancellationNote(?string $existingNotes, ?string $reason, bool $isBadWeather): string
    {
        $parts = [];

        if ($isBadWeather) {
            $parts[] = 'CANCELLED (Bad Weather)';
        } else {
            $parts[] = 'CANCELLED';
        }

        if ($reason) {
            $parts[] = "Reason: {$reason}";
        }

        $cancellationNote = implode(' - ', $parts);

        if ($existingNotes) {
            return "{$existingNotes}\n{$cancellationNote}";
        }

        return $cancellationNote;
    }
}
