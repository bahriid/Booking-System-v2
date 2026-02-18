<?php

declare(strict_types=1);

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Enums\PaxType;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Partner;
use App\Models\TourDeparture;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;

/**
 * Creates a new booking with passengers.
 *
 * Handles availability check, cut-off validation, overbooking logic,
 * and price calculation based on partner's price list.
 */
final class CreateBookingAction
{
    public function __construct(
        private readonly EmailService $emailService
    ) {}

    /**
     * Execute the booking creation.
     *
     * @param  TourDeparture  $departure  The tour departure to book
     * @param  Partner  $partner  The partner making the booking
     * @param  User  $user  The user creating the booking
     * @param  array<int, array<string, mixed>>  $passengers  Passenger details
     * @return Booking The created booking
     *
     * @throws \InvalidArgumentException When booking cannot be created
     */
    public function execute(
        TourDeparture $departure,
        Partner $partner,
        User $user,
        array $passengers
    ): Booking {
        // Validate departure is open
        if (! $departure->status->acceptsBookings()) {
            throw new \InvalidArgumentException('This departure is not available for booking.');
        }

        // Check cut-off time
        if ($departure->isPastCutoff()) {
            $cutoffHours = $departure->tour?->cutoff_hours ?? 0;
            throw new \InvalidArgumentException(
                "Booking is closed. Cut-off time is {$cutoffHours} hours before departure."
            );
        }

        // Count passengers (excluding infants for seat count)
        $seatCount = collect($passengers)->filter(function ($passenger) {
            return ($passenger['pax_type'] ?? 'adult') !== PaxType::INFANT->value;
        })->count();

        // Check availability - if not enough seats, create overbooking request
        $isOverbooking = ! $departure->hasAvailability($seatCount);

        // Calculate total amount
        $totalAmount = $this->calculateTotalAmount($departure, $partner, $passengers);

        return DB::transaction(function () use ($departure, $partner, $user, $passengers, $isOverbooking, $totalAmount) {
            // Create booking
            $booking = Booking::create([
                'booking_code' => Booking::generateBookingCode($departure),
                'partner_id' => $partner->id,
                'tour_departure_id' => $departure->id,
                'created_by' => $user->id,
                'status' => $isOverbooking ? BookingStatus::SUSPENDED_REQUEST : BookingStatus::CONFIRMED,
                'total_amount' => $totalAmount,
                'penalty_amount' => 0,
                'payment_status' => PaymentStatus::UNPAID,
                'suspended_until' => $isOverbooking ? now()->addHours(2) : null,
            ]);

            // Create passengers
            foreach ($passengers as $passengerData) {
                $booking->passengers()->create([
                    'first_name' => $passengerData['first_name'],
                    'last_name' => $passengerData['last_name'],
                    'phone' => $passengerData['phone'] ?? null,
                    'pickup_point_id' => $passengerData['pickup_point_id'],
                    'pax_type' => $passengerData['pax_type'] ?? PaxType::ADULT->value,
                    'allergies' => $passengerData['allergies'] ?? null,
                    'notes' => $passengerData['notes'] ?? null,
                ]);
            }

            // Send notification emails
            if ($booking->status === BookingStatus::CONFIRMED) {
                $this->emailService->sendBookingConfirmed($booking);
                $this->emailService->sendVoucherReady($booking);
                $this->emailService->sendNewBookingNotification($booking);
            } else {
                // Overbooking request - notify admin
                $this->emailService->sendOverbookingRequested($booking);
            }

            return $booking;
        });
    }

    /**
     * Calculate the total booking amount based on partner's price list.
     *
     * @param  array<int, array<string, mixed>>  $passengers
     */
    private function calculateTotalAmount(TourDeparture $departure, Partner $partner, array $passengers): float
    {
        $total = 0.0;
        $tour = $departure->tour;
        $season = $departure->season;

        if (! $tour) {
            return $total;
        }

        foreach ($passengers as $passenger) {
            $paxType = PaxType::tryFrom($passenger['pax_type'] ?? 'adult') ?? PaxType::ADULT;

            // Infants are free
            if ($paxType === PaxType::INFANT) {
                continue;
            }

            // Get price from partner's price list
            $priceList = $partner->priceLists()
                ->where('tour_id', $tour->id)
                ->where('season', $season)
                ->where('pax_type', $paxType)
                ->first();

            if ($priceList) {
                $total += (float) $priceList->price;
            }
            // If no price list entry, price is 0 (admin should set up prices)
        }

        return $total;
    }
}
