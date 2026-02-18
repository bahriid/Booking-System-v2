<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Price/currency input component with Metronic styling.
 *
 * Provides a styled number input with currency symbol.
 */
final class PriceInput extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $name Input name attribute
     * @param string $label Label text
     * @param float|int|string|null $value Input value
     * @param string $currency Currency symbol to display
     * @param string|null $placeholder Placeholder text
     * @param float|null $min Minimum value
     * @param float|null $max Maximum value
     * @param string $step Step increment
     * @param bool $required Whether the field is required
     * @param bool $disabled Whether the field is disabled
     * @param bool $readonly Whether the field is readonly
     * @param string|null $hint Help text below the input
     */
    public function __construct(
        public string $name,
        public string $label,
        public float|int|string|null $value = null,
        public string $currency = '€',
        public ?string $placeholder = null,
        public ?float $min = null,
        public ?float $max = null,
        public string $step = '0.01',
        public bool $required = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public ?string $hint = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.forms.price-input');
    }

    /**
     * Get the input ID based on name.
     */
    public function inputId(): string
    {
        return str_replace(['[', ']', '.'], ['_', '', '_'], $this->name);
    }
}
