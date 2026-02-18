@extends('emails.layouts.base')

@section('title', __('emails.rejected_title', ['code' => $booking->booking_code]))

@section('content')
<div class="email-header" style="background-color: #f1416c;">
    <h1>{{ __('emails.rejected_header') }}</h1>
    <div class="subtitle">{{ __('emails.rejected_subtitle') }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.hello', ['name' => $partner->name]) }}</div>

    <div class="message">
        {{ __('emails.rejected_message') }}
    </div>

    <div class="alert alert-danger">
        <strong>{{ __('emails.rejected_alert') }}</strong> {{ __('emails.rejected_alert_text') }}
        @if($reason)
            <br><strong>{{ __('emails.reason') }}:</strong> {{ $reason }}
        @endif
    </div>

    <div class="booking-code" style="background-color: #fff5f8; color: #f1416c;">{{ $booking->booking_code }}</div>

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
            <span class="info-value"><span class="status-badge status-rejected">{{ __('emails.rejected_status') }}</span></span>
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
        {{ __('emails.rejected_whats_next_text') }}
    </div>

    <div class="divider"></div>

    <p style="text-align: center; margin-bottom: 25px;">
        <a href="{{ route('partner.bookings.create') }}" class="btn btn-primary">{{ __('emails.create_new_booking') }}</a>
    </p>
</div>
@endsection
