<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('pdf.voucher_title', ['code' => $booking->booking_code]) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        .header {
            border-bottom: 3px solid #009ef7;
            padding-bottom: 15px;
            margin-bottom: 20px;
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
            font-size: 24px;
            font-weight: bold;
            color: #009ef7;
        }

        .voucher-title {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .booking-code {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .status-confirmed {
            background-color: #e8fff3;
            color: #50cd89;
        }

        .status-completed {
            background-color: #f1faff;
            color: #009ef7;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #009ef7;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e4e6ef;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 8px 0;
            border-bottom: 1px solid #f5f8fa;
        }

        .info-label {
            width: 140px;
            color: #7e8299;
            font-weight: 500;
        }

        .info-value {
            font-weight: 600;
            color: #181c32;
        }

        .tour-box {
            background-color: #f5f8fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .tour-name {
            font-size: 16px;
            font-weight: bold;
            color: #181c32;
            margin-bottom: 5px;
        }

        .tour-code {
            font-size: 11px;
            color: #7e8299;
        }

        .tour-datetime {
            margin-top: 10px;
            display: table;
            width: 100%;
        }

        .datetime-item {
            display: table-cell;
            width: 50%;
        }

        .datetime-label {
            font-size: 10px;
            color: #7e8299;
            text-transform: uppercase;
        }

        .datetime-value {
            font-size: 14px;
            font-weight: bold;
            color: #181c32;
        }

        table.passengers {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.passengers th {
            background-color: #f5f8fa;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            color: #7e8299;
            text-transform: uppercase;
            border-bottom: 1px solid #e4e6ef;
        }

        table.passengers td {
            padding: 10px;
            border-bottom: 1px solid #f5f8fa;
            font-size: 11px;
        }

        table.passengers tr:last-child td {
            border-bottom: none;
        }

        .pax-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
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

        .allergies {
            color: #f1416c;
            font-weight: bold;
        }

        .pickup-box {
            background-color: #fff8dd;
            border-left: 4px solid #ffc700;
            padding: 12px 15px;
            margin-bottom: 20px;
        }

        .pickup-title {
            font-size: 10px;
            font-weight: bold;
            color: #7e8299;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .pickup-info {
            font-size: 12px;
            font-weight: bold;
            color: #181c32;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e4e6ef;
            text-align: center;
            color: #7e8299;
            font-size: 9px;
        }

        .qr-placeholder {
            width: 80px;
            height: 80px;
            border: 1px dashed #ccc;
            display: inline-block;
            text-align: center;
            line-height: 80px;
            color: #999;
            font-size: 9px;
        }

        .partner-info {
            margin-top: 10px;
            padding: 10px;
            background-color: #f5f8fa;
            border-radius: 4px;
        }

        .partner-label {
            font-size: 9px;
            color: #7e8299;
            text-transform: uppercase;
        }

        .partner-name {
            font-size: 12px;
            font-weight: bold;
            color: #181c32;
        }

        .notes-box {
            background-color: #f1faff;
            border-left: 4px solid #009ef7;
            padding: 12px 15px;
            margin-top: 15px;
        }

        .notes-title {
            font-size: 10px;
            font-weight: bold;
            color: #009ef7;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .notes-text {
            font-size: 11px;
            color: #333;
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
                    <div class="voucher-title">{{ __('pdf.booking_voucher') }}</div>
                </div>
                <div class="header-right">
                    <div class="booking-code">{{ $booking->booking_code }}</div>
                    <div class="status-badge status-{{ $booking->status->value }}">
                        {{ $booking->status->label() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Tour Information -->
        <div class="tour-box">
            <div class="tour-name">{{ $tour?->name ?? 'N/A' }}</div>
            <div class="tour-code">{{ __('pdf.tour_code', ['code' => $tour?->code ?? '-']) }}</div>
            <div class="tour-datetime">
                <div class="datetime-item">
                    <div class="datetime-label">{{ __('pdf.date') }}</div>
                    <div class="datetime-value">{{ $departure->date->format('d/m/Y') }}</div>
                </div>
                <div class="datetime-item">
                    <div class="datetime-label">{{ __('pdf.departure_time') }}</div>
                    <div class="datetime-value">{{ $departure->time }}</div>
                </div>
            </div>
        </div>

        <!-- Pickup Information -->
        @php
            $firstPassenger = $passengers->first();
            $pickupPoint = $firstPassenger?->pickupPoint;
        @endphp
        @if($pickupPoint)
        <div class="pickup-box">
            <div class="pickup-title">{{ __('pdf.pickup_location') }}</div>
            <div class="pickup-info">
                {{ $pickupPoint->name }}
                @if($pickupPoint->location)
                    - {{ $pickupPoint->location }}
                @endif
            </div>
            <div style="margin-top: 5px; font-size: 11px; color: #7e8299;">
                {{ __('pdf.pickup_time') }}: <strong>{{ $pickupPoint->default_time }}</strong>
            </div>
        </div>
        @endif

        <!-- Passenger List -->
        <div class="section">
            <div class="section-title">{{ __('pdf.passengers', ['count' => $passengers->count()]) }}</div>
            <table class="passengers">
                <thead>
                    <tr>
                        <th style="width: 30px;">#</th>
                        <th>{{ __('pdf.name') }}</th>
                        <th style="width: 60px;">{{ __('pdf.type') }}</th>
                        <th>{{ __('pdf.phone') }}</th>
                        <th>{{ __('pdf.allergies_notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($passengers as $index => $passenger)
                    <tr>
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
                        <td>{{ $passenger->phone ?: '-' }}</td>
                        <td>
                            @if($passenger->allergies)
                                <span class="allergies">{{ $passenger->allergies }}</span>
                            @endif
                            @if($passenger->notes)
                                {{ $passenger->notes }}
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

        <!-- Partner Information -->
        <div class="partner-info">
            <div class="partner-label">{{ __('pdf.booked_by') }}</div>
            <div class="partner-name">{{ $partner->name }}</div>
        </div>

        <!-- Important Notes -->
        <div class="notes-box">
            <div class="notes-title">{{ __('pdf.important_information') }}</div>
            <div class="notes-text">
                {{ __('pdf.voucher_important_text') }}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ __('pdf.voucher_generated_on', ['date' => now()->format('d/m/Y H:i')]) }}</p>
            <p>{{ __('pdf.tourbook_footer') }}</p>
        </div>
    </div>
</body>
</html>
