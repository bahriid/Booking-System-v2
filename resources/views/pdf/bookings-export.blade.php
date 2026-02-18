<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('pdf.bookings_export_title') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
        }

        .container {
            padding: 15px;
        }

        .header {
            border-bottom: 3px solid #009ef7;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #009ef7;
        }

        .report-title {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }

        .generated-at {
            font-size: 9px;
            color: #7e8299;
        }

        .filters-bar {
            background-color: #f5f8fa;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 9px;
            color: #7e8299;
        }

        .filters-bar strong {
            color: #181c32;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 8px;
            background-color: #f5f8fa;
            border-right: 2px solid #fff;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #181c32;
        }

        .summary-label {
            font-size: 8px;
            color: #7e8299;
            text-transform: uppercase;
            margin-top: 2px;
        }

        table.bookings {
            width: 100%;
            border-collapse: collapse;
        }

        table.bookings th {
            background-color: #009ef7;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }

        table.bookings td {
            padding: 5px 8px;
            border-bottom: 1px solid #e4e6ef;
            font-size: 9px;
        }

        table.bookings tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-confirmed {
            background-color: #e8fff3;
            color: #50cd89;
        }

        .status-suspended_request {
            background-color: #fff8dd;
            color: #ffc700;
        }

        .status-cancelled {
            background-color: #fff5f8;
            color: #f1416c;
        }

        .status-rejected {
            background-color: #fff5f8;
            color: #f1416c;
        }

        .status-expired {
            background-color: #f8f5ff;
            color: #7239ea;
        }

        .status-completed {
            background-color: #f1faff;
            color: #009ef7;
        }

        .pax-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
        }

        .pax-adult {
            background-color: #f1faff;
            color: #009ef7;
        }

        .pax-child {
            background-color: #fff8dd;
            color: #ffc700;
        }

        .pax-infant {
            background-color: #f5f8fa;
            color: #7e8299;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e4e6ef;
            text-align: center;
            color: #7e8299;
            font-size: 8px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="logo">MagShip</div>
                    <div class="report-title">{{ __('pdf.bookings_export_title') }}</div>
                </div>
                <div class="header-right">
                    <div class="generated-at">{{ __('pdf.generated_on', ['date' => now()->format('d/m/Y H:i')]) }}</div>
                    <div class="generated-at">{{ __('pdf.total_records', ['count' => $bookings->count()]) }}</div>
                </div>
            </div>
        </div>

        <!-- Active Filters -->
        @php
            $activeFilters = collect($filters)->filter()->isNotEmpty();
        @endphp
        @if($activeFilters)
        <div class="filters-bar">
            {{ __('pdf.active_filters') }}:
            @if($filters['search'])
                <strong>{{ __('pdf.search') }}:</strong> {{ $filters['search'] }}
            @endif
            @if($filters['status'])
                <strong>{{ __('pdf.status') }}:</strong> {{ ucfirst(str_replace('_', ' ', $filters['status'])) }}
            @endif
            @if($filters['date_from'])
                <strong>{{ __('pdf.from') }}:</strong> {{ $filters['date_from'] }}
            @endif
            @if($filters['date_to'])
                <strong>{{ __('pdf.to') }}:</strong> {{ $filters['date_to'] }}
            @endif
        </div>
        @endif

        <!-- Summary -->
        @php
            $totalAmount = $bookings->sum('total_amount');
            $totalPax = $bookings->sum(fn ($b) => $b->passengers->count());
            $confirmedCount = $bookings->where('status', \App\Enums\BookingStatus::CONFIRMED)->count();
            $pendingCount = $bookings->where('status', \App\Enums\BookingStatus::SUSPENDED_REQUEST)->count();
            $cancelledCount = $bookings->where('status', \App\Enums\BookingStatus::CANCELLED)->count();
        @endphp
        <div class="summary-row">
            <div class="summary-item">
                <div class="summary-value">{{ $bookings->count() }}</div>
                <div class="summary-label">{{ __('pdf.total_bookings') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $confirmedCount }}</div>
                <div class="summary-label">{{ __('pdf.confirmed') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $pendingCount }}</div>
                <div class="summary-label">{{ __('pdf.pending') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $cancelledCount }}</div>
                <div class="summary-label">{{ __('pdf.cancelled') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $totalPax }}</div>
                <div class="summary-label">{{ __('pdf.total_pax') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">&euro;{{ number_format($totalAmount, 2) }}</div>
                <div class="summary-label">{{ __('pdf.total_amount') }}</div>
            </div>
        </div>

        <!-- Bookings Table -->
        <table class="bookings">
            <thead>
                <tr>
                    <th>{{ __('pdf.code') }}</th>
                    <th>{{ __('pdf.status') }}</th>
                    <th>{{ __('pdf.partner') }}</th>
                    <th>{{ __('pdf.tour') }}</th>
                    <th>{{ __('pdf.date') }}</th>
                    <th>{{ __('pdf.time') }}</th>
                    <th class="text-center">{{ __('pdf.pax') }}</th>
                    <th class="text-right">{{ __('pdf.amount') }}</th>
                    <th>{{ __('pdf.payment') }}</th>
                    <th>{{ __('pdf.passengers') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                @php
                    $paxCounts = $booking->passengers->groupBy('pax_type')->map->count();
                    $paxParts = [];
                    if (isset($paxCounts['adult'])) $paxParts[] = $paxCounts['adult'] . 'A';
                    if (isset($paxCounts['child'])) $paxParts[] = $paxCounts['child'] . 'C';
                    if (isset($paxCounts['infant'])) $paxParts[] = $paxCounts['infant'] . 'I';
                @endphp
                <tr>
                    <td class="fw-bold" style="white-space: nowrap;">{{ $booking->booking_code }}</td>
                    <td>
                        <span class="status-badge status-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
                    </td>
                    <td>{{ Str::limit($booking->partner->name ?? '', 18) }}</td>
                    <td>{{ Str::limit($booking->tourDeparture?->tour?->name ?? 'N/A', 18) }}</td>
                    <td style="white-space: nowrap;">{{ $booking->tourDeparture?->date?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $booking->tourDeparture?->time ?? '-' }}</td>
                    <td class="text-center">{{ implode(' ', $paxParts) ?: '-' }}</td>
                    <td class="text-right fw-bold">&euro;{{ number_format($booking->total_amount, 2) }}</td>
                    <td>{{ $booking->payment_status->label() }}</td>
                    <td>{{ Str::limit($booking->passengers->map(fn ($p) => $p->first_name . ' ' . $p->last_name)->implode(', '), 30) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">{{ __('pdf.no_bookings') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>{{ __('pdf.bookings_export_footer', ['date' => now()->format('d/m/Y H:i')]) }}</p>
        </div>
    </div>
</body>
</html>
