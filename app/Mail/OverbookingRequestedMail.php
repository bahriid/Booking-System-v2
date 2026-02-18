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
 * Email sent to admin when an overbooking request is created.
 */
final class OverbookingRequestedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Booking $booking
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Overbooking Request - {$this->booking->booking_code} - Action Required",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $departure = $this->booking->tourDeparture;

        return new Content(
            view: 'emails.overbooking-requested',
            with: [
                'booking' => $this->booking,
                'departure' => $departure,
                'tour' => $departure?->tour,
                'partner' => $this->booking->partner,
                'passengers' => $this->booking->passengers,
                'currentCapacity' => $departure->capacity,
                'bookedSeats' => $departure->booked_seats,
                'requestedSeats' => $this->booking->passengers->count(),
                'expiresAt' => $this->booking->suspended_until,
            ],
        );
    }
}
