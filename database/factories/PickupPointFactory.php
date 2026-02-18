<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PickupPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for PickupPoint model.
 *
 * @extends Factory<PickupPoint>
 */
final class PickupPointFactory extends Factory
{
    /**
     * Predefined pickup points for realistic data.
     */
    private const PICKUP_TEMPLATES = [
        ['name' => 'MAIN ROAD', 'location' => 'Via Correale, Sorrento', 'time' => '07:25'],
        ['name' => 'HOTEL TUNNEL', 'location' => 'Via Capo, Sorrento', 'time' => '07:30'],
        ['name' => 'PIAZZA TASSO', 'location' => 'Piazza Tasso, Sorrento', 'time' => '07:45'],
        ['name' => 'PORTO', 'location' => 'Marina Piccola, Sorrento', 'time' => '08:00'],
        ['name' => 'SANT\'AGNELLO', 'location' => 'Via Crawford, Sant\'Agnello', 'time' => '07:20'],
        ['name' => 'PIANO DI SORRENTO', 'location' => 'Piazza Cota, Piano di Sorrento', 'time' => '07:15'],
        ['name' => 'META', 'location' => 'Via Caracciolo, Meta', 'time' => '07:10'],
        ['name' => 'VICO EQUENSE', 'location' => 'Corso Umberto, Vico Equense', 'time' => '07:00'],
    ];

    /**
     * Track which templates have been used.
     */
    private static int $templateIndex = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => strtoupper($this->faker->streetName()),
            'location' => $this->faker->address(),
            'default_time' => $this->faker->time('H:i'),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    /**
     * Use a predefined pickup point template.
     */
    public function template(int $index = null): static
    {
        $i = $index ?? self::$templateIndex++;
        $template = self::PICKUP_TEMPLATES[$i % count(self::PICKUP_TEMPLATES)];

        return $this->state(fn (array $attributes) => [
            'name' => $template['name'],
            'location' => $template['location'],
            'default_time' => $template['time'],
            'sort_order' => $i + 1,
        ]);
    }

    /**
     * Indicate that the pickup point is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
