<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Setting model for key-value application settings.
 *
 * @property int $id
 * @property string $group
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
final class Setting extends Model
{
    private const CACHE_KEY = 'app_settings';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::getAllCached();

        if (!isset($settings[$key])) {
            return $default;
        }

        return self::castValue($settings[$key]['value'], $settings[$key]['type']);
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value): void
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => self::serializeValue($value, $setting->type)]);
        }

        self::clearCache();
    }

    /**
     * Update multiple settings at once.
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            self::set($key, $value);
        }
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        $settings = self::getAllCached();
        $result = [];

        foreach ($settings as $key => $data) {
            if ($data['group'] === $group) {
                $result[$key] = self::castValue($data['value'], $data['type']);
            }
        }

        return $result;
    }

    /**
     * Get all settings as a cached array.
     */
    private static function getAllCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::all()
                ->keyBy('key')
                ->map(fn ($s) => [
                    'group' => $s->group,
                    'value' => $s->value,
                    'type' => $s->type,
                ])
                ->toArray();
        });
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Cast a string value to the appropriate type.
     */
    private static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer' => (int) $value,
            'boolean' => (bool) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Serialize a value for storage.
     */
    private static function serializeValue(mixed $value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Get available timezones for display.
     */
    public static function getTimezones(): array
    {
        return [
            'Europe/Rome' => 'Europe/Rome (GMT+1)',
            'Europe/London' => 'Europe/London (GMT)',
            'America/New_York' => 'America/New York (GMT-5)',
            'America/Los_Angeles' => 'America/Los Angeles (GMT-8)',
            'Asia/Tokyo' => 'Asia/Tokyo (GMT+9)',
        ];
    }

    /**
     * Get available currencies for display.
     */
    public static function getCurrencies(): array
    {
        return [
            'EUR' => 'Euro (€)',
            'USD' => 'US Dollar ($)',
            'GBP' => 'British Pound (£)',
        ];
    }

    /**
     * Get available date formats for display.
     */
    public static function getDateFormats(): array
    {
        return [
            'd/m/Y' => 'DD/MM/YYYY (27/12/2025)',
            'm/d/Y' => 'MM/DD/YYYY (12/27/2025)',
            'Y-m-d' => 'YYYY-MM-DD (2025-12-27)',
        ];
    }

    /**
     * Get available languages for display.
     */
    public static function getLanguages(): array
    {
        return [
            'en' => 'English',
            'it' => 'Italiano',
        ];
    }
}
