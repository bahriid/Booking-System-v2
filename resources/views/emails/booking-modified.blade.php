@extends('emails.layouts.base')

@section('title', __('emails.modified_title', ['code' => $booking->booking_code]))

@section('content')
<div class="email-header" style="background-color: #7239ea;">
    <h1>{{ __('emails.modified_header') }}</h1>
    <div class="subtitle">{{ __('emails.modified_subtitle') }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.hello', ['name' => $partner->name]) }}</div>

    <div class="message">
        {{ __('emails.modified_message') }}
    </div>

    <div class="booking-code" style="background-color: #f8f5ff; color: #7239ea;">{{ $booking->booking_code }}</div>

    @if(!empty($changes))
    <div class="info-box">
        <div class="info-box-header">{{ __('emails.changes_made') }}</div>

        {{-- Simple changes (notes, total_amount, status) --}}
        @foreach($changes as $field => $change)
            @if(is_array($change) && isset($change['old']) && !is_array($change['old']))
            <div class="info-row">
                <span class="info-label">{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                <span class="info-value">
                    <span style="text-decoration: line-through; color: #999;">{{ $change['old'] }}</span>
                    &rarr;
                    <strong>{{ $change['new'] }}</strong>
                </span>
            </div>
            @endif
        @endforeach

        {{-- Removed passengers --}}
        @if(!empty($changes['removed_passengers']))
        <div class="info-row">
            <span class="info-label">{{ __('emails.removed_passengers') }}</span>
            <span class="info-value">
                @foreach($changes['removed_passengers'] as $name)
                    <span style="text-decoration: line-through; color: #999;">{{ $name }}</span>@if(!$loop->last), @endif
                @endforeach
            </span>
        </div>
        @endif

        {{-- Added passengers --}}
        @if(!empty($changes['added_passengers']))
        <div class="info-row">
            <span class="info-label">{{ __('emails.added_passengers') }}</span>
            <span class="info-value">
                @foreach($changes['added_passengers'] as $name)
                    <strong>{{ $name }}</strong>@if(!$loop->last), @endif
                @endforeach
            </span>
        </div>
        @endif

        {{-- Passenger detail changes --}}
        @if(!empty($changes['passengers']))
            @foreach($changes['passengers'] as $passengerName => $fieldChanges)
            <div class="info-row">
                <span class="info-label">{{ $passengerName }}</span>
                <span class="info-value">
                    @foreach($fieldChanges as $fieldLabel => $fieldChange)
                        {{ $fieldLabel }}:
                        <span style="text-decoration: line-through; color: #999;">{{ $fieldChange['old'] ?? '' }}</span>
                        &rarr;
                        <strong>{{ $fieldChange['new'] ?? '' }}</strong>@if(!$loop->last); @endif
                    @endforeach
                </span>
            </div>
            @endforeach
        @endif
    </div>
    @endif

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.current_booking_details') }}</div>
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
            <span class="info-label">{{ __('emails.total_amount') }}</span>
            <span class="info-value">€{{ number_format($booking->total_amount, 2) }}</span>
        </div>
    </div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.passengers', ['count' => $passengers->count()]) }}</div>
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
        <strong>{{ __('emails.need_help') }}</strong><br>
        {{ __('emails.need_help_text') }}
    </div>

    <div class="divider"></div>

    <p style="text-align: center; margin-bottom: 25px;">
        <a href="{{ route('partner.bookings.show', $booking) }}" class="btn btn-primary">{{ __('emails.view_booking_details') }}</a>
    </p>
</div>
@endsection
