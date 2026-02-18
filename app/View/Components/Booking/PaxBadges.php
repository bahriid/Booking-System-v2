<?php

declare(strict_types=1);

namespace App\View\Components\Booking;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Passenger count badges component.
 *
 * Displays compact badges showing adult, child, and infant counts.
 */
final class PaxBadges extends Component
{
    /**
     * Create a new component instance.
     *
     * @param int $adults Number of adult passengers
     * @param int $children Number of child passengers
     * @param int $infants Number of infant passengers
     * @param bool $compact Use compact display style
     */
    public function __construct(
        public int $adults = 0,
        public int $children = 0,
        public int $infants = 0,
        public bool $compact = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.booking.pax-badges');
    }

    /**
     * Get total passenger count.
     */
    public function total(): int
    {
        return $this->adults + $this->children + $this->infants;
    }

    /**
     * Check if there are any passengers.
     */
    public function hasPassengers(): bool
    {
        return $this->total() > 0;
    }
}
