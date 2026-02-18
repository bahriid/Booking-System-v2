<?php

declare(strict_types=1);

namespace App\Actions\Booking;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Exports filtered bookings as a PDF file.
 */
final class ExportBookingsPdfAction
{
    /**
     * Execute the bookings PDF export.
     *
     * @param  Request  $request  The current request with filter parameters
     * @return Response The PDF download response
     */
    public function execute(Request $request): Response
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

        $bookings = $query->get();

        $pdf = Pdf::loadView('pdf.bookings-export', [
            'bookings' => $bookings,
            'filters' => [
                'search' => $request->query('search'),
                'status' => $request->query('status'),
                'partner' => $request->query('partner'),
                'tour' => $request->query('tour'),
                'date_from' => $request->query('date_from'),
                'date_to' => $request->query('date_to'),
            ],
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = 'bookings-'.date('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
