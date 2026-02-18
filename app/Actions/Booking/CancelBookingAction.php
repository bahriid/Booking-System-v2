<?php

declare(strict_types=1);

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Cancels a booking with appropriate penalty calculation.
 */
final class CancelBookingAction
{
    public function __construct(
        private readonly EmailService $emailService
    ) {}

    /**
     * Execute the booking cancellation.
     *
     * @param Booking $booking The booking to cancel
     * @param string|null $reason The cancellation reason
     * @return Booking The updated booking
     */
    public function execute(Booking $booking, ?string $reason = null): Booking
    {
        if (!$booking->status->canBeCancelled()) {
            throw new \InvalidArgumentException("Booking cannot be cancelled in its current state.");
        }

        return DB::transaction(function () use ($booking, $reason) {
            $penaltyAmount = $this->calculatePenalty($booking);
            $hasPenalty = $penaltyAmount > 0;

            $booking->update([
                'status' => BookingStatus::CANCELLED,
                'penalty_amount' => $penaltyAmount,
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            $booking = $booking->fresh();

            // Send cancellation notification to partner
            $this->emailService->sendBookingCancelled($booking, $reason, $hasPenalty);

            return $booking;
        });
    }

    /**
     * Calculate the penalty amount based on cancellation timing.
     *
     * Business rules (spec: Section 8.1):
     * - More than 48 hours before departure: No penalty (free cancellation)
     * - 24-48 hours before departure: No penalty (grace period)
     * - Less than 24 hours before departure: 100% penalty
     */
    private function calculatePenalty(Booking $booking): float
    {
        $departure = $booking->tourDeparture;

        if (!$departure || !$departure->date) {
            return 0.0;
        }

        $departureDateTime = Carbon::parse($departure->date->format('Y-m-d') . ' ' . $departure->time);
        $hoursUntilDeparture = now()->diffInHours($departureDateTime, false);

        // If less than 24 hours until departure, apply 100% penalty
        if ($hoursUntilDeparture < 24) {
            return (float) $booking->total_amount;
        }

        // Free cancellation if more than 48 hours OR in the 24-48h grace period
        return 0.0;
    }

    /**
     * Check if free cancellation is available for the booking.
     */
    public function isFreeCancellation(Booking $booking): bool
    {
        $departure = $booking->tourDeparture;

        if (!$departure || !$departure->date) {
            return true;
        }

        $departureDateTime = Carbon::parse($departure->date->format('Y-m-d') . ' ' . $departure->time);
        $hoursUntilDeparture = now()->diffInHours($departureDateTime, false);

        // Free cancellation if more than 48 hours before departure
        return $hoursUntilDeparture >= 48;
    }

    /**
     * Check if cancellation will incur a penalty.
     */
    public function willHavePenalty(Booking $booking): bool
    {
        $departure = $booking->tourDeparture;

        if (!$departure || !$departure->date) {
            return false;
        }

        $departureDateTime = Carbon::parse($departure->date->format('Y-m-d') . ' ' . $departure->time);
        $hoursUntilDeparture = now()->diffInHours($departureDateTime, false);

        // 100% penalty if less than 24 hours before departure
        return $hoursUntilDeparture < 24;
    }
}
