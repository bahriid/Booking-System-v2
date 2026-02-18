<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Partner;
use App\Models\TourDeparture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for Booking model.
 *
 * @extends Factory<Booking>
 */
final class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_code' => strtoupper($this->faker->lexify('???')) . '-' . $this->faker->numerify('##') . '-' . $this->faker->date('Ymd'),
            'partner_id' => Partner::factory(),
            'tour_departure_id' => TourDeparture::factory(),
            'status' => BookingStatus::CONFIRMED,
            'total_amount' => $this->faker->randomFloat(2, 100, 500),
            'penalty_amount' => 0,
            'payment_status' => PaymentStatus::UNPAID,
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Set the booking for a specific partner.
     */
    public function forPartner(Partner $partner): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_id' => $partner->id,
        ]);
    }

    /**
     * Set the booking for a specific departure.
     */
    public function forDeparture(TourDeparture $departure): static
    {
        return $this->state(fn (array $attributes) => [
            'tour_departure_id' => $departure->id,
            'booking_code' => Booking::generateBookingCode($departure),
        ]);
    }

    /**
     * Set the booking as confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::CONFIRMED,
        ]);
    }

    /**
     * Set the booking as a suspended overbooking request.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::SUSPENDED_REQUEST,
            'suspended_until' => now()->addHours(2),
        ]);
    }

    /**
     * Set the booking as cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::CANCELLED,
            'cancellation_reason' => $this->faker->sentence(),
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Set the booking as paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => PaymentStatus::PAID,
        ]);
    }

    /**
     * Set the booking as partially paid.
     */
    public function partiallyPaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => PaymentStatus::PARTIAL,
        ]);
    }
}
