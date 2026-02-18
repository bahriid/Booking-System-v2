<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Text input component with Metronic styling.
 *
 * Provides a styled text input with label, error handling, and optional icon.
 */
final class Input extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $name Input name attribute
     * @param string $label Label text
     * @param string $type Input type (text, email, password, number, tel, url)
     * @param string|null $value Input value
     * @param string|null $placeholder Placeholder text
     * @param string|null $icon Keenicon name for input addon
     * @param bool $required Whether the field is required
     * @param bool $disabled Whether the field is disabled
     * @param bool $readonly Whether the field is readonly
     * @param string|null $hint Help text below the input
     */
    public function __construct(
        public string $name,
        public string $label,
        public string $type = 'text',
        public ?string $value = null,
        public ?string $placeholder = null,
        public ?string $icon = null,
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
        return view('components.forms.input');
    }

    /**
     * Get the input ID based on name.
     */
    public function inputId(): string
    {
        return str_replace(['[', ']', '.'], ['_', '', '_'], $this->name);
    }
}
