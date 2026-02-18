<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Booking status enum.
 *
 * Represents all possible states a booking can be in throughout its lifecycle.
 */
enum BookingStatus: string
{
    /**
     * Booking is confirmed and seats are reserved.
     */
    case CONFIRMED = 'confirmed';

    /**
     * Overbooking request, awaiting admin approval (2h limit).
     */
    case SUSPENDED_REQUEST = 'suspended_request';

    /**
     * Overbooking request was rejected by admin.
     */
    case REJECTED = 'rejected';

    /**
     * Overbooking request expired (no admin action within 2h).
     */
    case EXPIRED = 'expired';

    /**
     * Booking was cancelled.
     */
    case CANCELLED = 'cancelled';

    /**
     * Tour has been completed.
     */
    case COMPLETED = 'completed';

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::CONFIRMED => 'Confirmed',
            self::SUSPENDED_REQUEST => 'Pending Approval',
            self::REJECTED => 'Rejected',
            self::EXPIRED => 'Expired',
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
        };
    }

    /**
     * Get the badge color variant for the status.
     */
    public function color(): string
    {
        return match ($this) {
            self::CONFIRMED => 'success',
            self::SUSPENDED_REQUEST => 'warning',
            self::REJECTED => 'danger',
            self::EXPIRED => 'secondary',
            self::CANCELLED => 'danger',
            self::COMPLETED => 'secondary',
        };
    }

    /**
     * Check if the booking can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this, [
            self::CONFIRMED,
            self::SUSPENDED_REQUEST,
        ], true);
    }

    /**
     * Check if the booking is pending (requires admin action).
     */
    public function isPending(): bool
    {
        return $this === self::SUSPENDED_REQUEST;
    }

    /**
     * Check if the booking is in a final state.
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::REJECTED,
            self::EXPIRED,
            self::CANCELLED,
            self::COMPLETED,
        ], true);
    }

    /**
     * Check if the booking can be modified.
     */
    public function canBeModified(): bool
    {
        return in_array($this, [
            self::CONFIRMED,
            self::SUSPENDED_REQUEST,
        ], true);
    }
}
