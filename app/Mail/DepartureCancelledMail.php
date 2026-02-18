<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;
use App\Models\TourDeparture;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent to partner when their departure is cancelled.
 */
final class DepartureCancelledMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param TourDeparture $departure The cancelled departure
     * @param Booking $booking The affected booking
     * @param string|null $reason The cancellation reason
     * @param bool $isBadWeather Whether this is a bad weather refund
     */
    public function __construct(
        public readonly TourDeparture $departure,
        public readonly Booking $booking,
        public readonly ?string $reason = null,
        public readonly bool $isBadWeather = false
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isBadWeather
            ? "Tour Cancelled (Weather) - {$this->booking->booking_code}"
            : "Tour Cancelled - {$this->booking->booking_code}";

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.departure-cancelled',
            with: [
                'departure' => $this->departure,
                'tour' => $this->departure?->tour,
                'booking' => $this->booking,
                'partner' => $this->booking->partner,
                'passengers' => $this->booking->passengers,
                'reason' => $this->reason,
                'isBadWeather' => $this->isBadWeather,
            ],
        );
    }
}
