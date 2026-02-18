<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Payment status enum.
 *
 * Defines the payment state for bookings.
 */
enum PaymentStatus: string
{
    /**
     * No payment has been made.
     */
    case UNPAID = 'unpaid';

    /**
     * Partial payment received.
     */
    case PARTIAL = 'partial';

    /**
     * Fully paid.
     */
    case PAID = 'paid';

    /**
     * Refunded (full or partial).
     */
    case REFUNDED = 'refunded';

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Unpaid',
            self::PARTIAL => 'Partial',
            self::PAID => 'Paid',
            self::REFUNDED => 'Refunded',
        };
    }

    /**
     * Get the badge color variant for the status.
     */
    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'danger',
            self::PARTIAL => 'warning',
            self::PAID => 'success',
            self::REFUNDED => 'info',
        };
    }

    /**
     * Check if there's an outstanding balance.
     */
    public function hasOutstanding(): bool
    {
        return in_array($this, [self::UNPAID, self::PARTIAL], true);
    }
}
