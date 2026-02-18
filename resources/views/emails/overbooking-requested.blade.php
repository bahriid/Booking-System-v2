@extends('emails.layouts.base')

@section('title', __('emails.requested_title', ['code' => $booking->booking_code]))

@section('content')
<div class="email-header" style="background-color: #ffc700;">
    <h1>{{ __('emails.requested_header') }}</h1>
    <div class="subtitle">{{ __('emails.requested_subtitle') }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.admin_alert') }}</div>

    <div class="alert alert-warning">
        <strong>{{ __('emails.urgent') }}</strong> {{ __('emails.urgent_text') }}
        @if($expiresAt)
            <br>{{ __('emails.request_expires', ['date' => $expiresAt->format('d/m/Y H:i')]) }}
        @endif
    </div>

    <div class="booking-code">{{ $booking->booking_code }}</div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.request_details') }}</div>
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
    </div>

    <div class="info-box" style="background-color: #fff5f8;">
        <div class="info-box-header" style="color: #f1416c;">{{ __('emails.capacity_analysis') }}</div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.current_capacity') }}</span>
            <span class="info-value">{{ $currentCapacity }} {{ __('emails.seats') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.already_booked') }}</span>
            <span class="info-value">{{ $bookedSeats }} {{ __('emails.seats') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.this_request') }}</span>
            <span class="info-value" style="color: #f1416c;">+{{ $requestedSeats }} {{ __('emails.seats') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.if_approved') }}</span>
            <span class="info-value" style="color: #f1416c;">{{ $bookedSeats + $requestedSeats }} / {{ $currentCapacity }}</span>
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
        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-primary">{{ __('emails.review_respond') }}</a>
    </p>
</div>
@endsection
