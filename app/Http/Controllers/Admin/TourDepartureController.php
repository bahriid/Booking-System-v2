<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Tour\BulkCloseDeparturesAction;
use App\Actions\Tour\BulkCreateDeparturesAction;
use App\Actions\Tour\CancelDepartureAction;
use App\Enums\TourDepartureStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkCloseDeparturesRequest;
use App\Http\Requests\Admin\BulkCreateDeparturesRequest;
use App\Models\Tour;
use App\Models\TourDeparture;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Handles tour departure and calendar management for admin panel.
 */
final class TourDepartureController extends Controller
{
    /**
     * Display the calendar view.
     */
    public function index(Request $request): InertiaResponse
    {
        $tours = Tour::active()->orderBy('name')->get();
        $selectedTourId = $request->query('tour') ? (int) $request->query('tour') : null;

        return Inertia::render('admin/calendar', [
            'tours' => $tours,
            'selectedTourId' => $selectedTourId,
            'filters' => $request->only(['tour']),
        ]);
    }

    /**
     * Get departures as JSON for FullCalendar.
     */
    public function events(Request $request): JsonResponse
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $tourId = $request->query('tour');

        $query = TourDeparture::with(['tour', 'driver', 'bookings' => function ($query) {
            $query->whereNotIn('status', ['cancelled', 'rejected', 'expired']);
        }])
            ->whereHas('tour')
            ->when($start, fn ($q) => $q->where('date', '>=', $start))
            ->when($end, fn ($q) => $q->where('date', '<=', $end))
            ->when($tourId, fn ($q) => $q->where('tour_id', $tourId));

        $departures = $query->get();

