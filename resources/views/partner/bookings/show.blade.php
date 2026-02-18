@extends('layouts.partner')

@section('title', __('partner.booking_details') . ' ' . $booking->booking_code)
@section('page-title', __('partner.booking_details'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('partner.bookings.index') }}" class="text-muted text-hover-primary">{{ __('partner.my_bookings') }}</a>
</li>
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ $booking->booking_code }}</li>
@endsection

@section('toolbar-actions')
@if (in_array($booking->status, [\App\Enums\BookingStatus::CONFIRMED, \App\Enums\BookingStatus::COMPLETED]))
<a href="{{ route('partner.bookings.voucher', $booking) }}" class="btn btn-sm btn-light-primary">
    <i class="ki-duotone ki-printer fs-5 me-1">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
        <span class="path4"></span>
        <span class="path5"></span>
    </i>
    {{ __('partner.download_voucher') }}
</a>
@endif
@endsection

@section('content')
@if (session('success'))
    <x-ui.alert type="success" :message="session('success')" dismissible class="mb-5" />
@endif

@if (session('error'))
    <x-ui.alert type="danger" :message="session('error')" dismissible class="mb-5" />
@endif

@php
    $departure = $booking->tourDeparture;
    $tour = $departure?->tour;
    $isConfirmed = $booking->status === \App\Enums\BookingStatus::CONFIRMED;
    $isPending = $booking->status === \App\Enums\BookingStatus::SUSPENDED_REQUEST;
    $isCancellable = $booking->status->canBeCancelled();

    // Calculate free cancellation deadline (48h before departure)
    $departureDateTime = $departure->date->copy()->setTimeFromTimeString($departure->time);
    $freeCancellationDeadline = $departureDateTime->copy()->subHours(48);
    $isFreeCancellation = now()->lessThan($freeCancellationDeadline);
