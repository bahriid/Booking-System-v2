<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaxType;
use App\Enums\Season;
use App\Enums\TourDepartureStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TourDeparture model representing a specific tour departure instance.
 *
 * @property int $id
 * @property int $tour_id
 * @property int|null $driver_id
 * @property \Carbon\Carbon $date
 * @property string $time
 * @property int $capacity
 * @property TourDepartureStatus $status
 * @property Season $season
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
final class TourDeparture extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tour_id',
        'driver_id',
        'date',
        'time',
        'capacity',
        'status',
        'season',
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
            'date' => 'date',
            'capacity' => 'integer',
            'status' => TourDepartureStatus::class,
            'season' => Season::class,
        ];
    }

    /**
     * Get the tour for this departure.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get the driver assigned to this departure.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the bookings for this departure.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope to filter departures assigned to a specific driver.
     */
    public function scopeForDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * Scope to filter open departures.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', TourDepartureStatus::OPEN);
    }

    /**
     * Scope to filter future departures.
     */
    public function scopeFuture($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Get the count of booked seats (confirmed bookings only, excluding infants).
     */
    public function getBookedSeatsAttribute(): int
    {
        return $this->bookings()
            ->whereIn('status', [
                BookingStatus::CONFIRMED->value,
                BookingStatus::COMPLETED->value,
            ])
            ->withCount(['passengers' => function ($query) {
                $query->where('pax_type', '!=', PaxType::INFANT);
            }])
            ->get()
            ->sum('passengers_count');
    }

    /**
     * Get the count of remaining available seats.
     */
    public function getRemainingSeatsAttribute(): int
    {
        return max(0, $this->capacity - $this->booked_seats);
    }

    /**
     * Check if the departure has available capacity.
     */
    public function hasAvailability(int $requestedSeats = 1): bool
    {
        return $this->remaining_seats >= $requestedSeats;
    }

    /**
     * Check if the departure is past cut-off time.
     */
    public function isPastCutoff(): bool
    {
        $cutoffHours = $this->tour?->cutoff_hours ?? 24;
        $departureDateTime = $this->date->copy()->setTimeFromTimeString($this->time);

        return now()->addHours($cutoffHours)->greaterThan($departureDateTime);
    }

    /**
     * Get the full datetime of departure.
     */
    public function getDepartureDateTimeAttribute(): \Carbon\Carbon
    {
        return $this->date->copy()->setTimeFromTimeString($this->time);
    }
}
