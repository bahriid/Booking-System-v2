<?php

declare(strict_types=1);

namespace App\View\Components\Booking;

use App\Enums\PaymentStatus;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Payment status badge component.
 *
 * Displays a color-coded badge for payment status.
 */
final class PaymentBadge extends Component
{
    /**
     * Create a new component instance.
     *
     * @param PaymentStatus|string $status The payment status
     * @param bool $light Use light badge style
     */
    public function __construct(
        public PaymentStatus|string $status,
        public bool $light = true,
    ) {
        // Convert string to enum if needed
        if (is_string($this->status)) {
            $this->status = PaymentStatus::from($this->status);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.booking.payment-badge');
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
