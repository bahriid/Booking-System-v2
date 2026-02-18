@extends('layouts.partner')

@section('title', __('partner.my_bookings'))
@section('page-title', __('partner.my_bookings'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('partner.bookings') }}</li>
@endsection

@section('toolbar-actions')
<a href="{{ route('partner.bookings.create') }}" class="btn btn-sm btn-primary">
    <i class="ki-duotone ki-plus fs-4 me-2">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    {{ __('partner.new_booking') }}
</a>
@endsection

@section('content')
@if (session('success'))
    <x-ui.alert type="success" :message="session('success')" dismissible class="mb-5" />
@endif

@if (session('error'))
    <x-ui.alert type="danger" :message="session('error')" dismissible class="mb-5" />
@endif

<!--begin::Bookings Table Card-->
<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title">
            <!--begin::Search-->
            <form method="GET" action="{{ route('partner.bookings.index') }}" class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid w-250px ps-13" placeholder="{{ __('partner.search_booking_code') }}" />
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
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
            <div class="d-flex justify-content-end gap-3">
                <form method="GET" action="{{ route('partner.bookings.index') }}" class="d-flex gap-3">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select class="form-select form-select-solid w-140px" name="status" onchange="this.form.submit()">
                        <option value="">{{ __('partner.all_status') }}</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-solid w-160px" name="tour" onchange="this.form.submit()">
                        <option value="">{{ __('partner.all_tours') }}</option>
                        @foreach ($tours as $tour)
                            <option value="{{ $tour->id }}" {{ request('tour') == $tour->id ? 'selected' : '' }}>{{ $tour->code }}</option>
                        @endforeach
                    </select>
                    @if (request()->hasAny(['search', 'status', 'tour']))
                        <a href="{{ route('partner.bookings.index') }}" class="btn btn-icon btn-light-danger" title="{{ __('partner.clear_filters') }}">
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
        <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
            <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0 border-bottom-0">
                    <th>{{ __('partner.booking_code') }}</th>
                    <th>{{ __('partner.tour') }}</th>
                    <th>{{ __('partner.date') }}</th>
                    <th>{{ __('partner.time') }}</th>
                    <th class="text-center">{{ __('partner.pax') }}</th>
                    <th>{{ __('partner.status') }}</th>
                    <th>{{ __('partner.created') }}</th>
                    <th class="text-end">{{ __('partner.actions') }}</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @forelse ($bookings as $booking)
                @php
                    $isPending = $booking->status === \App\Enums\BookingStatus::SUSPENDED_REQUEST;
                    $isCancelled = in_array($booking->status, [
                        \App\Enums\BookingStatus::CANCELLED,
                        \App\Enums\BookingStatus::REJECTED,
                        \App\Enums\BookingStatus::EXPIRED,
                    ]);
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('partner.bookings.show', $booking) }}" class="text-gray-900 text-hover-primary fw-bold">{{ $booking->booking_code }}</a>
                    </td>
                    <td class="text-gray-700">{{ Str::limit($booking->tourDeparture?->tour?->name ?? 'N/A', 25) }}</td>
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
                    <td>
                        <span class="badge badge-light-{{ $booking->status->color() }}">{{ $booking->status->label() }}</span>
                        @if ($isPending && $booking->suspended_until)
                            @php
                                $minutesLeft = (int) now()->diffInMinutes($booking->suspended_until, false);
                            @endphp
                            <span class="d-block text-warning fs-8 mt-1">
                                @if ($minutesLeft > 0)
                                    {{ __('partner.min_left', ['minutes' => $minutesLeft]) }}
                                @else
                                    {{ __('partner.expired') }}
                                @endif
                            </span>
                        @endif
                    </td>
                    <td class="text-gray-600 fs-7">{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-end">
                        <a href="{{ route('partner.bookings.show', $booking) }}" class="btn btn-sm btn-icon btn-light btn-active-light-primary" title="{{ __('partner.view') }}">
                            <i class="ki-duotone ki-eye fs-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </a>
                        @if ($booking->status === \App\Enums\BookingStatus::CONFIRMED)
                            <a href="#" class="btn btn-sm btn-icon btn-light btn-active-light-primary" title="{{ __('partner.download_voucher') }}">
                                <i class="ki-duotone ki-file-down fs-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-10">
                        <div class="text-muted">{{ __('partner.no_bookings_found') }}</div>
                        <a href="{{ route('partner.bookings.create') }}" class="btn btn-sm btn-primary mt-3">{{ __('partner.create_first_booking') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!--end::Table-->

        <!--begin::Pagination-->
        {{ $bookings->withQueryString()->links() }}
        <!--end::Pagination-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Bookings Table Card-->
@endsection
