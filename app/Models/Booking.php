<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaxType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Booking model representing a tour booking.
 *
 * @property int $id
 * @property string $booking_code
 * @property int $partner_id
 * @property int $tour_departure_id
 * @property int|null $created_by
 * @property BookingStatus $status
 * @property float $total_amount
 * @property float $penalty_amount
 * @property PaymentStatus $payment_status
 * @property \Carbon\Carbon|null $suspended_until
 * @property string|null $notes
 * @property string|null $cancellation_reason
 * @property \Carbon\Carbon|null $cancelled_at
 * @property \Carbon\Carbon|null $expired_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
final class Booking extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * Get a display label for audit logs.
     */
    public function getAuditLabel(): string
    {
        return $this->booking_code;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_code',
        'partner_id',
        'tour_departure_id',
        'created_by',
        'status',
        'total_amount',
        'penalty_amount',
        'payment_status',
        'suspended_until',
        'notes',
        'cancellation_reason',
        'cancelled_at',
        'expired_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'payment_status' => PaymentStatus::class,
            'total_amount' => 'decimal:2',
            'penalty_amount' => 'decimal:2',
            'suspended_until' => 'datetime',
            'cancelled_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /**
     * Get the partner for this booking.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the tour departure for this booking.
     */
    public function tourDeparture(): BelongsTo
    {
        return $this->belongsTo(TourDeparture::class);
    }

    /**
     * Get the user who created this booking.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the passengers for this booking.
     */
    public function passengers(): HasMany
    {
        return $this->hasMany(BookingPassenger::class);
    }

    /**
     * Get the payments for this booking.
     */
    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(Payment::class)
            ->withPivot('amount')
            ->withTimestamps();
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus($query, BookingStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', BookingStatus::CONFIRMED);
    }

    /**
     * Scope to filter by partner.
     */
    public function scopeForPartner($query, int $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    /**
     * Get the count of adults in this booking.
     */
    public function getAdultsCountAttribute(): int
    {
        return $this->passengers()->where('pax_type', PaxType::ADULT)->count();
    }

    /**
     * Get the count of children in this booking.
     */
    public function getChildrenCountAttribute(): int
    {
        return $this->passengers()->where('pax_type', PaxType::CHILD)->count();
    }

    /**
     * Get the count of infants in this booking.
     */
    public function getInfantsCountAttribute(): int
    {
        return $this->passengers()->where('pax_type', PaxType::INFANT)->count();
    }

    /**
     * Get the total passenger count.
     */
    public function getTotalPassengersAttribute(): int
    {
        return $this->passengers()->count();
    }

    /**
     * Get the formatted pax summary (e.g., "2 ADU + 1 CHD").
     */
    public function getPaxSummaryAttribute(): string
    {
        $parts = [];

        $adults = $this->adults_count;
        $children = $this->children_count;
        $infants = $this->infants_count;

        if ($adults > 0) {
            $parts[] = "{$adults} ADU";
        }
        if ($children > 0) {
            $parts[] = "{$children} CHD";
        }
        if ($infants > 0) {
            $parts[] = "{$infants} INF";
        }

        return implode(' + ', $parts) ?: '0 PAX';
    }

    /**
     * Get the amount paid for this booking.
     */
    public function getAmountPaidAttribute(): float
    {
        return (float) $this->payments()->sum('booking_payment.amount');
    }

    /**
     * Get the outstanding balance for this booking.
     */
    public function getBalanceDueAttribute(): float
    {
        return (float) ($this->total_amount - $this->amount_paid);
    }

    /**
     * Check if the booking is pending approval (overbooking).
     */
    public function isPendingApproval(): bool
    {
        return $this->status === BookingStatus::SUSPENDED_REQUEST;
    }

    /**
     * Check if the overbooking request has expired.
     */
    public function isOverbookingExpired(): bool
    {
        if (!$this->isPendingApproval() || !$this->suspended_until) {
            return false;
        }

        return now()->greaterThan($this->suspended_until);
    }

    /**
     * Generate a new booking code.
     */
    public static function generateBookingCode(TourDeparture $departure): string
    {
        $tourCode = $departure->tour?->code ?? 'UNK';
        $dateFormatted = $departure->date->format('dmY');

        // Get the next sequential number for this tour on this date (across all departures)
        $count = self::whereHas('tourDeparture', function ($query) use ($departure) {
            $query->where('tour_id', $departure->tour_id)
                ->whereDate('date', $departure->date);
        })
            ->withTrashed()
            ->count();

        $sequenceNumber = str_pad((string) ($count + 1), 2, '0', STR_PAD_LEFT);

        return "{$tourCode}-{$sequenceNumber}-{$dateFormatted}";
    }
}
