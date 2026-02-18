<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\TourDeparture;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Handles PDF generation for vouchers and manifests.
 */
final class PdfController extends Controller
{
    /**
     * Generate and download a booking voucher PDF.
     */
    public function bookingVoucher(Request $request, Booking $booking): Response
    {
        $user = $request->user();

        // Authorization check
        if ($user->isPartner() && $booking->partner_id !== $user->partner_id) {
            abort(403, 'You do not have access to this booking.');
        }

        // Only allow voucher for confirmed/completed bookings
        if (!in_array($booking->status, [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])) {
            abort(403, 'Voucher is only available for confirmed bookings.');
        }

        $booking->load([
            'partner',
            'tourDeparture.tour',
            'passengers.pickupPoint',
        ]);

        $pdf = Pdf::loadView('pdf.booking-voucher', [
            'booking' => $booking,
            'departure' => $booking->tourDeparture,
            'tour' => $booking->tourDeparture?->tour,
            'partner' => $booking->partner,
            'passengers' => $booking->passengers,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = "voucher-{$booking->booking_code}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Stream a booking voucher PDF (for preview/print).
     */
    public function bookingVoucherStream(Request $request, Booking $booking): Response
    {
        $user = $request->user();

        // Authorization check
        if ($user->isPartner() && $booking->partner_id !== $user->partner_id) {
            abort(403, 'You do not have access to this booking.');
        }

        // Only allow voucher for confirmed/completed bookings
        if (!in_array($booking->status, [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])) {
            abort(403, 'Voucher is only available for confirmed bookings.');
        }

        $booking->load([
            'partner',
            'tourDeparture.tour',
            'passengers.pickupPoint',
        ]);

        $pdf = Pdf::loadView('pdf.booking-voucher', [
            'booking' => $booking,
            'departure' => $booking->tourDeparture,
            'tour' => $booking->tourDeparture?->tour,
            'partner' => $booking->partner,
            'passengers' => $booking->passengers,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("voucher-{$booking->booking_code}.pdf");
    }

    /**
     * Generate and download a tour manifest PDF.
     */
    public function tourManifest(Request $request, TourDeparture $departure): Response
    {
        $user = $request->user();

        // Authorization check for drivers
        if ($user->isDriver() && $departure->driver_id !== $user->id) {
            abort(403, 'You are not assigned to this departure.');
        }

        $departure->load([
            'tour',
            'driver',
            'bookings' => function ($query) {
                $query->whereIn('status', [
                    BookingStatus::CONFIRMED,
                    BookingStatus::COMPLETED,
                ])->with(['partner', 'passengers.pickupPoint']);
            },
        ]);

        // Collect all passengers
        $passengers = $departure->bookings->flatMap(function ($booking) {
            return $booking->passengers->map(function ($passenger) use ($booking) {
                $passenger->booking_code = $booking->booking_code;
                $passenger->partner_name = $booking->partner->name;

                return $passenger;
            });
        })->sortBy([
            fn ($a, $b) => strcmp($a->pickupPoint?->default_time ?? '99:99', $b->pickupPoint?->default_time ?? '99:99'),
            fn ($a, $b) => strcmp($a->pickupPoint?->name ?? 'ZZZ', $b->pickupPoint?->name ?? 'ZZZ'),
            fn ($a, $b) => strcmp($a->last_name ?? '', $b->last_name ?? ''),
        ])->values();

        // Pax summary
        $paxCounts = $passengers->groupBy('pax_type')->map->count();

        // Passengers with allergies
        $allergies = $passengers->filter(fn ($p) => !empty($p->allergies));

        // Pickup summary
        $pickupSummary = $passengers
            ->groupBy(fn ($p) => $p->pickupPoint?->name ?? 'Not specified')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'time' => $group->first()->pickupPoint?->default_time ?? '-',
                ];
            })
            ->sortBy('time');

        $pdf = Pdf::loadView('pdf.tour-manifest', [
            'departure' => $departure,
            'tour' => $departure->tour,
            'driver' => $departure->driver,
            'passengers' => $passengers,
            'paxCounts' => $paxCounts,
            'allergies' => $allergies,
            'pickupSummary' => $pickupSummary,
            'bookingsCount' => $departure->bookings->count(),
        ]);

        $pdf->setPaper('a4', 'portrait');

        $tourCode = $departure->tour?->code ?? 'unknown';
        $filename = "manifest-{$tourCode}-{$departure->date->format('Ymd')}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Stream a tour manifest PDF (for preview/print).
     */
    public function tourManifestStream(Request $request, TourDeparture $departure): Response
    {
        $user = $request->user();

        // Authorization check for drivers
        if ($user->isDriver() && $departure->driver_id !== $user->id) {
            abort(403, 'You are not assigned to this departure.');
        }

        $departure->load([
            'tour',
            'driver',
            'bookings' => function ($query) {
                $query->whereIn('status', [
                    BookingStatus::CONFIRMED,
                    BookingStatus::COMPLETED,
                ])->with(['partner', 'passengers.pickupPoint']);
            },
        ]);

        // Collect all passengers
        $passengers = $departure->bookings->flatMap(function ($booking) {
            return $booking->passengers->map(function ($passenger) use ($booking) {
                $passenger->booking_code = $booking->booking_code;
                $passenger->partner_name = $booking->partner->name;

                return $passenger;
            });
        })->sortBy([
            fn ($a, $b) => strcmp($a->pickupPoint?->default_time ?? '99:99', $b->pickupPoint?->default_time ?? '99:99'),
            fn ($a, $b) => strcmp($a->pickupPoint?->name ?? 'ZZZ', $b->pickupPoint?->name ?? 'ZZZ'),
            fn ($a, $b) => strcmp($a->last_name ?? '', $b->last_name ?? ''),
        ])->values();

        // Pax summary
        $paxCounts = $passengers->groupBy('pax_type')->map->count();

        // Passengers with allergies
        $allergies = $passengers->filter(fn ($p) => !empty($p->allergies));

        // Pickup summary
        $pickupSummary = $passengers
            ->groupBy(fn ($p) => $p->pickupPoint?->name ?? 'Not specified')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'time' => $group->first()->pickupPoint?->default_time ?? '-',
                ];
            })
            ->sortBy('time');

        $pdf = Pdf::loadView('pdf.tour-manifest', [
            'departure' => $departure,
            'tour' => $departure->tour,
            'driver' => $departure->driver,
            'passengers' => $passengers,
            'paxCounts' => $paxCounts,
            'allergies' => $allergies,
            'pickupSummary' => $pickupSummary,
            'bookingsCount' => $departure->bookings->count(),
        ]);

        $pdf->setPaper('a4', 'portrait');

        $tourCode = $departure->tour?->code ?? 'unknown';

        return $pdf->stream("manifest-{$tourCode}-{$departure->date->format('Ymd')}.pdf");
    }
}
