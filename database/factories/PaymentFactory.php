<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for Payment model.
 *
 * @extends Factory<Payment>
 */
final class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'method' => $this->faker->randomElement(['bank_transfer', 'cash', 'credit_card']),
            'reference' => $this->faker->optional(0.7)->numerify('TRX-######'),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'paid_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

    /**
     * Set the payment for a specific partner.
     */
    public function forPartner(Partner $partner): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_id' => $partner->id,
        ]);
    }

    /**
     * Set payment method as bank transfer.
     */
    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'bank_transfer',
            'reference' => $this->faker->numerify('BNK-######'),
        ]);
    }

    /**
     * Set payment method as cash.
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'cash',
            'reference' => null,
        ]);
    }
}
