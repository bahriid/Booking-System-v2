@extends('layouts.admin')

@section('title', __('bookings.title'))
@section('page-title', __('bookings.booking_management'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('bookings.title') }}</li>
@endsection

@section('toolbar-actions')
<a href="{{ route('admin.bookings.create') }}" class="btn btn-sm btn-primary me-2">
    <i class="ki-duotone ki-plus fs-4 me-2">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
    {{ __('bookings.create_booking') }}
</a>
<a href="{{ route('admin.bookings.export', request()->query()) }}" class="btn btn-sm btn-light-primary me-2">
    <i class="ki-duotone ki-file-down fs-4 me-2">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
    {{ __('bookings.export_excel') }}
</a>
<a href="{{ route('admin.bookings.export-pdf', request()->query()) }}" class="btn btn-sm btn-light-danger">
    <i class="ki-duotone ki-document fs-4 me-2">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
    {{ __('bookings.export_pdf') }}
</a>
@endsection

@section('content')
@if (session('success'))
    <x-ui.alert type="success" :message="session('success')" dismissible class="mb-5" />
@endif

@if (session('error'))
    <x-ui.alert type="danger" :message="session('error')" dismissible class="mb-5" />
@endif

@if ($pendingCount > 0)
<div class="alert alert-warning d-flex align-items-center p-5 mb-5">
    <i class="ki-duotone ki-information-5 fs-2hx text-warning me-4">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    <div class="d-flex flex-column">
        <h4 class="mb-1 text-dark">{{ __('bookings.pending_overbooking_requests') }}</h4>
        <span>{!! __('bookings.overbooking_requests_count', ['count' => '<strong>' . $pendingCount . '</strong>']) !!}</span>
    </div>
    <a href="{{ route('admin.bookings.index', ['status' => 'suspended_request']) }}" class="btn btn-warning ms-auto">{{ __('bookings.view_pending') }}</a>
</div>
@endif

