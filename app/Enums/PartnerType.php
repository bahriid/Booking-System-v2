<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Partner type enum.
 *
 * Defines the categories of B2B partners.
 */
enum PartnerType: string
{
    /**
     * Hotel, B&B, or reception desk.
     */
    case HOTEL = 'hotel';

    /**
     * Tour operator.
     */
    case TOUR_OPERATOR = 'tour_operator';

    /**
     * Get the human-readable label for the type.
     */
    public function label(): string
    {
        return match ($this) {
            self::HOTEL => 'Hotel / B&B / Reception',
            self::TOUR_OPERATOR => 'Tour Operator',
        };
    }

    /**
     * Get the short label for display.
     */
    public function shortLabel(): string
    {
        return match ($this) {
            self::HOTEL => 'Hotel',
            self::TOUR_OPERATOR => 'Tour Operator',
        };
    }

    /**
     * Get the badge color variant for the type.
     */
    public function color(): string
    {
        return match ($this) {
            self::HOTEL => 'primary',
            self::TOUR_OPERATOR => 'info',
        };
    }
}
