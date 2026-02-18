<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Tour model representing tour products in the catalog.
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property \Carbon\Carbon $seasonality_start
 * @property \Carbon\Carbon $seasonality_end
 * @property int $cutoff_hours
 * @property int $default_capacity
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
final class Tour extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'seasonality_start',
        'seasonality_end',
        'cutoff_hours',
        'default_capacity',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'seasonality_start' => 'date',
            'seasonality_end' => 'date',
            'cutoff_hours' => 'integer',
            'default_capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the departures for this tour.
     */
    public function departures(): HasMany
    {
        return $this->hasMany(TourDeparture::class);
    }

    /**
     * Get the price lists for this tour.
     */
    public function priceLists(): HasMany
    {
        return $this->hasMany(PartnerPriceList::class);
    }

    /**
     * Scope to filter active tours.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if a given date is within the tour's season.
     *
     * @param \Carbon\Carbon|string $date The date to check
     */
    public function isInSeason(\Carbon\Carbon|string $date): bool
    {
        $checkDate = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);

        // Normalize to same year for comparison (use month-day only)
        $checkMd = (int) $checkDate->format('nd'); // e.g., 415 for Apr 15
        $startMd = (int) $this->seasonality_start->format('nd');
        $endMd = (int) $this->seasonality_end->format('nd');

        if ($startMd <= $endMd) {
            return $checkMd >= $startMd && $checkMd <= $endMd;
        }

        // Handles wrap-around (e.g., Nov-Feb)
        return $checkMd >= $startMd || $checkMd <= $endMd;
    }

    /**
     * Get the seasonality range as a formatted string.
     */
    public function getSeasonalityRangeAttribute(): string
    {
        return $this->seasonality_start->format('d M Y') . ' - ' . $this->seasonality_end->format('d M Y');
    }
}
