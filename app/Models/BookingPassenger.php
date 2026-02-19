<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaxType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BookingPassenger model representing individual passengers on a booking.
 *
 * @property int $id
 * @property int $booking_id
 * @property int|null $pickup_point_id
 * @property string $first_name
 * @property string $last_name
 * @property PaxType $pax_type
 * @property string|null $phone
 * @property string|null $allergies
 * @property string|null $notes
 * @property float $price
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
final class BookingPassenger extends Model
{
    use HasFactory;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['full_name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_id',
        'pickup_point_id',
        'first_name',
        'last_name',
        'pax_type',
        'phone',
        'allergies',
        'notes',
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
            'pax_type' => PaxType::class,
            'price' => 'decimal:2',
        ];
    }

    /**
     * Get the booking for this passenger.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the pickup point for this passenger.
     */
    public function pickupPoint(): BelongsTo
    {
        return $this->belongsTo(PickupPoint::class);
    }

    /**
     * Get the full name of the passenger.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Scope to filter by pax type.
     */
    public function scopeOfType($query, PaxType $type)
    {
        return $query->where('pax_type', $type);
    }

    /**
     * Scope to filter passengers with allergies.
     */
    public function scopeWithAllergies($query)
    {
        return $query->whereNotNull('allergies')->where('allergies', '!=', '');
    }
}
