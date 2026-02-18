<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;
use App\Models\Partner;
use App\Models\Tour;
use App\Models\TourDeparture;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent to partner when booking is confirmed with voucher download link.
 */
final class VoucherReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public Partner $partner;
    public ?Tour $tour;
    public TourDeparture $departure;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Booking $booking,
    ) {
        $this->partner = $booking->partner;
        $this->tour = $booking->tourDeparture?->tour;
        $this->departure = $booking->tourDeparture;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.voucher_ready_subject', ['code' => $this->booking->booking_code]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.voucher-ready',
            with: [
                'booking' => $this->booking,
                'partner' => $this->partner,
                'tour' => $this->tour,
                'departure' => $this->departure,
                'passengers' => $this->booking->passengers,
                'voucherUrl' => route('partner.bookings.voucher', $this->booking),
            ],
        );
    }
}
