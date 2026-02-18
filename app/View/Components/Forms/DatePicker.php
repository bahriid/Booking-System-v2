<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Date picker component with Metronic/Flatpickr styling.
 *
 * Provides a styled date input with calendar picker.
 */
final class DatePicker extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $name Input name attribute
     * @param string $label Label text
     * @param string|null $value Date value (Y-m-d format)
     * @param string|null $placeholder Placeholder text
     * @param string|null $minDate Minimum selectable date
     * @param string|null $maxDate Maximum selectable date
     * @param bool $required Whether the field is required
     * @param bool $disabled Whether the field is disabled
     * @param bool $enableTime Whether to include time selection
     * @param string $format Display format for the date
     * @param string|null $hint Help text below the input
     */
    public function __construct(
        public string $name,
        public string $label,
        public ?string $value = null,
        public ?string $placeholder = null,
        public ?string $minDate = null,
        public ?string $maxDate = null,
        public bool $required = false,
        public bool $disabled = false,
        public bool $enableTime = false,
        public string $format = 'd/m/Y',
        public ?string $hint = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.forms.date-picker');
    }

    /**
     * Get the input ID based on name.
     */
    public function inputId(): string
    {
        return str_replace(['[', ']', '.'], ['_', '', '_'], $this->name);
    }

    /**
     * Get the Flatpickr options as JSON.
     */
    public function flatpickrOptions(): string
    {
        $options = [
            'dateFormat' => $this->format,
            'enableTime' => $this->enableTime,
        ];

        if ($this->minDate) {
            $options['minDate'] = $this->minDate;
        }

        if ($this->maxDate) {
            $options['maxDate'] = $this->maxDate;
        }

        return json_encode($options);
    }
}
