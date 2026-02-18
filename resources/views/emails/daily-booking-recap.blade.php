@extends('emails.layouts.base')

@section('title', __('emails.daily_recap_subject', ['date' => $date->format('d/m/Y')]))

@section('content')
<div class="email-header" style="background-color: #009ef7;">
    <h1>{{ __('emails.daily_recap_header') }}</h1>
    <div class="subtitle">{{ __('emails.daily_recap_subtitle', ['date' => $date->format('d/m/Y')]) }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.admin_alert') }}</div>

    <div class="message">
        {{ __('emails.daily_recap_message', ['date' => $date->format('d/m/Y')]) }}
    </div>

    {{-- Summary Stats --}}
    <div class="info-box">
        <div class="info-box-header">{{ __('emails.daily_recap_summary') }}</div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.daily_recap_total_bookings') }}</span>
            <span class="info-value">{{ $stats['total_bookings'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.daily_recap_confirmed') }}</span>
            <span class="info-value" style="color: #50cd89;">{{ $stats['confirmed'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.daily_recap_overbooking_requests') }}</span>
            <span class="info-value" style="color: #ffc700;">{{ $stats['overbooking_requests'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.daily_recap_cancelled') }}</span>
            <span class="info-value" style="color: #f1416c;">{{ $stats['cancelled'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.daily_recap_total_passengers') }}</span>
            <span class="info-value">{{ $stats['total_passengers'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.daily_recap_total_revenue') }}</span>
            <span class="info-value">&euro;{{ number_format($stats['total_revenue'], 2) }}</span>
        </div>
    </div>

    {{-- Breakdown by Tour --}}
    @if(!empty($stats['by_tour']))
    <div class="info-box">
        <div class="info-box-header">{{ __('emails.daily_recap_by_tour') }}</div>
        <table class="passengers-table">
            <thead>
                <tr>
                    <th>{{ __('emails.tour') }}</th>
                    <th>{{ __('emails.daily_recap_bookings') }}</th>
                    <th>{{ __('emails.pax') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['by_tour'] as $tourStat)
                <tr>
                    <td>{{ $tourStat['name'] }}</td>
                    <td>{{ $tourStat['bookings'] }}</td>
                    <td>{{ $tourStat['passengers'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Breakdown by Partner --}}
    @if(!empty($stats['by_partner']))
    <div class="info-box">
        <div class="info-box-header">{{ __('emails.daily_recap_by_partner') }}</div>
        <table class="passengers-table">
            <thead>
                <tr>
                    <th>{{ __('emails.partner') }}</th>
                    <th>{{ __('emails.daily_recap_bookings') }}</th>
                    <th>{{ __('emails.pax') }}</th>
                    <th>{{ __('emails.daily_recap_revenue') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['by_partner'] as $partnerStat)
                <tr>
                    <td>{{ $partnerStat['name'] }}</td>
                    <td>{{ $partnerStat['bookings'] }}</td>
                    <td>{{ $partnerStat['passengers'] }}</td>
                    <td>&euro;{{ number_format($partnerStat['revenue'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- All Bookings List --}}
    <div class="info-box">
        <div class="info-box-header">{{ __('emails.daily_recap_all_bookings') }}</div>
        <table class="passengers-table">
            <thead>
                <tr>
                    <th>{{ __('emails.daily_recap_code') }}</th>
                    <th>{{ __('emails.partner') }}</th>
                    <th>{{ __('emails.tour') }}</th>
                    <th>{{ __('emails.pax') }}</th>
                    <th>{{ __('emails.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                <tr>
                    <td style="font-family: monospace; font-size: 12px;">{{ $b->booking_code }}</td>
                    <td>{{ $b->partner?->name ?? '-' }}</td>
                    <td>{{ $b->tourDeparture?->tour?->name ?? '-' }}</td>
                    <td>{{ $b->passengers->count() }}</td>
                    <td>
                        @php
                            $statusClass = match($b->status->value) {
                                'confirmed' => 'status-confirmed',
                                'suspended_request' => 'status-pending',
                                'cancelled' => 'status-cancelled',
                                'rejected' => 'status-rejected',
                                'expired' => 'status-expired',
                                default => '',
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $b->status->label() }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; margin-bottom: 25px;">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">{{ __('emails.daily_recap_view_dashboard') }}</a>
    </p>
</div>
@endsection
