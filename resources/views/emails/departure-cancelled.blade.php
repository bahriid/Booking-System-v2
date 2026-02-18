@extends('emails.layouts.base')

@section('title', __('emails.departure_cancelled_title', ['code' => $booking->booking_code]))

@section('content')
<div class="email-header" style="background-color: {{ $isBadWeather ? '#ffc700' : '#f1416c' }};">
    <h1>{{ $isBadWeather ? __('emails.departure_cancelled_header_weather') : __('emails.departure_cancelled_header') }}</h1>
    <div class="subtitle">{{ __('emails.departure_cancelled_subtitle') }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.hello', ['name' => $partner->name]) }}</div>

    <div class="message">
        @if($isBadWeather)
            {{ __('emails.departure_cancelled_message_weather') }}
        @else
            {{ __('emails.departure_cancelled_message') }}
        @endif
    </div>

    @if($isBadWeather)
    <div class="alert alert-warning">
        <strong>{{ __('emails.weather_refund') }}</strong> {{ __('emails.weather_refund_text') }}
    </div>
    @else
    <div class="alert alert-danger">
        <strong>{{ __('emails.tour_cancelled_alert') }}</strong> {{ __('emails.tour_cancelled_alert_text') }}
        @if($reason)
            <br><strong>{{ __('emails.reason') }}:</strong> {{ $reason }}
        @endif
    </div>
    @endif

    <div class="booking-code" style="background-color: {{ $isBadWeather ? '#fff8dd' : '#fff5f8' }}; color: {{ $isBadWeather ? '#ffc700' : '#f1416c' }};">{{ $booking->booking_code }}</div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.cancelled_departure_details') }}</div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.tour') }}</span>
            <span class="info-value">{{ $tour?->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.original_date') }}</span>
            <span class="info-value">{{ $departure->date->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.original_time') }}</span>
            <span class="info-value">{{ $departure->time }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.status') }}</span>
            <span class="info-value"><span class="status-badge status-cancelled">{{ __('emails.cancelled_status') }}</span></span>
        </div>
    </div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.affected_passengers', ['count' => $passengers->count()]) }}</div>
        <table class="passengers-table">
            <thead>
                <tr>
                    <th>{{ __('emails.name') }}</th>
                    <th>{{ __('emails.type') }}</th>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.whats_next_header') }}</div>
        <div style="padding: 10px 15px;">
            @if($isBadWeather)
                <p style="margin: 0 0 10px 0;"><strong>{{ __('emails.weather_step_1') }}</strong> {{ __('emails.weather_step_1_text', ['amount' => '€' . number_format($booking->total_amount, 2)]) }}</p>
                <p style="margin: 0 0 10px 0;"><strong>{{ __('emails.weather_step_2') }}</strong> {{ __('emails.weather_step_2_text') }}</p>
                <p style="margin: 0;"><strong>{{ __('emails.weather_step_3') }}</strong> {{ __('emails.weather_step_3_text') }}</p>
            @else
                <p style="margin: 0 0 10px 0;"><strong>{{ __('emails.normal_step_1') }}</strong> {{ __('emails.normal_step_1_text') }}</p>
                <p style="margin: 0 0 10px 0;"><strong>{{ __('emails.normal_step_2') }}</strong> {{ __('emails.normal_step_2_text') }}</p>
                <p style="margin: 0;"><strong>{{ __('emails.normal_step_3') }}</strong> {{ __('emails.normal_step_3_text') }}</p>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; margin-bottom: 25px;">
        <a href="{{ route('partner.bookings.create') }}" class="btn btn-primary">{{ __('emails.book_another_tour') }}</a>
    </p>
</div>
@endsection
