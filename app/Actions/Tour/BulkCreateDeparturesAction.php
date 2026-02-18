<?php

declare(strict_types=1);

namespace App\Actions\Tour;

use App\Enums\Season;
use App\Enums\TourDepartureStatus;
use App\Models\Tour;
use App\Models\TourDeparture;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Creates multiple tour departures within a date range.
 */
final class BulkCreateDeparturesAction
{
    /**
     * Execute the bulk departure creation.
     *
     * @param Tour $tour The tour to create departures for
     * @param array<string, mixed> $data Validated data including date range, days, etc.
     * @return int Number of departures created
     */
    public function execute(Tour $tour, array $data): int
    {
        $count = 0;
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $selectedDays = $data['days']; // 0 = Sunday, 1 = Monday, etc.
        $time = $data['time'];
        $capacity = $data['capacity'];
        $season = $data['season'] instanceof Season ? $data['season'] : Season::from($data['season']);
        $notes = $data['notes'] ?? null;

        DB::transaction(function () use ($tour, $startDate, $endDate, $selectedDays, $time, $capacity, $season, $notes, &$count) {
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                // Check if current day is in selected days of week
                if (in_array($currentDate->dayOfWeek, $selectedDays)) {
                    // Check if tour is in season for this date
                    if ($tour->isInSeason($currentDate)) {
                        // Check if departure already exists for this date/time
                        $exists = TourDeparture::where('tour_id', $tour->id)
                            ->where('date', $currentDate->format('Y-m-d'))
                            ->where('time', $time)
                            ->exists();

                        if (!$exists) {
                            TourDeparture::create([
                                'tour_id' => $tour->id,
                                'date' => $currentDate->format('Y-m-d'),
                                'time' => $time,
                                'capacity' => $capacity,
                                'status' => TourDepartureStatus::OPEN,
                                'season' => $season,
                                'notes' => $notes,
                            ]);
                            $count++;
                        }
                    }
                }

                $currentDate->addDay();
            }
        });

        return $count;
    }
}
