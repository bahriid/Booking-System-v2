@extends('emails.layouts.base')

@section('title', __('emails.new_booking_received_subject', ['code' => $booking->booking_code]))

@section('content')
<div class="email-header" style="background-color: #50cd89;">
    <h1>{{ __('emails.new_booking_received_header') }}</h1>
    <div class="subtitle">{{ __('emails.new_booking_received_subtitle') }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.admin_alert') }}</div>

    <div class="alert alert-success">
        <strong>{{ __('emails.new_booking_alert') }}</strong> {{ __('emails.new_booking_alert_text', ['partner' => $partner->name]) }}
    </div>

    <div class="booking-code">{{ $booking->booking_code }}</div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.tour_details') }}</div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.partner') }}</span>
            <span class="info-value">{{ $partner->name }}</span>
        </div>
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
        <div class="info-row">
            <span class="info-label">{{ __('emails.pax') }}</span>
            <span class="info-value">{{ $passengers->count() }} {{ __('emails.pax') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.total_amount') }}</span>
            <span class="info-value">&euro;{{ number_format($booking->total_amount, 2) }}</span>
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

    <div class="divider"></div>

    <p style="text-align: center; margin-bottom: 25px;">
        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-primary">{{ __('emails.view_booking_details') }}</a>
    </p>
</div>
@endsection