@endphp

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
                    {{ __('partner.booking_summary') }}
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
                                <td class="text-muted fw-semibold w-150px">{{ __('partner.booking_code') }}</td>
                                <td class="fw-bold fs-5">{{ $booking->booking_code }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('partner.tour') }}</td>
                                <td>{{ $tour?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('partner.date') }}</td>
                                <td>{{ $departure->date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('partner.time') }}</td>
                                <td>{{ $departure->time }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted fw-semibold w-150px">{{ __('partner.passengers') }}</td>
                                <td>
                                    @php
                                        $paxCounts = $booking->passengers->groupBy('pax_type')->map->count();
                                    @endphp
                                    @if (isset($paxCounts['adult']))
                                        <span class="badge badge-light-primary me-1">{{ $paxCounts['adult'] }} Adult{{ $paxCounts['adult'] > 1 ? 's' : '' }}</span>
                                    @endif
                                    @if (isset($paxCounts['child']))
                                        <span class="badge badge-light-info me-1">{{ $paxCounts['child'] }} Child{{ $paxCounts['child'] > 1 ? 'ren' : '' }}</span>
                                    @endif
                                    @if (isset($paxCounts['infant']))
                                        <span class="badge badge-light-secondary">{{ $paxCounts['infant'] }} Infant{{ $paxCounts['infant'] > 1 ? 's' : '' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('partner.created') }}</td>
                                <td>{{ $booking->created_at->format('M d, Y \a\t H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('partner.last_updated') }}</td>
                                <td>{{ $booking->updated_at->format('M d, Y \a\t H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
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
                    {{ __('partner.passengers') }}
                </div>
            </div>
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th>#</th>
                                <th>{{ __('partner.name') }}</th>
                                <th>{{ __('partner.type') }}</th>
                                <th>{{ __('partner.pickup_point') }}</th>
                                <th>{{ __('partner.phone') }}</th>
                                <th>{{ __('partner.allergies') }}</th>
                                <th>{{ __('partner.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @foreach ($booking->passengers as $index => $passenger)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $passenger->first_name }} {{ $passenger->last_name }}</td>
                                <td><span class="badge badge-light-{{ $passenger->pax_type->color() }}">{{ $passenger->pax_type->label() }}</span></td>
                                <td>{{ $passenger->pickupPoint?->name ?? '-' }}</td>
                                <td>{{ $passenger->phone ?: '-' }}</td>
                                <td>
                                    @if ($passenger->allergies)
                                        <span class="text-danger">{{ $passenger->allergies }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $passenger->notes ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end::Passengers Card-->

        <!--begin::Tour Info Card-->
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <span class="me-2">
                        <i class="ki-duotone ki-map fs-2 text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </span>
                    {{ __('partner.tour_information') }}
                </div>
            </div>
            <div class="card-body py-4">
                <h4 class="text-gray-800 fw-bold mb-3">{{ $tour?->name ?? 'N/A' }}</h4>
                @if ($tour?->description)
                    <p class="text-gray-600 mb-5">{{ $tour->description }}</p>
                @endif

                @php
                    $firstPassenger = $booking->passengers->first();
                    $pickupPoint = $firstPassenger?->pickupPoint;
                @endphp
                @if ($pickupPoint)
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4">
                    <i class="ki-duotone ki-information fs-2tx text-primary me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-6 text-gray-800 fw-bold">{{ __('partner.pickup_information') }}</div>
                            <div class="fs-7 text-gray-700">
                                {{ __('partner.guests_should_be_at') }} <strong>{{ $pickupPoint->name }}</strong>
                                @if ($pickupPoint->default_time)
                                    {{ __('partner.by') }} <strong>{{ $pickupPoint->default_time }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <!--end::Tour Info Card-->
    </div>
    <!--end::Main Column-->

    <!--begin::Sidebar-->
    <div class="col-xl-4">
        <!--begin::Status Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('partner.status') }}</div>
            </div>
            <div class="card-body py-4">
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-light-{{ $booking->status->color() }}">
                            @if ($isConfirmed)
                                <i class="ki-duotone ki-check-circle fs-2x text-success">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            @elseif ($isPending)
                                <i class="ki-duotone ki-timer fs-2x text-warning">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            @else
                                <i class="ki-duotone ki-cross-circle fs-2x text-{{ $booking->status->color() }}">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            @endif
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-4 fw-bold text-{{ $booking->status->color() }}">{{ $booking->status->label() }}</span>
                        <span class="text-muted fs-7">
                            @if ($isConfirmed)
                                {{ __('partner.booking_confirmed') }}
                            @elseif ($isPending)
                                {{ __('partner.awaiting_approval') }}
                                @if ($booking->suspended_until)
                                    @php
                                        $minutesLeft = (int) now()->diffInMinutes($booking->suspended_until, false);
                                    @endphp
                                    <br><span class="text-warning">({{ $minutesLeft > 0 ? __('partner.min_left', ['minutes' => $minutesLeft]) : __('partner.expired') }})</span>
                                @endif
                            @else
                                {{ $booking->cancellation_reason ?: __('partner.booking_no_longer_active') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Status Card-->

        @if ($isCancellable)
        <!--begin::Cancellation Policy Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('partner.cancellation_policy') }}</div>
            </div>
            <div class="card-body py-4">
                @if ($isFreeCancellation)
                <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4 mb-4">
                    <i class="ki-duotone ki-check-circle fs-2tx text-success me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-6 text-gray-800 fw-bold">{{ __('partner.free_cancellation') }}</div>
                            <div class="fs-7 text-gray-700">{{ __('partner.until', ['datetime' => $freeCancellationDeadline->format('M d, Y H:i')]) }}</div>
                        </div>
                    </div>
                </div>
                @else
                <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4 mb-4">
                    <i class="ki-duotone ki-information-2 fs-2tx text-danger me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-6 text-gray-800 fw-bold">{{ __('partner.cancellation_penalty_applies') }}</div>
                            <div class="fs-7 text-gray-700">{{ __('partner.no_show_penalty') }}</div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="text-gray-600 fs-7 mb-4">
                    <strong>{{ __('partner.policy') }}:</strong><br>
                    {{ __('partner.policy_description') }}
                </div>
                <button type="button" class="btn btn-sm btn-light-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="ki-duotone ki-cross-circle fs-5 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ __('partner.request_cancellation') }}
                </button>
            </div>
        </div>
        <!--end::Cancellation Policy Card-->
        @endif

        <!--begin::Actions Card-->
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('partner.actions') }}</div>
            </div>
            <div class="card-body py-4">
                <div class="d-grid gap-2">
                    @if ($booking->status->canBeModified() && !$departure->isPastCutoff())
                    <a href="{{ route('partner.bookings.edit', $booking) }}" class="btn btn-warning">
                        <i class="ki-duotone ki-pencil fs-4 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('partner.edit_booking') }}
                    </a>
                    @endif
                    @if (in_array($booking->status, [\App\Enums\BookingStatus::CONFIRMED, \App\Enums\BookingStatus::COMPLETED]))
                    <a href="{{ route('partner.bookings.voucher', $booking) }}" class="btn btn-primary">
                        <i class="ki-duotone ki-printer fs-4 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                        {{ __('partner.download_voucher_pdf') }}
                    </a>
                    <a href="{{ route('partner.bookings.voucher.preview', $booking) }}" target="_blank" class="btn btn-light-primary">
                        <i class="ki-duotone ki-eye fs-4 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        {{ __('partner.preview_voucher') }}
                    </a>
                    @endif
                    <a href="{{ route('partner.bookings.index') }}" class="btn btn-light">
                        <i class="ki-duotone ki-arrow-left fs-4 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('partner.back_to_bookings') }}
                    </a>
                </div>
            </div>
        </div>
        <!--end::Actions Card-->
    </div>
    <!--end::Sidebar-->
</div>

@if ($isCancellable)
<!--begin::Cancel Modal-->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('partner.bookings.cancel', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('partner.request_cancellation') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body">
                    @if ($isFreeCancellation)
                    <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4 mb-5">
                        <i class="ki-duotone ki-check-circle fs-2tx text-success me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-800 fw-bold">{{ __('partner.free_cancellation_available') }}</div>
                                <div class="fs-7 text-gray-700">{{ __('partner.cancel_without_penalty') }}</div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4 mb-5">
                        <i class="ki-duotone ki-information-2 fs-2tx text-danger me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-800 fw-bold">{{ __('partner.cancellation_penalty_applies') }}</div>
                                <div class="fs-7 text-gray-700">{{ __('partner.no_show_penalty_account') }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="mb-0">
                        <label class="form-label fw-semibold fs-6">{{ __('partner.cancellation_reason') }}</label>
                        <textarea class="form-control form-control-solid" name="reason" rows="3" placeholder="{{ __('partner.cancellation_reason_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('partner.close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('partner.confirm_cancellation') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Cancel Modal-->
@endif
@endsection
