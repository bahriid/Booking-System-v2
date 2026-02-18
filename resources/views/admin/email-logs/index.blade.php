@extends('layouts.admin')

@section('title', __('logs.email_logs'))
@section('page-title', __('logs.email_logs'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('logs.email_logs') }}</li>
@endsection

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-sms fs-3 position-absolute ms-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <input type="text"
                       data-kt-filter="search"
                       class="form-control form-control-solid w-250px ps-13"
                       placeholder="{{ __('logs.search_by_email') }}"
                       form="filter-form"
                       name="email"
                       value="{{ request('email') }}">
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="collapse" data-bs-target="#filters">
                <i class="ki-duotone ki-filter fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ __('logs.filters') }}
            </button>
        </div>
    </div>

    <div class="collapse {{ request()->hasAny(['event_type', 'status', 'date_from', 'date_to', 'booking_code']) ? 'show' : '' }}" id="filters">
        <div class="card-body border-top pt-6">
            <form id="filter-form" action="{{ route('admin.email-logs.index') }}" method="GET">
                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('logs.event_type') }}</label>
                        <select name="event_type" class="form-select form-select-solid">
                            <option value="">{{ __('logs.all_types') }}</option>
                            @foreach($eventTypes as $value => $label)
                                <option value="{{ $value }}" {{ request('event_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('logs.status') }}</label>
                        <select name="status" class="form-select form-select-solid">
                            <option value="">{{ __('logs.all') }}</option>
                            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>{{ __('logs.sent') }}</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>{{ __('logs.failed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('logs.booking_code') }}</label>
                        <input type="text" name="booking_code" class="form-control form-control-solid" placeholder="{{ __('logs.search') }}" value="{{ request('booking_code') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('logs.from_date') }}</label>
                        <input type="date" name="date_from" class="form-control form-control-solid" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('logs.to_date') }}</label>
                        <input type="date" name="date_to" class="form-control form-control-solid" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">{{ __('logs.filter') }}</button>
                    </div>
                </div>
                @if(request()->hasAny(['event_type', 'status', 'date_from', 'date_to', 'booking_code', 'email']))
                <div class="mt-4">
                    <a href="{{ route('admin.email-logs.index') }}" class="btn btn-sm btn-light">{{ __('logs.clear_filters') }}</a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th class="min-w-120px">{{ __('logs.sent_at') }}</th>
                        <th class="min-w-140px">{{ __('logs.recipient') }}</th>
                        <th class="min-w-180px">{{ __('logs.subject') }}</th>
                        <th class="min-w-120px">{{ __('logs.event_type') }}</th>
                        <th class="min-w-100px">{{ __('logs.booking') }}</th>
                        <th class="min-w-80px">{{ __('logs.status') }}</th>
                        <th class="min-w-80px text-end">{{ __('logs.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emailLogs as $log)
                    <tr>
                        <td>
                            <span class="text-gray-800 fw-semibold">{{ $log->sent_at->format('M d, Y') }}</span>
                            <span class="text-muted fw-semibold d-block fs-7">{{ $log->sent_at->format('H:i:s') }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                @if($log->to_name)
                                    <span class="text-gray-800 fw-semibold">{{ $log->to_name }}</span>
                                @endif
                                <span class="text-muted fw-semibold fs-7">{{ $log->to_email }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-gray-800 fw-semibold">{{ Str::limit($log->subject, 40) }}</span>
                        </td>
                        <td>
                            @php
                                $eventBadge = match($log->event_type) {
                                    'booking_confirmed' => 'badge-light-success',
                                    'overbooking_requested' => 'badge-light-warning',
                                    'overbooking_approved' => 'badge-light-success',
                                    'overbooking_rejected' => 'badge-light-danger',
                                    'booking_cancelled' => 'badge-light-secondary',
                                    default => 'badge-light-primary',
                                };
                            @endphp
                            <span class="badge {{ $eventBadge }}">{{ $eventTypes[$log->event_type] ?? ucfirst(str_replace('_', ' ', $log->event_type)) }}</span>
                        </td>
                        <td>
                            @if($log->booking)
                                <a href="{{ route('admin.bookings.show', $log->booking) }}" class="text-gray-800 text-hover-primary fw-semibold">
                                    {{ $log->booking->booking_code }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($log->success)
                                <span class="badge badge-light-success">
                                    <i class="ki-duotone ki-check fs-6 text-success me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('logs.sent') }}
                                </span>
                            @else
                                <span class="badge badge-light-danger">
                                    <i class="ki-duotone ki-cross fs-6 text-danger me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('logs.failed') }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.email-logs.show', $log) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                <i class="ki-duotone ki-eye fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-10">
                            <i class="ki-duotone ki-sms fs-2x text-gray-300 mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="fs-5">{{ __('logs.no_email_logs') }}</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($emailLogs->hasPages())
        <div class="d-flex justify-content-end mt-5">
            {{ $emailLogs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
