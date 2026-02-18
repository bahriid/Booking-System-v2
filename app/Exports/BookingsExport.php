<?php

declare(strict_types=1);

namespace App\Exports;

use App\Enums\PaxType;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export bookings to Excel spreadsheet.
 */
final class BookingsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * @param Builder<Booking> $query
     */
    public function __construct(
        private readonly Builder $query
    ) {}

    /**
     * Return the query for the export.
     *
     * @return Builder<Booking>
     */
    public function query(): Builder
    {
        return $this->query;
    }

    /**
     * Define the column headings.
     *
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'Booking Code',
            'Status',
            'Partner',
            'Tour',
            'Tour Code',
            'Date',
            'Time',
            'Adults',
            'Children',
            'Infants',
            'Total Pax',
            'Amount',
            'Payment Status',
            'Penalty',
            'Passenger Names',
            'Pickup Points',
            'Allergies',
            'Notes',
            'Created At',
        ];
    }

    /**
     * Map each booking row to export columns.
     *
     * @param Booking $booking
     * @return list<string|int|float|null>
     */
    public function map($booking): array
    {
        $passengers = $booking->passengers;
        $paxCounts = $passengers->groupBy(fn ($p) => $p->pax_type->value);

        $adultsCount = $paxCounts->get(PaxType::ADULT->value)?->count() ?? 0;
        $childrenCount = $paxCounts->get(PaxType::CHILD->value)?->count() ?? 0;
        $infantsCount = $paxCounts->get(PaxType::INFANT->value)?->count() ?? 0;

        $passengerNames = $passengers
            ->map(fn ($p) => $p->full_name)
            ->implode(', ');

        $pickupPoints = $passengers
            ->pluck('pickupPoint.name')
            ->filter()
            ->unique()
            ->implode(', ');

        $allergies = $passengers
            ->filter(fn ($p) => !empty($p->allergies))
            ->map(fn ($p) => "{$p->full_name}: {$p->allergies}")
            ->implode(', ');

        return [
            $booking->booking_code,
            $booking->status->label(),
            $booking->partner->name ?? '',
            $booking->tourDeparture?->tour?->name ?? '',
            $booking->tourDeparture?->tour?->code ?? '',
            $booking->tourDeparture?->date?->format('d/m/Y') ?? '',
            $booking->tourDeparture?->time ?? '',
            $adultsCount,
            $childrenCount,
            $infantsCount,
            $passengers->count(),
            $booking->total_amount,
            $booking->payment_status->label(),
            $booking->penalty_amount,
            $passengerNames,
            $pickupPoints,
            $allergies,
            $booking->notes ?? '',
            $booking->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Style the header row.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
