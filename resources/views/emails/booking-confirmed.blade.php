@extends('emails.layouts.base')

@section('title', __('emails.confirmed_title', ['code' => $booking->booking_code]))

@section('content')
<div class="email-header">
    <h1>{{ __('emails.confirmed_header') }}</h1>
    <div class="subtitle">{{ __('emails.confirmed_subtitle') }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.hello', ['name' => $partner->name]) }}</div>

    <div class="message">
        {{ __('emails.confirmed_message') }}
    </div>

    <div class="booking-code">{{ $booking->booking_code }}</div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.tour_details') }}</div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.tour') }}</span>
            <span class="info-value">{{ $tour?->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.date') }}</span>
            <span class="info-value">{{ $departure->date->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.time') }}</span>
            <span class="info-value">{{ $departure->time }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.status') }}</span>
            <span class="info-value"><span class="status-badge status-confirmed">{{ __('emails.confirmed_status') }}</span></span>
        </div>
    </div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.passengers', ['count' => $passengers->count()]) }}</div>
        <table class="passengers-table">
            <thead>
                <tr>
                    <th>{{ __('emails.name') }}</th>
                    <th>{{ __('emails.type') }}</th>
                    <th>{{ __('emails.pickup') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($passengers as $passenger)
                <tr>
                    <td>{{ $passenger->first_name }} {{ $passenger->last_name }}</td>
                    <td>
                        @php
                            $paxClass = match($passenger->pax_type->value) {
                                'adult' => 'pax-adult',
                                'child' => 'pax-child',
                                'infant' => 'pax-infant',
                                default => '',
                            };
                        @endphp
                        <span class="pax-badge {{ $paxClass }}">{{ strtoupper(substr($passenger->pax_type->value, 0, 3)) }}</span>
                    </td>
                    <td>{{ $passenger->pickupPoint?->name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @php
        $firstPassenger = $passengers->first();
        $pickupPoint = $firstPassenger?->pickupPoint;
    @endphp
    @if($pickupPoint)
    <div class="alert alert-info">
        <strong>{{ __('emails.pickup_information') }}</strong><br>
        {{ __('emails.pickup_info_text', ['pickup' => $pickupPoint->name . ($pickupPoint->location ? ' (' . $pickupPoint->location . ')' : ''), 'time' => $pickupPoint->default_time]) }}
    </div>
    @endif

    <div class="divider"></div>

    <p style="text-align: center; margin-bottom: 25px;">
        <a href="{{ route('partner.bookings.show', $booking) }}" class="btn btn-primary">{{ __('emails.view_booking_details') }}</a>
    </p>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.billing_summary') }}</div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.total_amount') }}</span>
            <span class="info-value">€{{ number_format($booking->total_amount, 2) }}</span>
        </div>
    </div>
</div>
@endsection
