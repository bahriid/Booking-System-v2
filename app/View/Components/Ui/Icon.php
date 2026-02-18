<?php

declare(strict_types=1);

namespace App\View\Components\Ui;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Keenicons icon component.
 *
 * Renders Metronic's duotone icons with the correct path spans.
 */
final class Icon extends Component
{
    /**
     * Icon to path count mapping.
     *
     * @var array<string, int>
     */
    private const ICON_PATHS = [
        'element-11' => 4,
        'map' => 3,
        'calendar-8' => 6,
        'document' => 2,
        'people' => 5,
        'plus' => 3,
        'plus-circle' => 2,
        'eye' => 3,
        'pencil' => 2,
        'check' => 2,
        'check-circle' => 2,
        'cross' => 2,
        'cross-circle' => 2,
        'filter' => 2,
        'magnifier' => 2,
        'dots-square' => 4,
        'building' => 3,
        'user' => 2,
        'notification-status' => 4,
        'wallet' => 4,
        'setting-2' => 2,
        'printer' => 5,
        'arrow-left' => 2,
        'arrow-right' => 2,
        'information' => 3,
        'information-2' => 3,
        'information-3' => 3,
        'time' => 2,
        'timer' => 3,
        'sms' => 2,
        'phone' => 2,
        'bill' => 6,
        'dollar' => 3,
        'file-down' => 2,
        'cloud-download' => 2,
        'trash' => 5,
        'lock' => 3,
        'flag' => 2,
        'geolocation' => 3,
        'arrow-mix' => 2,
        'chart-simple' => 4,
        'shield-cross' => 3,
        'home' => 2,
        'abstract-14' => 2,
    ];

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $class = 'fs-2',
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.ui.icon');
    }

    /**
     * Get the number of path spans needed for this icon.
     */
    public function pathCount(): int
    {
        return self::ICON_PATHS[$this->name] ?? 2;
    }
}
