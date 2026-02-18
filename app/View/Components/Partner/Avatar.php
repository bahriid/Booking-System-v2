<?php

declare(strict_types=1);

namespace App\View\Components\Partner;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Partner avatar component.
 *
 * Displays a partner's avatar with initials fallback.
 */
final class Avatar extends Component
{
    /**
     * Create a new component instance.
     *
     * @param string $name Partner name for initials
     * @param string|null $image Image URL or path
     * @param string $size Avatar size (sm, md, lg, xl)
     * @param bool $showName Whether to display name next to avatar
     */
    public function __construct(
        public string $name,
        public ?string $image = null,
        public string $size = 'md',
        public bool $showName = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.partner.avatar');
    }

    /**
     * Get initials from the partner name.
     */
    public function initials(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            if (strlen($word) > 0) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }
        }

        return $initials ?: '?';
    }

    /**
     * Get the avatar size in pixels.
     */
    public function sizePixels(): int
    {
        return match ($this->size) {
            'sm' => 30,
            'md' => 40,
            'lg' => 50,
            'xl' => 65,
            default => 40,
        };
    }

    /**
     * Get the avatar CSS class.
     */
    public function sizeClass(): string
    {
        return match ($this->size) {
            'sm' => 'symbol-30px',
            'md' => 'symbol-40px',
            'lg' => 'symbol-50px',
            'xl' => 'symbol-65px',
            default => 'symbol-40px',
        };
    }
}
