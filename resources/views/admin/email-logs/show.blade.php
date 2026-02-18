@extends('layouts.admin')

@section('title', __('logs.email_log_details'))
@section('page-title', __('logs.email_log_details'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('admin.email-logs.index') }}" class="text-muted text-hover-primary">{{ __('logs.email_logs') }}</a>
</li>
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('logs.details') }}</li>
@endsection

@section('content')
<div class="row g-5 g-xl-8">
    <div class="col-xl-8">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header">
                <h3 class="card-title">{{ __('logs.email_details') }}</h3>
            </div>
            <div class="card-body">
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.status') }}</label>
                    <div class="col-lg-8">
                        @if($emailLog->success)
                            <span class="badge badge-light-success fs-7">
                                <i class="ki-duotone ki-check fs-6 text-success me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ __('logs.sent_successfully') }}
                            </span>
                        @else
                            <span class="badge badge-light-danger fs-7">
                                <i class="ki-duotone ki-cross fs-6 text-danger me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ __('logs.failed') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.sent_at') }}</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $emailLog->sent_at->format('l, F d, Y \a\t H:i:s') }}</span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.recipient') }}</label>
                    <div class="col-lg-8 fv-row">
                        <span class="fw-bold fs-6 text-gray-800">
                            @if($emailLog->to_name)
                                {{ $emailLog->to_name }} &lt;{{ $emailLog->to_email }}&gt;
                            @else
                                {{ $emailLog->to_email }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.subject') }}</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $emailLog->subject }}</span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.event_type') }}</label>
                    <div class="col-lg-8">
                        @php
                            $eventLabels = [
                                'booking_confirmed' => __('logs.event_booking_confirmed'),
                                'overbooking_requested' => __('logs.event_overbooking_requested'),
                                'overbooking_approved' => __('logs.event_overbooking_approved'),
                                'overbooking_rejected' => __('logs.event_overbooking_rejected'),
                                'booking_cancelled' => __('logs.event_booking_cancelled'),
                            ];
                            $eventBadge = match($emailLog->event_type) {
                                'booking_confirmed' => 'badge-light-success',
                                'overbooking_requested' => 'badge-light-warning',
                                'overbooking_approved' => 'badge-light-success',
                                'overbooking_rejected' => 'badge-light-danger',
                                'booking_cancelled' => 'badge-light-secondary',
                                default => 'badge-light-primary',
                            };
                        @endphp
                        <span class="badge {{ $eventBadge }} fs-7">{{ $eventLabels[$emailLog->event_type] ?? ucfirst(str_replace('_', ' ', $emailLog->event_type)) }}</span>
                    </div>
                </div>

                @if($emailLog->booking)
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.related_booking') }}</label>
                    <div class="col-lg-8">
                        <a href="{{ route('admin.bookings.show', $emailLog->booking) }}" class="fw-bold text-primary">
                            {{ $emailLog->booking->booking_code }}
                        </a>
                    </div>
                </div>
                @endif

                @if(!$emailLog->success && $emailLog->error_message)
                <div class="row">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.error_message') }}</label>
                    <div class="col-lg-8">
                        <div class="alert alert-danger mb-0">
                            <code>{{ $emailLog->error_message }}</code>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header">
                <h3 class="card-title">{{ __('logs.actions') }}</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.email-logs.index') }}" class="btn btn-light-primary w-100 mb-3">
                    <i class="ki-duotone ki-arrow-left fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ __('logs.back_to_email_logs') }}
                </a>

                @if($emailLog->booking)
                <a href="{{ route('admin.bookings.show', $emailLog->booking) }}" class="btn btn-light w-100">
                    <i class="ki-duotone ki-ticket fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    {{ __('logs.view_booking') }}
                </a>
                @endif
            </div>
        </div>

        @if($emailLog->booking)
        <div class="card mb-5 mb-xl-8">
            <div class="card-header">
                <h3 class="card-title">{{ __('logs.booking_summary') }}</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-stack mb-5">
                    <span class="text-muted fw-semibold">{{ __('logs.code') }}</span>
                    <span class="text-gray-800 fw-bold">{{ $emailLog->booking->booking_code }}</span>
                </div>
                <div class="d-flex flex-stack mb-5">
                    <span class="text-muted fw-semibold">{{ __('logs.status') }}</span>
                    @php
                        $statusBadge = match($emailLog->booking->status->value) {
                            'confirmed' => 'badge-light-success',
                            'suspended_request' => 'badge-light-warning',
                            'cancelled' => 'badge-light-secondary',
                            'rejected' => 'badge-light-danger',
                            default => 'badge-light-primary',
                        };
                    @endphp
                    <span class="badge {{ $statusBadge }}">{{ ucfirst(str_replace('_', ' ', $emailLog->booking->status->value)) }}</span>
                </div>
                <div class="d-flex flex-stack mb-5">
                    <span class="text-muted fw-semibold">{{ __('logs.partner') }}</span>
                    <span class="text-gray-800 fw-bold">{{ $emailLog->booking->partner->name }}</span>
                </div>
                <div class="d-flex flex-stack">
                    <span class="text-muted fw-semibold">{{ __('logs.total') }}</span>
                    <span class="text-gray-800 fw-bold">{{ number_format($emailLog->booking->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
