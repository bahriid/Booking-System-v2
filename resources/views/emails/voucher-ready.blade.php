@extends('emails.layouts.base')

@section('title', __('emails.voucher_ready_title', ['code' => $booking->booking_code]))

@section('content')
<div class="email-header">
    <h1>{{ __('emails.voucher_ready_header') }}</h1>
    <div class="subtitle">{{ __('emails.voucher_ready_subtitle') }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.hello', ['name' => $partner->name]) }}</div>

    <div class="message">
        {{ __('emails.voucher_ready_message') }}
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
            <span class="info-label">{{ __('emails.passengers') }}</span>
            <span class="info-value">{{ $passengers->count() }} {{ __('emails.pax') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.total_amount') }}</span>
            <span class="info-value">€{{ number_format($booking->total_amount, 2) }}</span>
        </div>
    </div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.passenger_list') }}</div>
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
        <strong>{{ __('emails.voucher_tip') }}:</strong> {{ __('emails.voucher_tip_text') }}
    </div>

    <div class="divider"></div>

    <p style="text-align: center; margin-bottom: 25px;">
        <a href="{{ $voucherUrl }}" class="btn btn-primary">{{ __('emails.download_voucher') }}</a>
    </p>

    <p style="text-align: center; font-size: 13px; color: #7e8299;">
        {{ __('emails.voucher_url_note') }}
    </p>
</div>
@endsection
