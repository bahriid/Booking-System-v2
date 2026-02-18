<?php

declare(strict_types=1);

namespace App\View\Components\Ui;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Badge component with Metronic styling.
 *
 * Renders light-colored badges with consistent styling.
 */
final class Badge extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $variant Color variant (primary, success, warning, danger, info, secondary)
     * @param string|null $size Size modifier (sm, lg) or null for default
     */
    public function __construct(
        public string $variant = 'primary',
        public ?string $size = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.ui.badge');
    }

    /**
     * Get the CSS classes for the badge.
     */
    public function classes(): string
    {
        $classes = "badge badge-light-{$this->variant}";

        if ($this->size) {
            $classes .= " badge-{$this->size}";
        }

        return $classes;
    }
}
