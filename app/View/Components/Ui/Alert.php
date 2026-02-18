<?php

declare(strict_types=1);

namespace App\View\Components\Ui;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Alert/Notice component with Metronic styling.
 *
 * Displays an informational notice box with optional icon.
 */
final class Alert extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $variant Color variant (primary, success, warning, danger, info)
     * @param string|null $icon Keenicon name to display
     * @param string|null $title Optional bold title
     * @param bool $dashed Whether to use dashed border style
     */
    public function __construct(
        public string $variant = 'primary',
        public ?string $icon = null,
        public ?string $title = null,
        public bool $dashed = true,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.ui.alert');
    }

    /**
     * Get the default icon based on variant.
     */
    public function defaultIcon(): string
    {
        return match ($this->variant) {
            'success' => 'check-circle',
            'warning' => 'information-2',
            'danger' => 'information-2',
            default => 'information',
        };
    }
}
