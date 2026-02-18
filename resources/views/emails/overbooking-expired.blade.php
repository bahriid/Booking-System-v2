@extends('emails.layouts.base')

@section('title', __('emails.expired_title', ['code' => $booking->booking_code]))

@section('content')
<div class="email-header" style="background-color: #7239ea;">
    <h1>{{ __('emails.expired_header') }}</h1>
    <div class="subtitle">{{ __('emails.expired_subtitle') }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.hello', ['name' => $partner->name]) }}</div>

    <div class="message">
        {{ __('emails.expired_message') }}
    </div>

    <div class="alert alert-warning">
        <strong>{{ __('emails.expired_alert') }}</strong> {{ __('emails.expired_alert_text') }}
    </div>

    <div class="booking-code" style="background-color: #f8f5ff; color: #7239ea;">{{ $booking->booking_code }}</div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.request_details') }}</div>
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
            <span class="info-value"><span class="status-badge status-expired">{{ __('emails.expired_status') }}</span></span>
        </div>
    </div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.requested_passengers', ['count' => $passengers->count()]) }}</div>
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

    <div class="alert alert-info">
        <strong>{{ __('emails.whats_next') }}</strong><br>
        {{ __('emails.expired_whats_next_text') }}
    </div>

    <div class="divider"></div>

    <p style="text-align: center; margin-bottom: 25px;">
        <a href="{{ route('partner.bookings.create') }}" class="btn btn-primary">{{ __('emails.create_new_booking') }}</a>
    </p>
</div>
@endsection
