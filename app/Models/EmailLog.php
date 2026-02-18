<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * EmailLog model for tracking sent emails.
 *
 * @property int $id
 * @property string $event_type
 * @property string $to_email
 * @property string|null $to_name
 * @property string $subject
 * @property int|null $booking_id
 * @property bool $success
 * @property string|null $error_message
 * @property \Carbon\Carbon $sent_at
 */
final class EmailLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'event_type',
        'to_email',
        'to_name',
        'subject',
        'booking_id',
        'success',
        'error_message',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Get the related booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope to filter successful emails.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope to filter failed emails.
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope to filter by event type.
     */
    public function scopeOfType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Log a sent email.
     */
    public static function logEmail(
        string $eventType,
        string $toEmail,
        string $subject,
        bool $success,
        ?string $toName = null,
        ?int $bookingId = null,
        ?string $errorMessage = null
    ): self {
        return self::create([
            'event_type' => $eventType,
            'to_email' => $toEmail,
            'to_name' => $toName,
            'subject' => $subject,
            'booking_id' => $bookingId,
            'success' => $success,
            'error_message' => $errorMessage,
        ]);
    }
}
