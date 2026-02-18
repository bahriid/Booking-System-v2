<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Toggle switch component with Metronic styling.
 *
 * Provides a styled toggle switch for boolean options.
 */
final class Toggle extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $name Toggle name attribute
     * @param string $label Label text displayed next to toggle
     * @param bool $checked Whether the toggle is on
     * @param bool $disabled Whether the toggle is disabled
     * @param string $size Toggle size (sm, lg, or empty for default)
     * @param string|null $hint Help text below the toggle
     */
    public function __construct(
        public string $name,
        public string $label,
        public bool $checked = false,
        public bool $disabled = false,
        public string $size = '',
        public ?string $hint = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.forms.toggle');
    }

    /**
     * Get the toggle ID based on name.
     */
    public function toggleId(): string
    {
        return str_replace(['[', ']', '.'], ['_', '', '_'], $this->name);
    }

    /**
     * Determine if the toggle should be checked.
     */
    public function isChecked(): bool
    {
        $oldValue = old($this->name);

        if ($oldValue !== null) {
            return (bool) $oldValue;
        }

        return $this->checked;
    }

    /**
     * Get the switch CSS classes.
     */
    public function switchClasses(): string
    {
        $classes = 'form-check form-switch form-check-custom form-check-solid';

        if ($this->size) {
            $classes .= " form-switch-{$this->size}";
        }

        return $classes;
    }
}
