<?php

declare(strict_types=1);

namespace App\Actions\Booking;

use App\Exports\BookingsExport;
use App\Models\Booking;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Exports filtered bookings as an Excel file.
 */
final class ExportBookingsAction
{
    /**
     * Execute the bookings export.
     *
     * @param Request $request The current request with filter parameters
     * @return BinaryFileResponse The Excel download response
     */
    public function execute(Request $request): BinaryFileResponse
    {
        $query = Booking::with(['partner', 'tourDeparture.tour', 'passengers.pickupPoint'])
            ->whereHas('tourDeparture.tour')
            ->orderBy('created_at', 'desc');

        // Search by booking code or partner name
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('partner', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by partner
        if ($partnerId = $request->query('partner')) {
            $query->where('partner_id', $partnerId);
        }

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Filter by tour
        if ($tourId = $request->query('tour')) {
            $query->whereHas('tourDeparture', function ($q) use ($tourId) {
                $q->where('tour_id', $tourId);
            });
        }

        // Filter by date range
        if ($dateFrom = $request->query('date_from')) {
            $query->whereHas('tourDeparture', function ($q) use ($dateFrom) {
                $q->where('date', '>=', $dateFrom);
            });
        }

        if ($dateTo = $request->query('date_to')) {
            $query->whereHas('tourDeparture', function ($q) use ($dateTo) {
                $q->where('date', '<=', $dateTo);
            });
        }

        $filename = 'bookings-' . date('Y-m-d') . '.xlsx';

        return Excel::download(new BookingsExport($query), $filename);
    }
}
