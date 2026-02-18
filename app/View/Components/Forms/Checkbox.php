<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Checkbox component with Metronic styling.
 *
 * Provides a styled checkbox with label.
 */
final class Checkbox extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $name Checkbox name attribute
     * @param string $label Label text displayed next to checkbox
     * @param string|int $value Value when checked
     * @param bool $checked Whether the checkbox is checked
     * @param bool $disabled Whether the checkbox is disabled
     * @param string|null $hint Help text below the checkbox
     */
    public function __construct(
        public string $name,
        public string $label,
        public string|int $value = '1',
        public bool $checked = false,
        public bool $disabled = false,
        public ?string $hint = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.forms.checkbox');
    }

    /**
     * Get the checkbox ID based on name.
     */
    public function checkboxId(): string
    {
        return str_replace(['[', ']', '.'], ['_', '', '_'], $this->name);
    }

    /**
     * Determine if the checkbox should be checked.
     */
    public function isChecked(): bool
    {
        $oldValue = old($this->name);

        if ($oldValue !== null) {
            return (bool) $oldValue;
        }

        return $this->checked;
    }
}
