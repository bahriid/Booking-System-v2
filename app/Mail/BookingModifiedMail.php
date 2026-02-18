<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent when a booking is modified.
 */
final class BookingModifiedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param Booking $booking The modified booking
     * @param array $changes The changes made to the booking
     */
    public function __construct(
        public readonly Booking $booking,
        public readonly array $changes = []
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Booking Modified - {$this->booking->booking_code}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-modified',
            with: [
                'booking' => $this->booking,
                'departure' => $this->booking->tourDeparture,
                'tour' => $this->booking->tourDeparture?->tour,
                'partner' => $this->booking->partner,
                'passengers' => $this->booking->passengers,
                'changes' => $this->changes,
            ],
        );
    }
}
