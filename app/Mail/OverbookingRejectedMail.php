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
 * Email sent to partner when their overbooking request is rejected.
 */
final class OverbookingRejectedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Booking $booking,
        public readonly ?string $reason = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Booking Request Declined - {$this->booking->booking_code}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.overbooking-rejected',
            with: [
                'booking' => $this->booking,
                'departure' => $this->booking->tourDeparture,
                'tour' => $this->booking->tourDeparture?->tour,
                'partner' => $this->booking->partner,
                'passengers' => $this->booking->passengers,
                'reason' => $this->reason,
            ],
        );
    }
}
