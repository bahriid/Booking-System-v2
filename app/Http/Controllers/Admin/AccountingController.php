<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Booking\BulkMarkBookingsAsPaidAction;
use App\Actions\Payment\RecordPaymentAction;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkMarkBookingsAsPaidRequest;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Http\Requests\Admin\StoreCreditRequest;
use App\Models\Booking;
use App\Models\Partner;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class AccountingController extends Controller
{
    /**
     * Display the accounting overview.
     */
    public function index(Request $request): InertiaResponse
    {
        // Get date range filter (default: current month)
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now()->endOfMonth();

        // Date type filter (booking_date or tour_date)
        $dateType = $request->input('date_type', 'booking_date');

        // Summary statistics
        $revenueQuery = Booking::query()
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED]);

        if ($dateType === 'tour_date') {
            $revenueQuery->whereHas('tourDeparture', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            });
        } else {
            $revenueQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $totalRevenue = $revenueQuery->sum('total_amount');

        $totalPayments = Payment::query()
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->where('method', '!=', 'credit')
            ->sum('amount');

        $paymentCount = Payment::query()
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->where('method', '!=', 'credit')
            ->count();

        $totalPenalties = Booking::query()
            ->where('status', BookingStatus::CANCELLED)
            ->whereBetween('cancelled_at', [$startDate, $endDate])
            ->sum('penalty_amount');

        $penaltyCount = Booking::query()
            ->where('status', BookingStatus::CANCELLED)
            ->whereBetween('cancelled_at', [$startDate, $endDate])
            ->where('penalty_amount', '>', 0)
            ->count();

        // Total outstanding across all partners
        $totalOutstanding = $this->calculateTotalOutstanding();
        $partnersWithBalance = Partner::active()->get()->filter(fn($p) => $p->outstanding_balance > 0)->count();

        // Partner balances (with filter)
        $balanceFilter = $request->input('balance_filter', 'outstanding');
        $partners = Partner::query()
            ->active()
            ->withCount('bookings')
            ->get()
            ->map(function ($partner) {
                $activeBilled = $partner->bookings()
                    ->whereNotIn('status', ['cancelled', 'expired', 'rejected'])
                    ->sum('total_amount');
                $penalties = $partner->bookings()
                    ->where('status', 'cancelled')
                    ->where('penalty_amount', '>', 0)
                    ->sum('penalty_amount');
                $partner->total_billed = (float) $activeBilled + (float) $penalties;
                $partner->total_paid = $partner->payments()->sum('amount');
                $partner->balance = $partner->outstanding_balance;
                return $partner;
            });

        if ($balanceFilter === 'outstanding') {
            $partners = $partners->filter(fn($p) => $p->balance > 0);
        } elseif ($balanceFilter === 'paid') {
            $partners = $partners->filter(fn($p) => $p->balance <= 0);
        }

        $partners = $partners->sortByDesc('balance')->values();

        // Recent transactions (payments + bookings + penalties)
        $recentPayments = Payment::query()
            ->with('partner')
            ->orderBy('paid_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($payment) {
                return [
                    'date' => $payment->paid_at?->toDateTimeString(),
                    'type' => $payment->method === 'credit' ? 'refund' : 'payment',
                    'partner_name' => $payment->partner?->name ?? 'Unknown',
                    'description' => $payment->notes ?? 'Payment received',
                    'method' => $payment->method,
                    'amount' => (float) $payment->amount,
                ];
            });

        $recentBookings = Booking::query()
            ->with('partner')
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($booking) {
                return [
                    'date' => $booking->created_at?->toDateTimeString(),
                    'type' => 'booking',
                    'partner_name' => $booking->partner?->name ?? 'Unknown',
                    'description' => $booking->booking_code,
                    'method' => null,
                    'amount' => -(float) $booking->total_amount,
                ];
            });

        $recentPenalties = Booking::query()
            ->with('partner')
            ->where('status', BookingStatus::CANCELLED)
            ->where('penalty_amount', '>', 0)
            ->orderBy('cancelled_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'date' => $booking->cancelled_at?->toDateTimeString(),
                    'type' => 'penalty',
                    'partner_name' => $booking->partner?->name ?? 'Unknown',
                    'description' => "No-show - {$booking->booking_code}",
                    'method' => null,
                    'amount' => -(float) $booking->penalty_amount,
                ];
            });

        $transactions = $recentPayments
            ->concat($recentBookings)
            ->concat($recentPenalties)
            ->sortByDesc('date')
            ->take(20)
            ->values();

        // For the payment modal dropdown
        $partnersForDropdown = Partner::active()
            ->orderBy('name')
            ->get()
            ->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'name' => $partner->name,
                    'outstanding' => $partner->outstanding_balance,
                ];
            });

        // Unpaid bookings for bulk payment marking
        $unpaidBookings = Booking::query()
            ->with(['partner', 'tourDeparture.tour'])
            ->whereHas('tourDeparture.tour')
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
            ->where('payment_status', '!=', PaymentStatus::PAID)
            ->orderBy('partner_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                $balanceDue = (float) $booking->total_amount - (float) $booking->payments()->sum('booking_payment.amount');
                return [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'partner_name' => $booking->partner?->name ?? 'Unknown',
                    'total_amount' => (float) $booking->total_amount,
                    'balance_due' => $balanceDue,
                ];
            })
            ->values();

        $unpaidBookingsCount = Booking::query()
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
            ->where('payment_status', '!=', PaymentStatus::PAID)
            ->count();

        return Inertia::render('admin/accounting/index', [
            'totalRevenue' => $totalRevenue,
            'totalPayments' => $totalPayments,
            'paymentCount' => $paymentCount,
            'totalPenalties' => $totalPenalties,
            'penaltyCount' => $penaltyCount,
            'totalOutstanding' => $totalOutstanding,
            'partnersWithBalance' => $partnersWithBalance,
            'partners' => $partners,
            'transactions' => $transactions,
            'partnersForDropdown' => $partnersForDropdown,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateType' => $dateType,
            'balanceFilter' => $balanceFilter,
            'unpaidBookings' => $unpaidBookings,
            'unpaidBookingsCount' => $unpaidBookingsCount,
            'filters' => $request->only(['start_date', 'end_date', 'date_type', 'balance_filter']),
        ]);
    }

    /**
     * Record a new payment.
     */
    public function storePayment(StorePaymentRequest $request, RecordPaymentAction $action): RedirectResponse
    {
        $payment = $action->execute(
            partnerId: (int) $request->validated('partner_id'),
            amount: (float) $request->validated('amount'),
            method: $request->validated('method'),
            paidAt: Carbon::parse($request->validated('paid_at')),
            notes: $request->validated('notes'),
            reference: $request->validated('reference'),
            bookingId: $request->validated('booking_id') ? (int) $request->validated('booking_id') : null
        );

        return redirect()
            ->route('admin.accounting.index')
            ->with('success', "Payment of €{$payment->amount} recorded for {$payment->partner->name}.");
    }

    /**
     * Record a credit/refund for a partner.
     */
    public function storeCredit(StoreCreditRequest $request, RecordPaymentAction $action): RedirectResponse
    {
        $payment = $action->execute(
            partnerId: (int) $request->validated('partner_id'),
            amount: (float) $request->validated('amount'),
            method: 'credit',
            paidAt: Carbon::parse($request->validated('date')),
            notes: $request->validated('notes'),
            reference: $request->validated('booking_code')
        );

        return redirect()
            ->route('admin.accounting.index')
            ->with('success', "Credit of €" . abs((float) $payment->amount) . " applied for {$payment->partner->name}.");
    }

    /**
     * Export accounting data to CSV.
     */
    public function export(Request $request): Response
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now()->endOfMonth();

        $type = $request->input('type', 'all');

        $filename = "accounting-{$type}-{$startDate->format('Y-m-d')}-to-{$endDate->format('Y-m-d')}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($startDate, $endDate, $type) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, ['Date', 'Type', 'Partner', 'Description', 'Method', 'Amount']);

            // Get data based on type
            if ($type === 'all' || $type === 'payments') {
                $payments = Payment::query()
                    ->with('partner')
                    ->whereBetween('paid_at', [$startDate, $endDate])
                    ->orderBy('paid_at', 'desc')
                    ->get();

                foreach ($payments as $payment) {
                    fputcsv($file, [
                        $payment->paid_at->format('Y-m-d'),
                        $payment->method === 'credit' ? 'Refund' : 'Payment',
                        $payment->partner->name,
                        $payment->notes ?? '-',
                        $payment->method ?? '-',
                        number_format($payment->amount, 2),
                    ]);
                }
            }

            if ($type === 'all' || $type === 'bookings') {
                $bookings = Booking::query()
                    ->with('partner')
                    ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($bookings as $booking) {
                    fputcsv($file, [
                        $booking->created_at->format('Y-m-d'),
                        'Booking',
                        $booking->partner->name,
                        $booking->booking_code,
                        '-',
                        number_format($booking->total_amount, 2),
                    ]);
                }
            }

            if ($type === 'all' || $type === 'penalties') {
                $penalties = Booking::query()
                    ->with('partner')
                    ->where('status', BookingStatus::CANCELLED)
                    ->where('penalty_amount', '>', 0)
                    ->whereBetween('cancelled_at', [$startDate, $endDate])
                    ->orderBy('cancelled_at', 'desc')
                    ->get();

                foreach ($penalties as $booking) {
                    fputcsv($file, [
                        $booking->cancelled_at->format('Y-m-d'),
                        'Penalty',
                        $booking->partner->name,
                        "No-show - {$booking->booking_code}",
                        '-',
                        number_format($booking->penalty_amount, 2),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export partner balances to CSV.
     */
    public function exportBalances(): Response
    {
        $filename = 'partner-balances-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, ['Partner', 'Type', 'Email', 'Total Billed', 'Total Paid', 'Outstanding Balance']);

            $partners = Partner::active()->orderBy('name')->get();

            foreach ($partners as $partner) {
                $activeBilled = $partner->bookings()
                    ->whereNotIn('status', ['cancelled', 'expired', 'rejected'])
                    ->sum('total_amount');
                $penalties = $partner->bookings()
                    ->where('status', 'cancelled')
                    ->where('penalty_amount', '>', 0)
                    ->sum('penalty_amount');
                $totalBilled = (float) $activeBilled + (float) $penalties;
                $totalPaid = $partner->payments()->sum('amount');

                fputcsv($file, [
                    $partner->name,
                    $partner->type->value,
                    $partner->email,
                    number_format($totalBilled, 2),
                    number_format($totalPaid, 2),
                    number_format($partner->outstanding_balance, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk mark multiple bookings as paid.
     */
    public function bulkMarkPaid(
        BulkMarkBookingsAsPaidRequest $request,
        BulkMarkBookingsAsPaidAction $action
    ): RedirectResponse {
        $result = $action->execute(
            bookingIds: $request->validated('booking_ids'),
            method: $request->validated('method'),
            paidAt: Carbon::parse($request->validated('paid_at')),
            notes: $request->validated('notes'),
            reference: $request->validated('reference')
        );

        if ($result['bookings_marked'] === 0) {
            return redirect()
                ->route('admin.accounting.index')
                ->with('warning', 'No bookings were marked as paid. They may already be paid or have no balance due.');
        }

        return redirect()
            ->route('admin.accounting.index')
            ->with('success', sprintf(
                '%d booking(s) marked as paid. Total: €%s across %d payment(s).',
                $result['bookings_marked'],
                number_format($result['total_amount'], 2),
                $result['payments_created']
            ));
    }

    /**
     * Calculate total outstanding balance across all partners.
     * Includes active booking amounts + penalties from cancelled bookings.
     */
    private function calculateTotalOutstanding(): float
    {
        $totalBilled = Booking::query()
            ->whereNotIn('status', [BookingStatus::CANCELLED, BookingStatus::EXPIRED, BookingStatus::REJECTED])
            ->sum('total_amount');

        $totalPenalties = Booking::query()
            ->where('status', BookingStatus::CANCELLED)
            ->where('penalty_amount', '>', 0)
            ->sum('penalty_amount');

        $totalPaid = Payment::sum('amount');

        return (float) ($totalBilled + $totalPenalties - $totalPaid);
    }
}