        $events = $departures->map(function ($departure) {
            $bookedSeats = $departure->booked_seats;
            $remainingSeats = $departure->remaining_seats;
            $isFull = $remainingSeats <= 0;
            $isAlmostFull = $remainingSeats > 0 && $remainingSeats <= 5;

            // Determine color based on status and capacity
            $color = match (true) {
                $departure->status === TourDepartureStatus::CANCELLED => '#f1416c', // danger
                $departure->status === TourDepartureStatus::CLOSED => '#7239ea', // purple
                $isFull => '#ffc700', // warning (full)
                $isAlmostFull => '#50cd89', // success (almost full)
                default => '#009ef7', // primary (available)
            };

            return [
                'id' => $departure->id,
                'title' => ($departure->tour?->code ?? '-').' ('.$bookedSeats.'/'.$departure->capacity.')',
                'start' => $departure->date->format('Y-m-d').'T'.$departure->time,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'tour_id' => $departure->tour_id,
                    'tour_name' => $departure->tour?->name ?? 'N/A',
                    'tour_code' => $departure->tour?->code ?? '-',
                    'time' => $departure->time,
                    'capacity' => $departure->capacity,
                    'booked' => $bookedSeats,
                    'remaining' => $remainingSeats,
                    'status' => $departure->status->value,
                    'status_label' => $departure->status->label(),
                    'season' => $departure->season->value,
                    'driver_id' => $departure->driver_id,
                    'driver_name' => $departure->driver?->name,
                    'notes' => $departure->notes,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Display the specified departure.
     */
    public function show(TourDeparture $departure): InertiaResponse
    {
        $departure->load([
            'tour',
            'driver',
            'bookings' => function ($query) {
                $query->with(['partner', 'passengers.pickupPoint'])
                    ->whereNotIn('status', ['cancelled', 'rejected', 'expired'])
                    ->orderBy('created_at');
            },
        ]);

        // Get all available drivers
        $drivers = User::where('role', UserRole::DRIVER)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/departures/show', compact('departure', 'drivers'));
    }

    /**
     * Update the specified departure.
     */
    public function update(Request $request, TourDeparture $departure): RedirectResponse
    {
        $validated = $request->validate([
            'capacity' => ['required', 'integer', 'min:1', 'max:'.($departure->tour?->default_capacity ?? 999)],
            'status' => ['required', 'string', \Illuminate\Validation\Rule::in(array_column(TourDepartureStatus::cases(), 'value'))],
            'time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'driver_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Normalize time to H:i format
        $time = substr($validated['time'], 0, 5);

        $departure->update([
            'capacity' => $validated['capacity'],
            'status' => TourDepartureStatus::from($validated['status']),
            'time' => $time,
            'driver_id' => $validated['driver_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.calendar', ['tour' => $departure->tour_id])
            ->with('success', 'Departure updated successfully.');
    }

    /**
     * Bulk create departures within a date range.
     */
    public function bulkCreate(BulkCreateDeparturesRequest $request, BulkCreateDeparturesAction $action): RedirectResponse
    {
        $tour = Tour::findOrFail($request->tour_id);
        $count = $action->execute($tour, $request->validated());

        if ($count === 0) {
            return redirect()
                ->route('admin.calendar', ['tour' => $tour->id])
                ->with('warning', 'No new departures were created. They may already exist or fall outside the tour\'s season.');
        }

        return redirect()
            ->route('admin.calendar', ['tour' => $tour->id])
            ->with('success', "Created {$count} new departures for '{$tour->name}'.");
    }

    /**
     * Delete the specified departure.
     */
    public function destroy(TourDeparture $departure): RedirectResponse
    {
        $tourId = $departure->tour_id;

        // Check for existing bookings
        $activeBookings = $departure->bookings()
            ->whereNotIn('status', ['cancelled', 'rejected', 'expired'])
            ->count();

        if ($activeBookings > 0) {
            return redirect()
                ->route('admin.calendar', ['tour' => $tourId])
                ->with('error', "Cannot delete this departure - it has {$activeBookings} active bookings.");
        }

        $departure->delete();

        return redirect()
            ->route('admin.calendar', ['tour' => $tourId])
            ->with('success', 'Departure deleted successfully.');
    }

    /**
     * Bulk close departures within a date range.
     */
    public function bulkClose(BulkCloseDeparturesRequest $request, BulkCloseDeparturesAction $action): RedirectResponse
    {
        $result = $action->execute($request->validatedWithDefaults());

        if ($result['closed'] === 0) {
            return redirect()
                ->route('admin.calendar')
                ->with('warning', 'No departures found matching the criteria to close.');
        }

        $message = "Closed {$result['closed']} departure(s).";
        if ($result['affected_bookings'] > 0) {
            $message .= " {$result['affected_bookings']} booking(s) affected.";
            if ($result['notifications_sent'] > 0) {
                $message .= " {$result['notifications_sent']} partner notification(s) sent.";
            }
        }

        return redirect()
            ->route('admin.calendar', ['tour' => $request->validated('tour_id')])
            ->with('success', $message);
    }

    /**
     * Cancel a departure and notify affected partners.
     */
    public function cancel(Request $request, TourDeparture $departure, CancelDepartureAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
            'is_bad_weather' => ['sometimes', 'boolean'],
            'notify_partners' => ['sometimes', 'boolean'],
        ]);

        $result = $action->execute(
            $departure,
            $validated['reason'] ?? null,
            $request->boolean('is_bad_weather'),
            $request->boolean('notify_partners', true)
        );

        if (! $result['cancelled']) {
            return redirect()
                ->route('admin.departures.show', $departure)
                ->with('error', 'Departure is already cancelled.');
        }

        $message = 'Departure cancelled successfully.';
        if ($result['affected_bookings'] > 0) {
            $message = "Departure cancelled. {$result['affected_bookings']} booking(s) affected.";
            if ($result['notifications_sent'] > 0) {
                $message .= " {$result['notifications_sent']} partner(s) notified.";
            }
        }

        return redirect()
            ->route('admin.calendar', ['tour' => $departure->tour_id])
            ->with('success', $message);
    }
}
