<?php

declare(strict_types=1);

namespace App\View\Components\Booking;

use App\Enums\BookingStatus;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Booking status badge component.
 *
 * Displays a color-coded badge for booking status.
 */
final class StatusBadge extends Component
{
    /**
     * Create a new component instance.
     *
     * @param BookingStatus|string $status The booking status
     * @param bool $light Use light badge style
     */
    public function __construct(
        public BookingStatus|string $status,
        public bool $light = true,
    ) {
        // Convert string to enum if needed
        if (is_string($this->status)) {
            $this->status = BookingStatus::from($this->status);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.booking.status-badge');
    }

    /**
     * Get the badge color based on status.
     */
    public function color(): string
    {
        return $this->status->color();
    }

    /**
     * Get the status label.
     */
    public function label(): string
    {
        return $this->status->label();
    }
}
