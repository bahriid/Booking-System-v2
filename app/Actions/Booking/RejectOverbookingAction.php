<?php

declare(strict_types=1);

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;

/**
 * Rejects an overbooking request.
 */
final class RejectOverbookingAction
{
    public function __construct(
        private readonly EmailService $emailService
    ) {}

    /**
     * Execute the overbooking rejection.
     *
     * @param Booking $booking The booking to reject
     * @param string|null $reason The rejection reason
     * @return Booking The updated booking
     */
    public function execute(Booking $booking, ?string $reason = null): Booking
    {
        if ($booking->status !== BookingStatus::SUSPENDED_REQUEST) {
            throw new \InvalidArgumentException("Only pending overbooking requests can be rejected.");
        }

        return DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status' => BookingStatus::REJECTED,
                'suspended_until' => null,
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            $booking = $booking->fresh();

            // Send rejection notification to partner
            $this->emailService->sendOverbookingRejected($booking, $reason);

            return $booking;
        });
    }
}
