<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for Tour model.
 *
 * @extends Factory<Tour>
 */
final class TourFactory extends Factory
{
    /**
     * Predefined tour templates for realistic data.
     */
    private const TOUR_TEMPLATES = [
        ['code' => 'POSAMCL', 'name' => 'Positano, Amalfi Coast & Limoncello'],
        ['code' => 'CAPRI', 'name' => 'Capri Island Day Trip'],
        ['code' => 'POMPEI', 'name' => 'Pompeii Archaeological Tour'],
        ['code' => 'RAVELLO', 'name' => 'Ravello Gardens & Villa Tour'],
        ['code' => 'SORRENT', 'name' => 'Sorrento Walking Tour'],
        ['code' => 'VESUVIO', 'name' => 'Mount Vesuvius Hiking'],
        ['code' => 'NAPOLI', 'name' => 'Naples City Tour'],
        ['code' => 'ISCHIA', 'name' => 'Ischia Island Day Trip'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('???') . $this->faker->numerify('##')),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'seasonality_start' => now()->year . '-04-01',
            'seasonality_end' => now()->year . '-10-31',
            'cutoff_hours' => 24,
            'default_capacity' => $this->faker->randomElement([30, 40, 50, 60]),
            'is_active' => true,
        ];
    }

    /**
     * Use a predefined tour template.
     */
    public function template(int $index = 0): static
    {
        $template = self::TOUR_TEMPLATES[$index % count(self::TOUR_TEMPLATES)];

        return $this->state(fn (array $attributes) => [
            'code' => $template['code'],
            'name' => $template['name'],
            'description' => 'Experience the beauty of ' . $template['name'] . ' with our expert guides.',
        ]);
    }

    /**
     * Set custom seasonality.
     */
    public function seasonality(string $start, string $end): static
    {
        return $this->state(fn (array $attributes) => [
            'seasonality_start' => $start,
            'seasonality_end' => $end,
        ]);
    }

    /**
     * Indicate that the tour is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
