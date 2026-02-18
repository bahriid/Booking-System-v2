<?php

declare(strict_types=1);

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Enums\PaxType;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\PickupPoint;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;

/**
 * Action to update a booking and its passengers.
 */
final class UpdateBookingAction
{
    public function __construct(
        private readonly EmailService $emailService
    ) {}

    /**
     * Execute the booking update.
     *
     * @param  Booking  $booking  The booking to update
     * @param  array  $passengers  Updated passenger data
     * @param  string|null  $notes  Updated booking notes
     * @param  array  $newPassengers  New passengers to add
     * @param  array  $removedPassengerIds  IDs of passengers to remove
     * @return array{booking: Booking, changes: array}
     *
     * @throws \InvalidArgumentException If booking cannot be modified
     */
    public function execute(Booking $booking, array $passengers, ?string $notes = null, array $newPassengers = [], array $removedPassengerIds = []): array
    {
        // Check if booking is modifiable
        if (! $booking->status->canBeModified()) {
            throw new \InvalidArgumentException('This booking cannot be modified.');
        }

        // Check cut-off time
        if ($booking->tourDeparture->isPastCutoff()) {
            throw new \InvalidArgumentException('This booking cannot be modified after the cut-off time.');
        }

        // Ensure at least one passenger remains after removal
        $currentCount = $booking->passengers()->count();
        $remainingAfterRemoval = $currentCount - count($removedPassengerIds) + count($newPassengers);
        if ($remainingAfterRemoval < 1) {
            throw new \InvalidArgumentException('A booking must have at least one passenger. Cancel the booking instead.');
        }

        // Check capacity for new passengers (account for freed seats from removals)
        if (! empty($newPassengers)) {
            $departure = $booking->tourDeparture;
            $remainingSeats = $departure->remaining_seats;

            // Seats freed by removed non-infant passengers
            $freedSeats = 0;
            if (! empty($removedPassengerIds)) {
                $freedSeats = BookingPassenger::where('booking_id', $booking->id)
                    ->whereIn('id', $removedPassengerIds)
                    ->where('pax_type', '!=', PaxType::INFANT)
                    ->count();
            }

            $newSeatCount = count(array_filter($newPassengers, fn ($p) => ($p['pax_type'] ?? 'adult') !== 'infant'));

            if ($newSeatCount > ($remainingSeats + $freedSeats)) {
                throw new \InvalidArgumentException("Not enough available seats. Only " . ($remainingSeats + $freedSeats) . " seats remaining.");
            }
        }

        $changes = [];

        return DB::transaction(function () use ($booking, $passengers, $notes, $newPassengers, $removedPassengerIds, &$changes) {
            // Track booking-level changes
            if ($notes !== null && $notes !== $booking->notes) {
                $changes['notes'] = [
                    'old' => $booking->notes,
                    'new' => $notes,
                ];
                $booking->notes = $notes;
            }

            // Remove passengers
            if (! empty($removedPassengerIds)) {
                $removedPassengers = $this->removePassengers($booking, $removedPassengerIds);
                if (! empty($removedPassengers)) {
                    $changes['removed_passengers'] = $removedPassengers;
                }
            }

            // Update existing passengers
            $passengerChanges = $this->updatePassengers($booking, $passengers);
            if (! empty($passengerChanges)) {
                $changes['passengers'] = $passengerChanges;
            }

            // Add new passengers
            if (! empty($newPassengers)) {
                $addedPassengers = $this->addNewPassengers($booking, $newPassengers);
                if (! empty($addedPassengers)) {
                    $changes['added_passengers'] = $addedPassengers;
                }
            }

            // Recalculate total amount if passengers were added or removed
            if (! empty($removedPassengerIds) || ! empty($newPassengers)) {
                $newTotal = $booking->passengers()->sum('price');
                if ((float) $newTotal !== (float) $booking->total_amount) {
                    $changes['total_amount'] = [
                        'old' => $booking->total_amount,
                        'new' => $newTotal,
                    ];
                    $booking->total_amount = $newTotal;
                }
            }

            // Auto-confirm if a SUSPENDED_REQUEST booking now fits within capacity
            if ($booking->status === BookingStatus::SUSPENDED_REQUEST) {
                $departure = $booking->tourDeparture->fresh();
                $seatCount = $booking->passengers()
                    ->where('pax_type', '!=', PaxType::INFANT)
                    ->count();

                if ($departure->remaining_seats >= $seatCount) {
                    $changes['status'] = [
                        'old' => BookingStatus::SUSPENDED_REQUEST->label(),
                        'new' => BookingStatus::CONFIRMED->label(),
                    ];
                    $booking->status = BookingStatus::CONFIRMED;
                    $booking->suspended_until = null;
                }
            }

            // Save booking if modified
            if ($booking->isDirty()) {
                $booking->save();
            }

            // Send appropriate notifications
            if ($booking->status === BookingStatus::CONFIRMED && isset($changes['status'])) {
                // Booking was auto-confirmed: send confirmation + voucher
                $this->emailService->sendBookingConfirmed($booking);
                $this->emailService->sendVoucherReady($booking);
            } elseif (! empty($changes)) {
                // Regular modification notification
                $this->emailService->sendBookingModified($booking, $changes);
            }

            return [
                'booking' => $booking->fresh(['passengers.pickupPoint', 'tourDeparture.tour', 'partner']),
                'changes' => $changes,
            ];
        });
    }

