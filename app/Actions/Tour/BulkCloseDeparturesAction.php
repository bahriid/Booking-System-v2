<?php

declare(strict_types=1);

namespace App\Actions\Tour;

use App\Enums\BookingStatus;
use App\Enums\TourDepartureStatus;
use App\Models\Booking;
use App\Models\TourDeparture;
use App\Services\EmailService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Action to bulk close departures within a date range.
 */
final class BulkCloseDeparturesAction
{
    public function __construct(
        private readonly EmailService $emailService
    ) {}

    /**
     * Execute the bulk close action.
     *
     * @param array{
     *     tour_id: int|null,
     *     start_date: string,
     *     end_date: string,
     *     times: array|null,
     *     reason: string|null,
     *     notify_partners: bool
     * } $data
     * @return array{closed: int, affected_bookings: int, notifications_sent: int}
     */
    public function execute(array $data): array
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $tourId = $data['tour_id'] ?? null;
        $times = $data['times'] ?? null;
        $reason = $data['reason'] ?? null;
        $notifyPartners = $data['notify_partners'] ?? true;

        // Build query for departures to close
        $query = TourDeparture::query()
            ->where('status', TourDepartureStatus::OPEN)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($tourId) {
            $query->where('tour_id', $tourId);
        }

        if ($times && is_array($times) && count($times) > 0) {
            $query->whereIn('time', $times);
        }

        $departures = $query->with(['tour', 'bookings' => function ($q) {
            $q->whereIn('status', [
                BookingStatus::CONFIRMED->value,
                BookingStatus::SUSPENDED_REQUEST->value,
            ])->with('partner');
        }])->get();

        $closedCount = 0;
        $affectedBookingsCount = 0;
        $notificationsSent = 0;

        foreach ($departures as $departure) {
            // Close the departure
            $departure->update([
                'status' => TourDepartureStatus::CLOSED,
                'notes' => $reason ? "Closed: {$reason}" : ($departure->notes ?? 'Bulk closed'),
            ]);
            $closedCount++;

            // Handle affected bookings
            foreach ($departure->bookings as $booking) {
                $affectedBookingsCount++;

                // Send notification to partner if requested
                if ($notifyPartners) {
                    $sent = $this->emailService->sendDepartureCancelled(
                        $departure,
                        $booking,
                        $reason,
                        false // Not bad weather
                    );

                    if ($sent) {
                        $notificationsSent++;
                    }
                }
            }
        }

        return [
            'closed' => $closedCount,
            'affected_bookings' => $affectedBookingsCount,
            'notifications_sent' => $notificationsSent,
        ];
    }
}
