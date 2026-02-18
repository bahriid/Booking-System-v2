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
 * Email sent to admin when a new confirmed booking is received.
 */
final class NewBookingReceivedMail extends Mailable
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
            subject: __('emails.new_booking_received_subject', ['code' => $this->booking->booking_code]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $departure = $this->booking->tourDeparture;

        return new Content(
            view: 'emails.new-booking-received',
            with: [
                'booking' => $this->booking,
                'departure' => $departure,
                'tour' => $departure?->tour,
                'partner' => $this->booking->partner,
                'passengers' => $this->booking->passengers,
            ],
        );
    }
}
