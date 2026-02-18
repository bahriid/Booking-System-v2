@extends('layouts.admin')

@section('title', __('bookings.booking') . ' ' . $booking->booking_code)
@section('page-title', __('bookings.booking_details'))

@section('breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('admin.bookings.index') }}" class="text-muted text-hover-primary">{{ __('bookings.title') }}</a>
</li>
<li class="breadcrumb-item">
    <span class="bullet bg-gray-500 w-5px h-2px"></span>
</li>
<li class="breadcrumb-item text-muted">{{ $booking->booking_code }}</li>
@endsection

@section('toolbar-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-light">
        <i class="ki-duotone ki-arrow-left fs-5 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        {{ __('bookings.back_to_bookings') }}
    </a>
    @if (in_array($booking->status, [\App\Enums\BookingStatus::CONFIRMED, \App\Enums\BookingStatus::COMPLETED]))
    <a href="{{ route('admin.bookings.voucher', $booking) }}" class="btn btn-sm btn-light-primary">
        <i class="ki-duotone ki-printer fs-5 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
            <span class="path4"></span>
            <span class="path5"></span>
        </i>
        {{ __('bookings.download_voucher') }}
    </a>
    <a href="{{ route('admin.bookings.voucher.preview', $booking) }}" target="_blank" class="btn btn-sm btn-light">
        <i class="ki-duotone ki-eye fs-5 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
        </i>
        {{ __('bookings.preview') }}
    </a>
    @endif
    @if ($booking->tourDeparture)
    <a href="{{ route('admin.departures.manifest', $booking->tourDeparture) }}" class="btn btn-sm btn-light-success">
        <i class="ki-duotone ki-document fs-5 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        {{ __('bookings.tour_manifest') }}
    </a>
    @endif
</div>
@endsection

@section('content')
@if (session('success'))
    <x-ui.alert type="success" :message="session('success')" dismissible class="mb-5" />
@endif

@if (session('error'))
    <x-ui.alert type="danger" :message="session('error')" dismissible class="mb-5" />
@endif

@if ($booking->status === \App\Enums\BookingStatus::SUSPENDED_REQUEST)
<div class="alert alert-warning d-flex align-items-center p-5 mb-5">
    <i class="ki-duotone ki-information-5 fs-2hx text-warning me-4">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    <div class="d-flex flex-column flex-grow-1">
        <h4 class="mb-1 text-dark">{{ __('bookings.overbooking_request_pending') }}</h4>
        <span>{{ __('bookings.overbooking_exceeds_capacity') }}
            @if ($booking->suspended_until)
                @php
                    $minutesLeft = now()->diffInMinutes($booking->suspended_until, false);
                @endphp
                @if ($minutesLeft > 0)
                    <strong class="text-danger">{{ __('bookings.expires_in_minutes', ['minutes' => $minutesLeft]) }}</strong>
                @else
                    <strong class="text-danger">{{ __('bookings.expired_auto_reject') }}</strong>
                @endif
            @endif
        </span>
    </div>
    <div class="d-flex gap-2 ms-auto">
        <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">{{ __('bookings.approve') }}</button>
        </form>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">{{ __('bookings.reject') }}</button>
    </div>
</div>
@endif

<div class="row g-5 g-xl-10">
    <!--begin::Main Column-->
    <div class="col-xl-8">
        <!--begin::Booking Summary Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <span class="me-2">
                        <i class="ki-duotone ki-document fs-2 text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                    {{ __('bookings.booking_summary') }}
                </div>
                <div class="card-toolbar">
                    <span class="badge badge-light-{{ $booking->status->color() }} fs-7 fw-bold">{{ $booking->status->label() }}</span>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted fw-semibold w-150px">{{ __('bookings.booking_code') }}</td>
                                <td class="fw-bold fs-5">{{ $booking->booking_code }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('bookings.tour') }}</td>
                                <td>{{ $booking->tourDeparture?->tour?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('bookings.date') }}</td>
                                <td>{{ $booking->tourDeparture->date?->format('F d, Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('bookings.time') }}</td>
                                <td>{{ $booking->tourDeparture->time ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted fw-semibold w-150px">{{ __('bookings.partner') }}</td>
                                <td>
                                    <a href="{{ route('admin.partners.show', $booking->partner) }}" class="text-primary fw-bold">{{ $booking->partner->name }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('bookings.created') }}</td>
                                <td>{{ $booking->created_at->format('M d, Y \a\t H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('bookings.last_updated') }}</td>
                                <td>{{ $booking->updated_at->format('M d, Y \a\t H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('bookings.created_by') }}</td>
                                <td>{{ $booking->creator?->name ?? __('bookings.system') }} ({{ $booking->creator?->email ?? '-' }})</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if ($booking->notes)
                <div class="separator my-4"></div>
                <div class="row">
                    <div class="col-12">
                        <span class="text-muted fw-semibold">{{ __('bookings.notes') }}:</span>
                        <p class="mb-0 mt-2">{{ $booking->notes }}</p>
                    </div>
                </div>
                @endif
                @if ($booking->cancellation_reason)
                <div class="separator my-4"></div>
                <div class="row">
                    <div class="col-12">
                        <span class="text-danger fw-semibold">{{ __('bookings.cancellation_reason') }}:</span>
                        <p class="mb-0 mt-2">{{ $booking->cancellation_reason }}</p>
                        @if ($booking->cancelled_at)
                            <span class="text-muted fs-7">{{ __('bookings.cancelled_on', ['date' => $booking->cancelled_at->format('M d, Y \a\t H:i')]) }}</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        <!--end::Booking Summary Card-->

        <!--begin::Passengers Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <span class="me-2">
                        <i class="ki-duotone ki-people fs-2 text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                    </span>
                    {{ __('bookings.passengers') }}
                    <span class="badge badge-light-primary ms-2">{{ $booking->passengers->count() }} {{ __('bookings.total') }}</span>
                </div>
                <div class="card-toolbar">
                    @php
                        $paxCounts = $booking->passengers->groupBy('pax_type')->map->count();
                    @endphp
                    @foreach ($paxCounts as $type => $count)
                        <span class="badge badge-light-{{ $type === 'adult' ? 'primary' : ($type === 'child' ? 'info' : 'secondary') }} me-2">{{ $count }} {{ strtoupper(substr($type, 0, 3)) }}</span>
                    @endforeach
                </div>
            </div>
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th>#</th>
                                <th>{{ __('bookings.name') }}</th>
                                <th>{{ __('bookings.type') }}</th>
                                <th>{{ __('bookings.pickup_point') }}</th>
                                <th>{{ __('bookings.phone') }}</th>
                                <th>{{ __('bookings.allergies') }}</th>
                                <th>{{ __('bookings.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse ($booking->passengers as $index => $passenger)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $passenger->full_name }}</td>
                                <td><span class="badge badge-light-{{ $passenger->pax_type->value === 'adult' ? 'primary' : ($passenger->pax_type->value === 'child' ? 'info' : 'secondary') }}">{{ strtoupper(substr($passenger->pax_type->value, 0, 3)) }}</span></td>
                                <td>{{ $passenger->pickupPoint?->name ?? '-' }} {{ $passenger->pickupPoint?->time ? '(' . $passenger->pickupPoint->time . ')' : '' }}</td>
                                <td>{{ $passenger->phone ?? '-' }}</td>
                                <td>
                                    @if ($passenger->allergies)
                                        <span class="text-danger">{{ $passenger->allergies }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $passenger->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">{{ __('bookings.no_passengers') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end::Passengers Card-->
    </div>
    <!--end::Main Column-->

    <!--begin::Sidebar-->
    <div class="col-xl-4">
        <!--begin::Status Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('bookings.status') }}</div>
            </div>
            <div class="card-body py-4">
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-light-{{ $booking->status->color() }}">
                            <i class="ki-duotone ki-{{ $booking->status === \App\Enums\BookingStatus::CONFIRMED ? 'check-circle' : ($booking->status === \App\Enums\BookingStatus::CANCELLED ? 'cross-circle' : 'time') }} fs-2x text-{{ $booking->status->color() }}">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-4 fw-bold text-{{ $booking->status->color() }}">{{ $booking->status->label() }}</span>
                        <span class="text-muted fs-7">
                            @if ($booking->status === \App\Enums\BookingStatus::CONFIRMED)
                                {{ __('bookings.booking_is_confirmed') }}
                            @elseif ($booking->status === \App\Enums\BookingStatus::CANCELLED)
                                {{ __('bookings.booking_was_cancelled') }}
                            @elseif ($booking->status === \App\Enums\BookingStatus::SUSPENDED_REQUEST)
                                {{ __('bookings.awaiting_approval') }}
                            @else
                                {{ $booking->status->label() }}
                            @endif
                        </span>
                    </div>
                </div>
                @if ($booking->status->canBeCancelled())
                <div class="separator my-4"></div>
                <div class="d-flex flex-column gap-3">
                    <button type="button" class="btn btn-sm btn-light-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <i class="ki-duotone ki-cross-circle fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('bookings.cancel_booking') }}
                    </button>
                </div>
                @endif
            </div>
        </div>
        <!--end::Status Card-->

        <!--begin::Accounting Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <span class="me-2">
                        <i class="ki-duotone ki-dollar fs-2 text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </span>
                    {{ __('bookings.accounting') }}
                </div>
                <div class="card-toolbar">
                    @if ($booking->balance_due <= 0)
                        <span class="badge badge-light-success">{{ __('bookings.paid') }}</span>
                    @else
                        <span class="badge badge-light-warning">{{ __('bookings.unpaid') }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body py-4">
                @foreach ($booking->passengers->groupBy('pax_type') as $type => $passengers)
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ count($passengers) }}x {{ ucfirst($type) }}</span>
                    <span class="fw-bold">-</span>
                </div>
                @endforeach
                <div class="separator my-4"></div>
                <div class="d-flex flex-stack mb-3">
                    <span class="fw-bold fs-5">{{ __('bookings.total_amount') }}</span>
                    <span class="fw-bold fs-5 text-primary">{{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('bookings.paid') }}</span>
                    <span class="fw-bold text-success">{{ number_format($booking->amount_paid, 2) }}</span>
                </div>
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('bookings.outstanding') }}</span>
                    <span class="fw-bold {{ $booking->balance_due > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($booking->balance_due, 2) }}</span>
                </div>
                @if ($booking->penalty_amount > 0)
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('bookings.penalty') }}</span>
                    <span class="fw-bold text-danger">{{ number_format($booking->penalty_amount, 2) }}</span>
                </div>
                @endif
                @if ($booking->balance_due > 0 && $booking->status === \App\Enums\BookingStatus::CONFIRMED)
                <button type="button" class="btn btn-sm btn-light-success w-100 mt-3" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                    <i class="ki-duotone ki-wallet fs-5 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    {{ __('bookings.record_payment') }}
                </button>
                @endif
            </div>
        </div>
        <!--end::Accounting Card-->

        <!--begin::Tour Info Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('bookings.tour_departure') }}</div>
            </div>
            <div class="card-body py-4">
                @if ($booking->tourDeparture)
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('bookings.capacity') }}</span>
                    <span class="fw-bold">{{ $booking->tourDeparture->capacity }} {{ __('bookings.pax') }}</span>
                </div>
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('bookings.booked') }}</span>
                    <span class="fw-bold">{{ $booking->tourDeparture->booked_seats }} {{ __('bookings.pax') }}</span>
                </div>
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('bookings.available') }}</span>
                    <span class="fw-bold text-{{ $booking->tourDeparture->remaining_seats > 0 ? 'success' : 'danger' }}">{{ $booking->tourDeparture->remaining_seats }} {{ __('bookings.seats') }}</span>
                </div>
                <div class="separator my-4"></div>
                @endif
                <a href="{{ route('admin.departures.show', $booking->tour_departure_id) }}" class="btn btn-sm btn-light w-100">
                    <i class="ki-duotone ki-calendar-8 fs-5 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                        <span class="path6"></span>
                    </i>
                    {{ __('bookings.view_departure') }}
                </a>
            </div>
        </div>
        <!--end::Tour Info Card-->

        <!--begin::Cancellation Policy Card-->
        @if ($booking->status === \App\Enums\BookingStatus::CONFIRMED && $booking->tourDeparture)
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('bookings.cancellation_policy') }}</div>
            </div>
            <div class="card-body py-4">
                @php
                    $departureDateTime = \Carbon\Carbon::parse($booking->tourDeparture->date->format('Y-m-d') . ' ' . $booking->tourDeparture->time);
                    $hoursUntilDeparture = now()->diffInHours($departureDateTime, false);
                    $freeCancelUntil = $departureDateTime->copy()->subHours(24);
                @endphp
                <div class="notice d-flex bg-light-{{ $hoursUntilDeparture >= 24 ? 'success' : 'warning' }} rounded border-{{ $hoursUntilDeparture >= 24 ? 'success' : 'warning' }} border border-dashed p-4">
                    <i class="ki-duotone ki-information-2 fs-2tx text-{{ $hoursUntilDeparture >= 24 ? 'success' : 'warning' }} me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            @if ($hoursUntilDeparture >= 24)
                                <div class="fs-6 text-gray-700">
                                    <strong>{{ __('bookings.free_cancellation') }}</strong> {{ __('bookings.free_cancellation_until', ['date' => $freeCancelUntil->format('M d, Y H:i')]) }}
                                </div>
                                <div class="fs-7 text-muted mt-1">
                                    {{ __('bookings.after_100_penalty') }}
                                </div>
                            @else
                                <div class="fs-6 text-gray-700">
                                    <strong>{{ __('bookings.penalty_100_percent') }}</strong>
                                </div>
                                <div class="fs-7 text-muted mt-1">
                                    {{ __('bookings.less_than_24_hours') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!--end::Cancellation Policy Card-->
    </div>
    <!--end::Sidebar-->
</div>

@if ($booking->status->canBeCancelled())
<!--begin::Cancel Modal-->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('bookings.cancel_booking') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4 mb-5">
                        <i class="ki-duotone ki-information-2 fs-2tx text-danger me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-700">
                                    {{ __('bookings.action_cannot_be_undone_penalty') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold fs-6">{{ __('bookings.cancellation_reason') }}</label>
                        <textarea class="form-control form-control-solid" name="reason" rows="3" placeholder="{{ __('bookings.reason_for_cancellation') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('bookings.close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('bookings.confirm_cancellation') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Cancel Modal-->
@endif

@if ($booking->status === \App\Enums\BookingStatus::SUSPENDED_REQUEST)
<!--begin::Reject Modal-->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('bookings.reject_overbooking_request') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label class="form-label fw-semibold fs-6">{{ __('bookings.reason_optional') }}</label>
                        <textarea class="form-control form-control-solid" name="reason" rows="3" placeholder="{{ __('bookings.reason_for_rejection') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('bookings.close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('bookings.reject_request') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Reject Modal-->
@endif

@if ($booking->balance_due > 0 && $booking->status === \App\Enums\BookingStatus::CONFIRMED)
<!--begin::Record Payment Modal-->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.accounting.payment') }}" method="POST">
                @csrf
                <input type="hidden" name="partner_id" value="{{ $booking->partner_id }}">
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <div class="modal-header">
                    <h3 class="modal-title">{{ __('bookings.record_payment') }}</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-5">
                        <label class="form-label fw-semibold">{{ __('partners.partner') }}</label>
                        <input type="text" class="form-control form-control-solid" value="{{ $booking->partner->name }}" readonly>
                        <div class="form-text text-danger">{{ __('partners.outstanding_balance') }}: €{{ number_format($booking->balance_due, 2) }}</div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">{{ __('partners.amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" max="{{ $booking->balance_due }}" value="{{ $booking->balance_due }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">{{ __('partners.payment_method') }}</label>
                            <select name="method" class="form-select" required>
                                <option value="bank_transfer">{{ __('partners.bank_transfer') }}</option>
                                <option value="cash">{{ __('partners.cash') }}</option>
                                <option value="card">{{ __('partners.card') }}</option>
                                <option value="other">{{ __('partners.other') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">{{ __('partners.payment_date') }}</label>
                            <input type="date" name="paid_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('partners.reference') }}</label>
                            <input type="text" name="reference" class="form-control" placeholder="{{ $booking->booking_code }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('partners.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('partners.payment_notes_placeholder') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('partners.cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('partners.record_payment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Record Payment Modal-->
@endif
@endsection
