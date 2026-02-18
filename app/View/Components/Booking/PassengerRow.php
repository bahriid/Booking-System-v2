<?php

declare(strict_types=1);

namespace App\View\Components\Booking;

use App\Enums\PaxType;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Passenger row component for booking forms.
 *
 * Displays an editable row for passenger details.
 */
final class PassengerRow extends Component
{
    /**
     * Create a new component instance.
     *
     * @param int $index Row index for form array naming
     * @param PaxType|string $type Passenger type (adult, child, infant)
     * @param string|null $name Passenger name
     * @param string|null $pickup Pickup location
     * @param bool $removable Whether this row can be removed
     */
    public function __construct(
        public int $index,
        public PaxType|string $type = PaxType::ADULT,
        public ?string $name = null,
        public ?string $pickup = null,
        public bool $removable = true,
    ) {
        // Convert string to enum if needed
        if (is_string($this->type)) {
            $this->type = PaxType::from($this->type);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.booking.passenger-row');
    }

    /**
     * Get the type badge color.
     */
    public function typeColor(): string
    {
        return match ($this->type) {
            PaxType::ADULT => 'primary',
            PaxType::CHILD => 'info',
            PaxType::INFANT => 'warning',
        };
    }
}
