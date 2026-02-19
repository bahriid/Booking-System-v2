<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class EmailLogController extends Controller
{
    /**
     * Display a listing of email logs.
     */
    public function index(Request $request): InertiaResponse
    {
        $query = EmailLog::query()
            ->with('booking')
            ->orderBy('sent_at', 'desc');

        // Filter by event type
        if ($request->filled('event_type')) {
            $query->ofType($request->event_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->successful();
            } elseif ($request->status === 'failed') {
                $query->failed();
            }
        }

        // Filter by booking code
        if ($request->filled('booking_code')) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->booking_code . '%');
            });
        }

        // Filter by email
        if ($request->filled('email')) {
            $query->where('to_email', 'like', '%' . $request->email . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('sent_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sent_at', '<=', $request->date_to);
        }

        $emailLogs = $query->paginate(20)->withQueryString();

        // Get event types for filter dropdown
        $eventTypes = [
            'booking_confirmed' => 'Booking Confirmed',
            'overbooking_requested' => 'Overbooking Requested',
            'overbooking_approved' => 'Overbooking Approved',
            'overbooking_rejected' => 'Overbooking Rejected',
            'booking_cancelled' => 'Booking Cancelled',
        ];

        return Inertia::render('admin/email-logs/index', [
            'emailLogs' => $emailLogs,
            'eventTypes' => $eventTypes,
            'filters' => $request->only(['event_type', 'status', 'booking_code', 'email', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Display email log details.
     */
    public function show(EmailLog $emailLog): InertiaResponse
    {
        $emailLog->load('booking');

        return Inertia::render('admin/email-logs/show', compact('emailLog'));
    }
}
