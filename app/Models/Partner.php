<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PartnerType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Partner model representing B2B partners (Hotels, Tour Operators).
 *
 * @property int $id
 * @property string $name
 * @property PartnerType $type
 * @property string $email
 * @property string|null $phone
 * @property string|null $vat_number
 * @property string|null $sdi_pec
 * @property string|null $address
 * @property bool $is_active
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
final class Partner extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = ['initials', 'outstanding_balance'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'type',
        'email',
        'phone',
        'vat_number',
        'sdi_pec',
        'address',
        'is_active',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => PartnerType::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the users associated with this partner.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the bookings for this partner.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the price lists for this partner.
     */
    public function priceLists(): HasMany
    {
        return $this->hasMany(PartnerPriceList::class);
    }

    /**
     * Get the payments for this partner.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the passengers through bookings.
     */
    public function passengers(): HasManyThrough
    {
        return $this->hasManyThrough(BookingPassenger::class, Booking::class);
    }

    /**
     * Scope to filter active partners.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by partner type.
     */
    public function scopeOfType($query, PartnerType $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the partner's initials.
     */
    public function getInitialsAttribute(): string
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
     * Calculate the total outstanding balance for this partner.
     * Includes active booking amounts + penalties from cancelled bookings.
     */
    public function getOutstandingBalanceAttribute(): float
    {
        $totalBookings = $this->bookings()
            ->whereNotIn('status', ['cancelled', 'expired', 'rejected'])
            ->sum('total_amount');

        $totalPenalties = $this->bookings()
            ->where('status', 'cancelled')
            ->where('penalty_amount', '>', 0)
            ->sum('penalty_amount');

        $totalPayments = $this->payments()->sum('amount');

        return (float) ($totalBookings + $totalPenalties - $totalPayments);
    }
}
