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
 * Email sent to partner when a booking is cancelled.
 */
final class BookingCancelledMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Booking $booking,
        public readonly ?string $reason = null,
        public readonly bool $hasPenalty = false
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Booking Cancelled - {$this->booking->booking_code}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-cancelled',
            with: [
                'booking' => $this->booking,
                'departure' => $this->booking->tourDeparture,
                'tour' => $this->booking->tourDeparture?->tour,
                'partner' => $this->booking->partner,
                'passengers' => $this->booking->passengers,
                'reason' => $this->reason,
                'hasPenalty' => $this->hasPenalty,
                'penaltyAmount' => $this->booking->penalty_amount ?? 0,
            ],
        );
    }
}
