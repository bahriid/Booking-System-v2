<?php

declare(strict_types=1);

namespace App\View\Components\Ui;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Modal component with Metronic styling.
 *
 * Provides a Bootstrap modal with consistent styling.
 */
final class Modal extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $id Modal ID for targeting
     * @param string $title Modal header title
     * @param string $size Modal size (sm, lg, xl) or empty for default
     * @param bool $centered Whether to vertically center the modal
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $size = '',
        public bool $centered = true,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.ui.modal');
    }

    /**
     * Get the dialog CSS classes.
     */
    public function dialogClasses(): string
    {
        $classes = 'modal-dialog';

        if ($this->centered) {
            $classes .= ' modal-dialog-centered';
        }

        if ($this->size) {
            $classes .= " modal-{$this->size}";
        }

        return $classes;
    }
}
