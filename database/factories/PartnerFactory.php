<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PartnerType;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for Partner model.
 *
 * @extends Factory<Partner>
 */
final class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(PartnerType::cases());
        $name = $type === PartnerType::HOTEL
            ? 'Hotel ' . $this->faker->lastName()
            : $this->faker->company();

        return [
            'name' => $name,
            'type' => $type,
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'vat_number' => 'IT' . $this->faker->numerify('###########'),
            'sdi_pec' => $this->faker->randomElement([$this->faker->numerify('#######'), $this->faker->safeEmail()]),
            'address' => $this->faker->address(),
            'is_active' => true,
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the partner is a hotel.
     */
    public function hotel(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PartnerType::HOTEL,
            'name' => 'Hotel ' . $this->faker->lastName(),
        ]);
    }

    /**
     * Indicate that the partner is a tour operator.
     */
    public function tourOperator(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PartnerType::TOUR_OPERATOR,
            'name' => $this->faker->company() . ' Tours',
        ]);
    }

    /**
     * Indicate that the partner is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
