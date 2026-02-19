<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Payment model representing partner payments.
 *
 * @property int $id
 * @property int $partner_id
 * @property int|null $created_by
 * @property float $amount
 * @property string|null $method
 * @property string|null $reference
 * @property string|null $notes
 * @property \Carbon\Carbon $paid_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
final class Payment extends Model
{
    use Auditable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'created_by',
        'amount',
        'method',
        'reference',
        'notes',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'date:Y-m-d',
        ];
    }

    /**
     * Get the partner for this payment.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the user who created this payment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the bookings this payment is applied to.
     */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class)
            ->withPivot('amount')
            ->withTimestamps();
    }

    /**
     * Scope to filter by partner.
     */
    public function scopeForPartner($query, int $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('paid_at', [$startDate, $endDate]);
    }

    /**
     * Get the allocated amount (sum of amounts applied to bookings).
     */
    public function getAllocatedAmountAttribute(): float
    {
        return (float) $this->bookings()->sum('booking_payment.amount');
    }

    /**
     * Get the unallocated amount.
     */
    public function getUnallocatedAmountAttribute(): float
    {
        return (float) ($this->amount - $this->allocated_amount);
    }
}
