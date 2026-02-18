<?php

declare(strict_types=1);

namespace App\View\Components\Ui;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Card component with Metronic styling.
 *
 * Provides a consistent card layout with optional header and toolbar.
 */
final class Card extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string|null $title Card title displayed in header
     * @param string|null $icon Keenicon name to display before title
     * @param bool $flush Whether to remove body padding
     */
    public function __construct(
        public ?string $title = null,
        public ?string $icon = null,
        public bool $flush = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.ui.card');
    }
}
