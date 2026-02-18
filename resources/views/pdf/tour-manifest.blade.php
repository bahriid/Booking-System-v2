<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('pdf.manifest_title', ['code' => $tour?->code ?? '-', 'date' => $departure->date->format('Y-m-d')]) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
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
            vertical-align: top;
            width: 60%;
        }

        .header-right {
            display: table-cell;
            text-align: right;
            vertical-align: top;
            width: 40%;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #009ef7;
        }

        .manifest-title {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }

        .tour-name {
            font-size: 16px;
            font-weight: bold;
            color: #181c32;
            margin-top: 8px;
        }

        .tour-code {
            font-size: 11px;
            color: #7e8299;
        }

        .date-box {
            background-color: #009ef7;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            text-align: center;
            display: inline-block;
        }

        .date-day {
            font-size: 24px;
            font-weight: bold;
            line-height: 1;
        }

        .date-month {
            font-size: 11px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .date-time {
            font-size: 12px;
            margin-top: 5px;
            font-weight: bold;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            background-color: #f5f8fa;
            border-radius: 6px;
        }

        .summary-cell {
            display: table-cell;
            padding: 10px 15px;
            text-align: center;
            border-right: 1px solid #e4e6ef;
        }

        .summary-cell:last-child {
            border-right: none;
        }

        .summary-label {
            font-size: 9px;
            color: #7e8299;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #181c32;
        }

        .summary-detail {
            font-size: 9px;
            color: #7e8299;
            margin-top: 2px;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #009ef7;
            text-transform: uppercase;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e4e6ef;
        }

        .pickup-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .pickup-row {
            display: table-row;
        }

        .pickup-cell {
            display: table-cell;
            padding: 6px 10px;
            border-bottom: 1px solid #f5f8fa;
        }

        .pickup-time {
            width: 60px;
            font-weight: bold;
            color: #009ef7;
        }

        .pickup-name {
            font-weight: 600;
        }

        .pickup-count {
            width: 60px;
            text-align: right;
            color: #7e8299;
        }

        table.passengers {
            width: 100%;
            border-collapse: collapse;
        }

        table.passengers th {
            background-color: #009ef7;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        table.passengers td {
            padding: 6px 8px;
            border-bottom: 1px solid #e4e6ef;
            font-size: 10px;
            vertical-align: top;
        }

        table.passengers tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table.passengers tr:last-child td {
            border-bottom: none;
        }

        .pax-badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .pax-adult {
            background-color: #e8f4ff;
            color: #009ef7;
        }

        .pax-child {
            background-color: #fff8dd;
            color: #f5a623;
        }

        .pax-infant {
            background-color: #f5f5f5;
            color: #7e8299;
        }

        .allergies {
            color: #f1416c;
            font-weight: bold;
            font-size: 9px;
        }

        .alert-box {
            background-color: #fff5f8;
            border: 1px solid #f1416c;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .alert-title {
            color: #f1416c;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
        }

        .alert-list {
            color: #333;
            font-size: 10px;
        }

        .notes-box {
            background-color: #f5f8fa;
            border-radius: 4px;
            padding: 10px;
            margin-top: 15px;
        }

        .notes-title {
            font-size: 10px;
            font-weight: bold;
            color: #7e8299;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .notes-text {
            font-size: 10px;
            color: #333;
        }

        .driver-box {
            background-color: #e8fff3;
            border-left: 4px solid #50cd89;
            padding: 8px 12px;
            margin-bottom: 15px;
        }

        .driver-label {
            font-size: 9px;
            color: #7e8299;
            text-transform: uppercase;
        }

        .driver-name {
            font-size: 12px;
            font-weight: bold;
            color: #181c32;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e4e6ef;
            text-align: center;
            color: #7e8299;
            font-size: 8px;
        }

        .partner-ref {
            font-size: 9px;
            color: #7e8299;
        }

        .booking-code {
            font-size: 9px;
            color: #009ef7;
            font-weight: 600;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #999;
            border-radius: 2px;
            margin-right: 5px;
            vertical-align: middle;
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
                    <div class="manifest-title">{{ __('pdf.tour_manifest') }}</div>
                    <div class="tour-name">{{ $tour?->name ?? 'N/A' }}</div>
                    <div class="tour-code">{{ $tour?->code ?? '-' }}</div>
                </div>
                <div class="header-right">
                    <div class="date-box">
                        <div class="date-day">{{ $departure->date->format('d') }}</div>
                        <div class="date-month">{{ $departure->date->format('M Y') }}</div>
                        <div class="date-time">{{ $departure->time }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Driver Info -->
        @if($driver)
        <div class="driver-box">
            <div class="driver-label">{{ __('pdf.assigned_driver') }}</div>
            <div class="driver-name">{{ $driver->name }}</div>
        </div>
        @endif

        <!-- Summary -->
        <div class="summary-row">
            <div class="summary-cell">
                <div class="summary-label">{{ __('pdf.total_passengers') }}</div>
                <div class="summary-value">{{ $passengers->count() }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">{{ __('pdf.adults') }}</div>
                <div class="summary-value">{{ $paxCounts['adult'] ?? 0 }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">{{ __('pdf.children') }}</div>
                <div class="summary-value">{{ $paxCounts['child'] ?? 0 }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">{{ __('pdf.infants') }}</div>
                <div class="summary-value">{{ $paxCounts['infant'] ?? 0 }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">{{ __('pdf.bookings') }}</div>
                <div class="summary-value">{{ $bookingsCount }}</div>
            </div>
        </div>

        <!-- Allergies Alert -->
        @if($allergies->isNotEmpty())
        <div class="alert-box">
            <div class="alert-title">{{ __('pdf.allergy_alert') }}</div>
            <div class="alert-list">
                @foreach($allergies as $passenger)
                    <strong>{{ $passenger->first_name }} {{ $passenger->last_name }}:</strong> {{ $passenger->allergies }}@if(!$loop->last), @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- Pickup Points Summary -->
        <div class="section">
            <div class="section-title">{{ __('pdf.pickup_schedule') }}</div>
            <div class="pickup-grid">
                @foreach($pickupSummary as $pickupName => $data)
                <div class="pickup-row">
                    <div class="pickup-cell pickup-time">{{ $data['time'] }}</div>
                    <div class="pickup-cell pickup-name">{{ $pickupName }}</div>
                    <div class="pickup-cell pickup-count">{{ $data['count'] }} {{ __('pdf.pax') }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Passenger List -->
        <div class="section">
            <div class="section-title">{{ __('pdf.passenger_list') }}</div>
            <table class="passengers">
                <thead>
                    <tr>
                        <th style="width: 20px;"></th>
                        <th style="width: 25px;">#</th>
                        <th>{{ __('pdf.name') }}</th>
                        <th style="width: 35px;">{{ __('pdf.type') }}</th>
                        <th>{{ __('pdf.pickup') }}</th>
                        <th>{{ __('pdf.phone') }}</th>
                        <th>{{ __('pdf.partner_booking') }}</th>
                        <th>{{ __('pdf.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($passengers as $index => $passenger)
                    <tr>
                        <td><span class="checkbox"></span></td>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $passenger->first_name }} {{ $passenger->last_name }}</td>
                        <td>
                            @php
                                $paxClass = match($passenger->pax_type->value) {
                                    'adult' => 'pax-adult',
                                    'child' => 'pax-child',
                                    'infant' => 'pax-infant',
                                    default => '',
                                };
                            @endphp
                            <span class="pax-badge {{ $paxClass }}">{{ $passenger->pax_type->shortCode() }}</span>
                        </td>
                        <td>
                            @if($passenger->pickupPoint)
                                <strong>{{ $passenger->pickupPoint->default_time }}</strong><br>
                                <span style="font-size: 9px; color: #7e8299;">{{ $passenger->pickupPoint->name }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $passenger->phone ?: '-' }}</td>
                        <td>
                            <span class="partner-ref">{{ $passenger->partner_name }}</span><br>
                            <span class="booking-code">{{ $passenger->booking_code }}</span>
                        </td>
                        <td>
                            @if($passenger->allergies)
                                <span class="allergies">{{ $passenger->allergies }}</span>
                            @endif
                            @if($passenger->allergies && $passenger->notes)
                                <br>
                            @endif
                            @if($passenger->notes)
                                <span style="font-size: 9px;">{{ $passenger->notes }}</span>
                            @endif
                            @if(!$passenger->allergies && !$passenger->notes)
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Departure Notes -->
        @if($departure->notes)
        <div class="notes-box">
            <div class="notes-title">{{ __('pdf.departure_notes') }}</div>
            <div class="notes-text">{{ $departure->notes }}</div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>{{ __('pdf.generated_on', ['date' => now()->format('d/m/Y H:i')]) }} | {{ __('pdf.tourbook_footer') }}</p>
        </div>
    </div>
</body>
</html>
