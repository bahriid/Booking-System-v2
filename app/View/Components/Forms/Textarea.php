<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Textarea component with Metronic styling.
 *
 * Provides a styled textarea with label and error handling.
 */
final class Textarea extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $name Textarea name attribute
     * @param string $label Label text
     * @param string|null $value Textarea content
     * @param string|null $placeholder Placeholder text
     * @param int $rows Number of visible rows
     * @param bool $required Whether the field is required
     * @param bool $disabled Whether the field is disabled
     * @param bool $readonly Whether the field is readonly
     * @param string|null $hint Help text below the textarea
     */
    public function __construct(
        public string $name,
        public string $label = '',
        public ?string $value = null,
        public ?string $placeholder = null,
        public int $rows = 4,
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
        return view('components.forms.textarea');
    }

    /**
     * Get the textarea ID based on name.
     */
    public function textareaId(): string
    {
        return str_replace(['[', ']', '.'], ['_', '', '_'], $this->name);
    }
}
