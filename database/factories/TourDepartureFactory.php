<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Season;
use App\Enums\TourDepartureStatus;
use App\Models\Tour;
use App\Models\TourDeparture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for TourDeparture model.
 *
 * @extends Factory<TourDeparture>
 */
final class TourDepartureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('now', '+3 months');
        $month = (int) $date->format('n');

        return [
            'tour_id' => Tour::factory(),
            'date' => $date,
            'time' => $this->faker->randomElement(['08:00', '08:30', '09:00', '09:30', '10:00']),
            'capacity' => $this->faker->randomElement([30, 40, 50, 60]),
            'status' => TourDepartureStatus::OPEN,
            'season' => Season::fromMonth($month),
            'notes' => $this->faker->optional(0.2)->sentence(),
        ];
    }

    /**
     * Set the departure for a specific tour.
     */
    public function forTour(Tour $tour): static
    {
        return $this->state(fn (array $attributes) => [
            'tour_id' => $tour->id,
            'capacity' => $tour->default_capacity,
        ]);
    }

    /**
     * Set the departure date.
     */
    public function onDate(string $date): static
    {
        $month = (int) date('n', strtotime($date));

        return $this->state(fn (array $attributes) => [
            'date' => $date,
            'season' => Season::fromMonth($month),
        ]);
    }

    /**
     * Set the departure as closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TourDepartureStatus::CLOSED,
        ]);
    }

    /**
     * Set the departure as cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TourDepartureStatus::CANCELLED,
        ]);
    }

    /**
     * Set high season.
     */
    public function highSeason(): static
    {
        return $this->state(fn (array $attributes) => [
            'season' => Season::HIGH,
        ]);
    }
}
