@extends('emails.layouts.base')

@section('title', __('emails.cancelled_title', ['code' => $booking->booking_code]))

@section('content')
<div class="email-header" style="background-color: #7239ea;">
    <h1>{{ __('emails.cancelled_header') }}</h1>
    <div class="subtitle">{{ __('emails.cancelled_subtitle') }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.hello', ['name' => $partner->name]) }}</div>

    <div class="message">
        {{ __('emails.cancelled_message') }}
    </div>

    @if($hasPenalty)
    <div class="alert alert-danger">
        <strong>{{ __('emails.cancellation_penalty_applied') }}</strong><br>
        {{ __('emails.cancellation_penalty_text', ['amount' => number_format($penaltyAmount, 2)]) }}
    </div>
    @else
    <div class="alert alert-success">
        <strong>{{ __('emails.free_cancellation') }}</strong><br>
        {{ __('emails.free_cancellation_text') }}
    </div>
    @endif

    <div class="booking-code" style="background-color: #f5f8fa; color: #7e8299; text-decoration: line-through;">{{ $booking->booking_code }}</div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.cancelled_booking_details') }}</div>
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
            <span class="info-value"><span class="status-badge status-cancelled">{{ __('emails.cancelled_status') }}</span></span>
        </div>
    </div>

    @if($reason)
    <div class="info-box">
        <div class="info-box-header">{{ __('emails.cancellation_reason') }}</div>
        <p style="margin: 0; color: #5e6278;">{{ $reason }}</p>
    </div>
    @endif

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.cancelled_passengers', ['count' => $passengers->count()]) }}</div>
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

    @if($hasPenalty)
    <div class="info-box" style="background-color: #fff5f8;">
        <div class="info-box-header" style="color: #f1416c;">{{ __('emails.account_impact') }}</div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.penalty_amount') }}</span>
            <span class="info-value" style="color: #f1416c;">€{{ number_format($penaltyAmount, 2) }}</span>
        </div>
        <p style="margin-top: 10px; font-size: 13px; color: #7e8299;">
            {{ __('emails.penalty_added_to_balance') }}
        </p>
    </div>
    @endif

    <div class="divider"></div>

    <p style="text-align: center; margin-bottom: 25px;">
        <a href="{{ route('partner.bookings.create') }}" class="btn btn-primary">{{ __('emails.create_new_booking') }}</a>
    </p>
</div>
@endsection
