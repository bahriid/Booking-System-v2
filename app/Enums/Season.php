<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Season enum for pricing.
 *
 * Defines the seasonal pricing tiers.
 */
enum Season: string
{
    /**
     * Mid season (April-May, September-October).
     */
    case MID = 'mid';

    /**
     * High season (June-August).
     */
    case HIGH = 'high';

    /**
     * Get the human-readable label for the season.
     */
    public function label(): string
    {
        return match ($this) {
            self::MID => 'Mid Season',
            self::HIGH => 'High Season',
        };
    }

    /**
     * Get the months description for the season.
     */
    public function months(): string
    {
        return match ($this) {
            self::MID => 'April-May, September-October',
            self::HIGH => 'June-August',
        };
    }

    /**
     * Get the badge color variant for the season.
     */
    public function color(): string
    {
        return match ($this) {
            self::MID => 'info',
            self::HIGH => 'warning',
        };
    }

    /**
     * Determine the season for a given month.
     */
    public static function fromMonth(int $month): self
    {
        return match ($month) {
            6, 7, 8 => self::HIGH,
            default => self::MID,
        };
    }
}
