<?php

declare(strict_types=1);

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Daily booking recap email sent to admin summarizing the day's bookings.
 */
final class DailyBookingRecapMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  Carbon  $date  The date being summarized
     * @param  Collection  $bookings  All bookings created on this date
     * @param  array<string, mixed>  $stats  Aggregated statistics
     */
    public function __construct(
        public readonly Carbon $date,
        public readonly Collection $bookings,
        public readonly array $stats
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.daily_recap_subject', ['date' => $this->date->format('d/m/Y')]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-booking-recap',
            with: [
                'date' => $this->date,
                'bookings' => $this->bookings,
                'stats' => $this->stats,
            ],
        );
    }
}
