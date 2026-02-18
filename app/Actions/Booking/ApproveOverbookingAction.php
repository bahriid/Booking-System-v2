<?php

declare(strict_types=1);

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;

/**
 * Approves an overbooking request.
 */
final class ApproveOverbookingAction
{
    public function __construct(
        private readonly EmailService $emailService
    ) {}

    /**
     * Execute the overbooking approval.
     *
     * @param Booking $booking The booking to approve
     * @return Booking The updated booking
     */
    public function execute(Booking $booking): Booking
    {
        if ($booking->status !== BookingStatus::SUSPENDED_REQUEST) {
            throw new \InvalidArgumentException("Only pending overbooking requests can be approved.");
        }

        if ($booking->isOverbookingExpired()) {
            throw new \InvalidArgumentException("This overbooking request has expired.");
        }

        return DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => BookingStatus::CONFIRMED,
                'suspended_until' => null,
            ]);

            $booking = $booking->fresh();

            // Send approval notification to partner
            $this->emailService->sendOverbookingApproved($booking);

            // Send voucher ready email
            $this->emailService->sendVoucherReady($booking);

            return $booking;
        });
    }
}
