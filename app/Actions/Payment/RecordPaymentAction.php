<?php

declare(strict_types=1);

namespace App\Actions\Payment;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Partner;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Records a payment or credit for a partner.
 */
final class RecordPaymentAction
{
    /**
     * Execute the payment recording.
     *
     * @param int $partnerId The partner receiving payment
     * @param float $amount The payment amount
     * @param string|null $method Payment method (bank_transfer, cash, etc.)
     * @param Carbon $paidAt The payment date
     * @param string|null $notes Additional notes
     * @param string|null $reference Reference number (invoice, booking code, etc.)
     * @param int|null $bookingId Optional booking to link payment to
     *
     * @return Payment The created payment record
     */
    public function execute(
        int $partnerId,
        float $amount,
        ?string $method,
        Carbon $paidAt,
        ?string $notes = null,
        ?string $reference = null,
        ?int $bookingId = null
    ): Payment {
        $partner = Partner::findOrFail($partnerId);

        $payment = Payment::create([
            'partner_id' => $partner->id,
            'created_by' => Auth::id(),
            'amount' => $amount,
            'method' => $method,
            'reference' => $reference,
            'notes' => $notes,
            'paid_at' => $paidAt,
        ]);

        // Link payment to specific booking if provided
        if ($bookingId) {
            $booking = Booking::findOrFail($bookingId);

            $payment->bookings()->attach($booking->id, [
                'amount' => $amount,
            ]);

            // Update booking payment status
            $totalPaid = (float) $booking->fresh()->payments()->sum('booking_payment.amount');

            if ($totalPaid >= (float) $booking->total_amount) {
                $booking->update(['payment_status' => PaymentStatus::PAID]);
            } elseif ($totalPaid > 0) {
                $booking->update(['payment_status' => PaymentStatus::PARTIAL]);
            }
        }

        return $payment->load('partner');
    }
}
