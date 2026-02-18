<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Select dropdown component with Metronic styling.
 *
 * Provides a styled select input with label and error handling.
 */
final class Select extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $name Select name attribute
     * @param string $label Label text
     * @param array<string|int, string> $options Key-value pairs for options
     * @param string|int|null $selected Currently selected value
     * @param string|null $placeholder Empty option placeholder text
     * @param bool $required Whether the field is required
     * @param bool $disabled Whether the field is disabled
     * @param bool $multiple Allow multiple selections
     * @param string|null $hint Help text below the select
     */
    public function __construct(
        public string $name,
        public string $label,
        public array $options = [],
        public string|int|null $selected = null,
        public ?string $placeholder = null,
        public bool $required = false,
        public bool $disabled = false,
        public bool $multiple = false,
        public ?string $hint = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.forms.select');
    }

    /**
     * Get the select ID based on name.
     */
    public function selectId(): string
    {
        return str_replace(['[', ']', '.'], ['_', '', '_'], $this->name);
    }

    /**
     * Check if an option is selected.
     *
     * @param string|int $value The option value to check
     */
    public function isSelected(string|int $value): bool
    {
        $oldValue = old($this->name, $this->selected);

        if (is_array($oldValue)) {
            return in_array($value, $oldValue, false);
        }

        return (string) $oldValue === (string) $value;
    }
}
