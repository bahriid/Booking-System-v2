<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaxType;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\PickupPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for BookingPassenger model.
 *
 * @extends Factory<BookingPassenger>
 */
final class BookingPassengerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'pickup_point_id' => PickupPoint::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'pax_type' => PaxType::ADULT,
            'phone' => $this->faker->optional(0.6)->phoneNumber(),
            'allergies' => $this->faker->optional(0.1)->randomElement(['Nuts', 'Shellfish', 'Dairy', 'Gluten']),
            'notes' => $this->faker->optional(0.2)->sentence(),
            'price' => $this->faker->randomFloat(2, 30, 80),
        ];
    }

    /**
     * Set the passenger for a specific booking.
     */
    public function forBooking(Booking $booking): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_id' => $booking->id,
        ]);
    }

    /**
     * Set the passenger as an adult.
     */
    public function adult(): static
    {
        return $this->state(fn (array $attributes) => [
            'pax_type' => PaxType::ADULT,
            'price' => $this->faker->randomFloat(2, 50, 80),
        ]);
    }

    /**
     * Set the passenger as a child.
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'pax_type' => PaxType::CHILD,
            'price' => $this->faker->randomFloat(2, 30, 50),
        ]);
    }

    /**
     * Set the passenger as an infant.
     */
    public function infant(): static
    {
        return $this->state(fn (array $attributes) => [
            'pax_type' => PaxType::INFANT,
            'price' => 0,
        ]);
    }
}
