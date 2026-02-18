<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Tour departure status enum.
 *
 * Defines the status of a scheduled tour departure.
 */
enum TourDepartureStatus: string
{
    /**
     * Departure is open for bookings.
     */
    case OPEN = 'open';

    /**
     * Departure is closed (no more bookings accepted).
     */
    case CLOSED = 'closed';

    /**
     * Departure has been cancelled.
     */
    case CANCELLED = 'cancelled';

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
            self::CANCELLED => 'Cancelled',
        };
    }

    /**
     * Get the badge color variant for the status.
     */
    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'success',
            self::CLOSED => 'warning',
            self::CANCELLED => 'danger',
        };
    }

    /**
     * Check if bookings can be made.
     */
    public function acceptsBookings(): bool
    {
        return $this === self::OPEN;
    }
}
