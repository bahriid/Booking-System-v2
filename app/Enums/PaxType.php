<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Passenger type enum.
 *
 * Defines the passenger categories for pricing and counting.
 */
enum PaxType: string
{
    /**
     * Adult passenger.
     */
    case ADULT = 'adult';

    /**
     * Child passenger.
     */
    case CHILD = 'child';

    /**
     * Infant passenger (free of charge).
     */
    case INFANT = 'infant';

    /**
     * Get the human-readable label for the type.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADULT => 'Adult',
            self::CHILD => 'Child',
            self::INFANT => 'Infant',
        };
    }

    /**
     * Get the short code for display (e.g., in badges).
     */
    public function shortCode(): string
    {
        return match ($this) {
            self::ADULT => 'ADU',
            self::CHILD => 'CHD',
            self::INFANT => 'INF',
        };
    }

    /**
     * Get the badge color variant for the type.
     */
    public function color(): string
    {
        return match ($this) {
            self::ADULT => 'primary',
            self::CHILD => 'info',
            self::INFANT => 'secondary',
        };
    }

    /**
     * Check if this passenger type is chargeable.
     */
    public function isChargeable(): bool
    {
        return $this !== self::INFANT;
    }

    /**
     * Check if this passenger type counts towards capacity.
     */
    public function countsTowardsCapacity(): bool
    {
        return $this !== self::INFANT;
    }
}