    /**
     * Remove passengers from the booking.
     *
     * @param  Booking  $booking  The booking
     * @param  array  $passengerIds  IDs of passengers to remove
     * @return array List of removed passenger descriptions
     */
    private function removePassengers(Booking $booking, array $passengerIds): array
    {
        $removed = [];

        $passengers = BookingPassenger::where('booking_id', $booking->id)
            ->whereIn('id', $passengerIds)
            ->get();

        foreach ($passengers as $passenger) {
            $removed[] = "{$passenger->first_name} {$passenger->last_name} ({$passenger->pax_type->value})";
            $passenger->delete();
        }

        return $removed;
    }

    /**
     * Add new passengers to the booking.
     */
    private function addNewPassengers(Booking $booking, array $newPassengers): array
    {
        $added = [];

        foreach ($newPassengers as $passengerData) {
            $passenger = BookingPassenger::create([
                'booking_id' => $booking->id,
                'first_name' => $passengerData['first_name'],
                'last_name' => $passengerData['last_name'],
                'pax_type' => PaxType::from($passengerData['pax_type']),
                'pickup_point_id' => $passengerData['pickup_point_id'] ?? null,
                'phone' => $passengerData['phone'] ?? null,
                'allergies' => $passengerData['allergies'] ?? null,
                'notes' => $passengerData['notes'] ?? null,
            ]);

            $added[] = "{$passenger->first_name} {$passenger->last_name} ({$passengerData['pax_type']})";
        }

        return $added;
    }

    /**
     * Update passengers for the booking.
     */
    private function updatePassengers(Booking $booking, array $passengers): array
    {
        $changes = [];

        foreach ($passengers as $passengerData) {
            $passengerId = $passengerData['id'] ?? null;

            if (! $passengerId) {
                continue;
            }

            $passenger = BookingPassenger::where('id', $passengerId)
                ->where('booking_id', $booking->id)
                ->first();

            if (! $passenger) {
                continue;
            }

            $passengerChanges = [];

            // Check and update fields
            $fields = [
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'phone' => 'Phone',
                'allergies' => 'Allergies',
                'notes' => 'Notes',
            ];

            foreach ($fields as $field => $label) {
                if (isset($passengerData[$field]) && $passengerData[$field] !== $passenger->$field) {
                    $passengerChanges[$label] = [
                        'old' => $passenger->$field ?? '',
                        'new' => $passengerData[$field] ?? '',
                    ];
                    $passenger->$field = $passengerData[$field];
                }
            }

            // Handle pickup point separately
            if (isset($passengerData['pickup_point_id'])) {
                $newPickupPointId = $passengerData['pickup_point_id'] ?: null;
                if ($newPickupPointId !== $passenger->pickup_point_id) {
                    $oldPickupPoint = $passenger->pickupPoint?->name ?? 'None';
                    $newPickupPoint = $newPickupPointId
                        ? (PickupPoint::find($newPickupPointId)?->name ?? 'None')
                        : 'None';

                    $passengerChanges['Pickup Point'] = [
                        'old' => $oldPickupPoint,
                        'new' => $newPickupPoint,
                    ];
                    $passenger->pickup_point_id = $newPickupPointId;
                }
            }

            if (! empty($passengerChanges)) {
                $passenger->save();
                $changes["{$passenger->first_name} {$passenger->last_name}"] = $passengerChanges;
            }
        }

        return $changes;
    }
}
