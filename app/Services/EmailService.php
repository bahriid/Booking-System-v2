<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingModifiedMail;
use App\Mail\DailyBookingRecapMail;
use App\Mail\DepartureCancelledMail;
use App\Mail\NewBookingReceivedMail;
use App\Mail\OverbookingApprovedMail;
use App\Mail\OverbookingExpiredMail;
use App\Mail\OverbookingRejectedMail;
use App\Mail\OverbookingRequestedMail;
use App\Mail\VoucherReadyMail;
use App\Models\Booking;
use App\Models\EmailLog;
use App\Models\TourDeparture;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class EmailService
{
    /**
     * Send booking confirmed email to partner.
     */
    public function sendBookingConfirmed(Booking $booking): bool
    {
        $partner = $booking->partner;

        return $this->send(
            mailable: new BookingConfirmedMail($booking),
            toEmail: $partner->email,
            toName: $partner->name,
            eventType: 'booking_confirmed',
            subject: "Booking Confirmed - {$booking->booking_code}",
            bookingId: $booking->id
        );
    }

    /**
     * Send overbooking request email to admin.
     */
    public function sendOverbookingRequested(Booking $booking): bool
    {
        $adminEmail = config('mail.admin_email', 'admin@magship.test');

        return $this->send(
            mailable: new OverbookingRequestedMail($booking),
            toEmail: $adminEmail,
            toName: 'Admin',
            eventType: 'overbooking_requested',
            subject: "Overbooking Request - {$booking->booking_code}",
            bookingId: $booking->id
        );
    }

    /**
     * Send overbooking approved email to partner.
     */
    public function sendOverbookingApproved(Booking $booking): bool
    {
        $partner = $booking->partner;

        return $this->send(
            mailable: new OverbookingApprovedMail($booking),
            toEmail: $partner->email,
            toName: $partner->name,
            eventType: 'overbooking_approved',
            subject: "Booking Approved - {$booking->booking_code}",
            bookingId: $booking->id
        );
    }

    /**
     * Send overbooking rejected email to partner.
     */
    public function sendOverbookingRejected(Booking $booking, ?string $reason = null): bool
    {
        $partner = $booking->partner;

        return $this->send(
            mailable: new OverbookingRejectedMail($booking, $reason),
            toEmail: $partner->email,
            toName: $partner->name,
            eventType: 'overbooking_rejected',
            subject: "Booking Declined - {$booking->booking_code}",
            bookingId: $booking->id
        );
    }

    /**
     * Send overbooking expired email to partner.
     */
    public function sendOverbookingExpired(Booking $booking): bool
    {
        $partner = $booking->partner;

        return $this->send(
            mailable: new OverbookingExpiredMail($booking),
            toEmail: $partner->email,
            toName: $partner->name,
            eventType: 'overbooking_expired',
            subject: "Booking Request Expired - {$booking->booking_code}",
            bookingId: $booking->id
        );
    }

    /**
     * Send booking cancelled email to partner.
     */
    public function sendBookingCancelled(
        Booking $booking,
        ?string $reason = null,
        bool $hasPenalty = false
    ): bool {
        $partner = $booking->partner;

        return $this->send(
            mailable: new BookingCancelledMail($booking, $reason, $hasPenalty),
            toEmail: $partner->email,
            toName: $partner->name,
            eventType: 'booking_cancelled',
            subject: "Booking Cancelled - {$booking->booking_code}",
            bookingId: $booking->id
        );
    }

    /**
     * Send booking modified email to partner.
     */
    public function sendBookingModified(Booking $booking, array $changes = []): bool
    {
        $partner = $booking->partner;

        return $this->send(
            mailable: new BookingModifiedMail($booking, $changes),
            toEmail: $partner->email,
            toName: $partner->name,
            eventType: 'booking_modified',
            subject: "Booking Modified - {$booking->booking_code}",
            bookingId: $booking->id
        );
    }

    /**
     * Send departure cancelled email to partner.
     */
    public function sendDepartureCancelled(
        TourDeparture $departure,
        Booking $booking,
        ?string $reason = null,
        bool $isBadWeather = false
    ): bool {
        $partner = $booking->partner;
        $subject = $isBadWeather
            ? "Tour Cancelled (Weather) - {$booking->booking_code}"
            : "Tour Cancelled - {$booking->booking_code}";

        return $this->send(
            mailable: new DepartureCancelledMail($departure, $booking, $reason, $isBadWeather),
            toEmail: $partner->email,
            toName: $partner->name,
            eventType: 'departure_cancelled',
            subject: $subject,
            bookingId: $booking->id
        );
    }

    /**
     * Send voucher ready email to partner.
     */
    public function sendVoucherReady(Booking $booking): bool
    {
        $partner = $booking->partner;

        return $this->send(
            mailable: new VoucherReadyMail($booking),
            toEmail: $partner->email,
            toName: $partner->name,
            eventType: 'voucher_ready',
            subject: "Your Voucher is Ready - {$booking->booking_code}",
            bookingId: $booking->id
        );
    }

    /**
     * Send new booking notification email to admin.
     */
    public function sendNewBookingNotification(Booking $booking): bool
    {
        $adminEmail = config('mail.admin_email', 'admin@magship.test');

        return $this->send(
            mailable: new NewBookingReceivedMail($booking),
            toEmail: $adminEmail,
            toName: 'Admin',
            eventType: 'new_booking_notification',
            subject: "New Booking Received - {$booking->booking_code}",
            bookingId: $booking->id
        );
    }

    /**
     * Send daily booking recap email to admin.
     *
     * @param  Carbon  $date  The date being summarized
     * @param  Collection  $bookings  All bookings created on this date
     * @param  array<string, mixed>  $stats  Aggregated statistics
     */
    public function sendDailyBookingRecap(Carbon $date, Collection $bookings, array $stats): bool
    {
        $adminEmail = config('mail.admin_email', 'admin@magship.test');

        return $this->send(
            mailable: new DailyBookingRecapMail($date, $bookings, $stats),
            toEmail: $adminEmail,
            toName: 'Admin',
            eventType: 'daily_booking_recap',
            subject: "Daily Booking Recap - {$date->format('d/m/Y')}",
        );
    }

    /**
     * Send a mailable and log the result.
     */
    private function send(
        Mailable $mailable,
        string $toEmail,
        string $toName,
        string $eventType,
        string $subject,
        ?int $bookingId = null
    ): bool {
        try {
            Mail::to($toEmail, $toName)->send($mailable);

            EmailLog::logEmail(
                eventType: $eventType,
                toEmail: $toEmail,
                subject: $subject,
                success: true,
                toName: $toName,
                bookingId: $bookingId
            );

            return true;
        } catch (Throwable $e) {
            Log::error("Failed to send email [{$eventType}]: {$e->getMessage()}", [
                'event_type' => $eventType,
                'to_email' => $toEmail,
                'booking_id' => $bookingId,
                'exception' => $e,
            ]);

            EmailLog::logEmail(
                eventType: $eventType,
                toEmail: $toEmail,
                subject: $subject,
                success: false,
                toName: $toName,
                bookingId: $bookingId,
                errorMessage: $e->getMessage()
            );

            return false;
        }
    }
}
