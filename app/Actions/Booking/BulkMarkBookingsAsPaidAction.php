<?php

declare(strict_types=1);

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Action to mark multiple bookings as paid in bulk.
 *
 * Creates a payment record for each partner and links selected bookings to it.
 */
final class BulkMarkBookingsAsPaidAction
{
    /**
     * Execute the bulk mark as paid action.
     *
     * @param array<int> $bookingIds
     * @param string $method Payment method
     * @param Carbon|null $paidAt Payment date
     * @param string|null $notes Additional notes
     * @param string|null $reference Payment reference
     * @return array{payments_created: int, bookings_marked: int, total_amount: float}
     */
    public function execute(
        array $bookingIds,
        string $method = 'bank_transfer',
        ?Carbon $paidAt = null,
        ?string $notes = null,
        ?string $reference = null
    ): array {
        $paidAt ??= Carbon::now();
        $paymentsCreated = 0;
        $bookingsMarked = 0;
        $totalAmount = 0.0;

        return DB::transaction(function () use (
            $bookingIds,
            $method,
            $paidAt,
            $notes,
            $reference,
            &$paymentsCreated,
            &$bookingsMarked,
            &$totalAmount
        ) {
            // Get all eligible bookings grouped by partner
            $bookings = Booking::whereIn('id', $bookingIds)
                ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
                ->where('payment_status', '!=', PaymentStatus::PAID)
                ->with('partner')
                ->get()
                ->groupBy('partner_id');

            foreach ($bookings as $partnerId => $partnerBookings) {
                // Calculate total amount due for this partner's bookings
                $partnerTotal = $partnerBookings->sum(function ($booking) {
                    return $booking->balance_due;
                });

                if ($partnerTotal <= 0) {
                    continue;
                }

                // Create payment record for this partner
                $payment = Payment::create([
                    'partner_id' => $partnerId,
                    'created_by' => auth()->id(),
                    'amount' => $partnerTotal,
                    'method' => $method,
                    'reference' => $reference,
                    'notes' => $notes ?? 'Bulk payment for ' . $partnerBookings->count() . ' bookings',
                    'paid_at' => $paidAt,
                ]);

                $paymentsCreated++;
                $totalAmount += $partnerTotal;

                // Link each booking to this payment and update payment status
                foreach ($partnerBookings as $booking) {
                    $balanceDue = $booking->balance_due;

                    if ($balanceDue > 0) {
                        // Attach booking to payment with the amount applied
                        $payment->bookings()->attach($booking->id, [
                            'amount' => $balanceDue,
                        ]);

                        // Update booking payment status to PAID
                        $booking->update([
                            'payment_status' => PaymentStatus::PAID,
                        ]);

                        $bookingsMarked++;
                    }
                }
            }

            return [
                'payments_created' => $paymentsCreated,
                'bookings_marked' => $bookingsMarked,
                'total_amount' => $totalAmount,
            ];
        });
    }
}
