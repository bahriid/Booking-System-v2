<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent to new partners with their login credentials.
 */
final class PartnerWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Partner $partner,
        public string $temporaryPassword,
        public string $contactName,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.partner_welcome_subject', ['app' => config('app.name')]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.partner-welcome',
            with: [
                'partner' => $this->partner,
                'password' => $this->temporaryPassword,
                'contactName' => $this->contactName,
                'loginUrl' => route('login'),
            ],
        );
    }
}
