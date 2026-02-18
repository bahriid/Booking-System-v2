<?php

declare(strict_types=1);

namespace App\View\Components\Ui;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Button component with Metronic styling.
 *
 * Supports different variants, sizes, and optional icons.
 */
final class Button extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $variant Color variant (primary, success, warning, danger, info, light)
     * @param string|null $size Size modifier (sm, lg) or null for default
     * @param string|null $icon Keenicon name to display before text
     * @param string $type Button type (button, submit, reset)
     * @param bool $light Whether to use light variant (e.g., btn-light-primary)
     * @param bool $iconOnly Whether button contains only an icon
     */
    public function __construct(
        public string $variant = 'primary',
        public ?string $size = null,
        public ?string $icon = null,
        public string $type = 'button',
        public bool $light = false,
        public bool $iconOnly = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.ui.button');
    }

    /**
     * Get the CSS classes for the button.
     */
    public function classes(): string
    {
        $variantClass = $this->light ? "btn-light-{$this->variant}" : "btn-{$this->variant}";
        $classes = "btn {$variantClass}";

        if ($this->size) {
            $classes .= " btn-{$this->size}";
        }

        if ($this->iconOnly) {
            $classes .= ' btn-icon';
        }

        return $classes;
    }
}
