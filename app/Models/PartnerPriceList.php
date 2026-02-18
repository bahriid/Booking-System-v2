<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaxType;
use App\Enums\Season;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PartnerPriceList model representing partner-specific pricing.
 *
 * @property int $id
 * @property int $partner_id
 * @property int $tour_id
 * @property Season $season
 * @property PaxType $pax_type
 * @property float $price
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
final class PartnerPriceList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'tour_id',
        'season',
        'pax_type',
        'price',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'season' => Season::class,
            'pax_type' => PaxType::class,
            'price' => 'float',
        ];
    }

    /**
     * Get the partner for this price list entry.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the tour for this price list entry.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Scope to filter by partner and tour.
     */
    public function scopeForPartnerTour($query, int $partnerId, int $tourId)
    {
        return $query->where('partner_id', $partnerId)->where('tour_id', $tourId);
    }

    /**
     * Get the price for a specific combination.
     */
    public static function getPriceFor(
        int $partnerId,
        int $tourId,
        Season $season,
        PaxType $paxType
    ): ?float {
        $priceList = self::where('partner_id', $partnerId)
            ->where('tour_id', $tourId)
            ->where('season', $season)
            ->where('pax_type', $paxType)
            ->first();

        return $priceList?->price;
    }
}