<!--begin::Bookings Card-->
<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title">
            <!--begin::Search-->
            <form method="GET" action="{{ route('admin.bookings.index') }}" class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid w-250px ps-13" placeholder="{{ __('bookings.search_booking') }}" />
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('partner'))
                    <input type="hidden" name="partner" value="{{ request('partner') }}">
                @endif
                @if(request('tour'))
                    <input type="hidden" name="tour" value="{{ request('tour') }}">
                @endif
            </form>
            <!--end::Search-->
        </div>
        <!--end::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar">
            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end gap-3" data-kt-booking-table-toolbar="base">
                <form method="GET" action="{{ route('admin.bookings.index') }}" class="d-flex gap-3">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select class="form-select form-select-solid w-140px" name="status" onchange="this.form.submit()">
                        <option value="">{{ __('bookings.all_status') }}</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-solid w-140px" name="partner" onchange="this.form.submit()">
                        <option value="">{{ __('bookings.all_partners') }}</option>
                        @foreach ($partners as $partner)
                            <option value="{{ $partner->id }}" {{ request('partner') == $partner->id ? 'selected' : '' }}>{{ Str::limit($partner->name, 20) }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-solid w-140px" name="tour" onchange="this.form.submit()">
                        <option value="">{{ __('bookings.all_tours') }}</option>
                        @foreach ($tours as $tour)
                            <option value="{{ $tour->id }}" {{ request('tour') == $tour->id ? 'selected' : '' }}>{{ $tour->code }}</option>
                        @endforeach
                    </select>
                    @if (request()->hasAny(['search', 'status', 'partner', 'tour']))
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-icon btn-light-danger" title="{{ __('bookings.clear_filters') }}">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                    @endif
                </form>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body py-4">
        <!--begin::Table-->
        <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4" id="kt_bookings_table">
            <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0 border-bottom-0">
                    <th>{{ __('bookings.booking') }}</th>
                    <th>{{ __('bookings.partner') }}</th>
                    <th>{{ __('bookings.tour') }}</th>
                    <th>{{ __('bookings.date') }}</th>
                    <th>{{ __('bookings.time') }}</th>
                    <th class="text-center">{{ __('bookings.pax') }}</th>
                    <th class="text-end">{{ __('bookings.amount') }}</th>
                    <th>{{ __('bookings.status') }}</th>
                    <th class="text-end">{{ __('bookings.actions') }}</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @forelse ($bookings as $booking)
                @php
                    $isOverbooking = $booking->status === \App\Enums\BookingStatus::SUSPENDED_REQUEST;
                    $isCancelled = $booking->status === \App\Enums\BookingStatus::CANCELLED;
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-gray-900 text-hover-primary fw-bold">{{ $booking->booking_code }}</a>
                    </td>
                    <td class="text-gray-700">{{ Str::limit($booking->partner->name, 20) }}</td>
                    <td class="text-gray-700">{{ Str::limit($booking->tourDeparture?->tour?->name ?? 'N/A', 22) }}</td>
                    <td class="text-gray-700">{{ $booking->tourDeparture->date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-gray-700">{{ $booking->tourDeparture->time ?? '-' }}</td>
                    <td class="text-center">
                        @php
                            $paxCounts = $booking->passengers->groupBy('pax_type')->map->count();
                            $paxDisplay = [];
                            if (isset($paxCounts['adult'])) $paxDisplay[] = $paxCounts['adult'] . 'A';
                            if (isset($paxCounts['child'])) $paxDisplay[] = $paxCounts['child'] . 'C';
                            if (isset($paxCounts['infant'])) $paxDisplay[] = $paxCounts['infant'] . 'I';
                        @endphp
                        <span class="text-gray-700">{{ implode(' ', $paxDisplay) ?: '-' }}</span>
                    </td>
                    <td class="text-end">
                        <span class="fw-bold text-gray-800">{{ number_format($booking->total_amount, 2) }}</span>
                    </td>
                    <td>
                        <span class="badge badge-light-{{ $booking->status->color() }}">{{ $booking->status->label() }}</span>
                        @if ($isOverbooking && $booking->suspended_until)
                            @php
                                $minutesLeft = (int) now()->diffInMinutes($booking->suspended_until, false);
                            @endphp
                            <span class="d-block text-danger fs-8 mt-1">
                                @if ($minutesLeft > 0)
                                    {{ __('bookings.minutes_left', ['minutes' => $minutesLeft]) }}
                                @else
                                    {{ __('bookings.expired') }}
                                @endif
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($isOverbooking)
                            <div class="d-flex justify-content-end gap-1">
                                <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-icon btn-light-success" title="{{ __('bookings.approve') }}">
                                        <i class="ki-duotone ki-check fs-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger" title="{{ __('bookings.reject') }}" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $booking->id }}">
                                    <i class="ki-duotone ki-cross fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>
                            </div>
                        @else
                            <a href="#" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-dots-vertical fs-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="menu-link px-3">{{ __('bookings.view') }}</a>
                                </div>
                                @if ($booking->status->canBeCancelled())
                                    <div class="separator my-2"></div>
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 text-hover-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $booking->id }}">{{ __('bookings.cancel') }}</a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-10">
                        <div class="text-muted">{{ __('bookings.no_bookings') }}</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!--end::Table-->

        <!--begin::Pagination-->
        <div class="d-flex justify-content-end mt-5">
            {{ $bookings->withQueryString()->links() }}
        </div>
        <!--end::Pagination-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Bookings Card-->

@foreach ($bookings as $booking)
    @if ($booking->status === \App\Enums\BookingStatus::SUSPENDED_REQUEST)
    <!--begin::Reject Modal-->
    <div class="modal fade" id="rejectModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h2 class="fw-bold">{{ __('bookings.reject_overbooking') }}</h2>
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p class="text-gray-600 mb-5">{{ __('bookings.reject_booking_confirm', ['code' => $booking->booking_code]) }}</p>
                        <div class="mb-0">
                            <label class="form-label fw-semibold fs-6">{{ __('bookings.reason_optional') }}</label>
                            <textarea class="form-control form-control-solid" name="reason" rows="3" placeholder="{{ __('bookings.reason_for_rejection') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('bookings.close') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('bookings.reject') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end::Reject Modal-->
    @endif

    @if ($booking->status->canBeCancelled())
    <!--begin::Cancel Modal-->
    <div class="modal fade" id="cancelModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
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
                                    <div class="fs-6 text-gray-700">{{ __('bookings.action_cannot_be_undone') }}</div>
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
                        <button type="submit" class="btn btn-danger">{{ __('bookings.cancel_booking') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end::Cancel Modal-->
    @endif
@endforeach
@endsection
