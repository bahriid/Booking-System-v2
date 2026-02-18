@extends('layouts.partner')

@section('title', __('partner.dashboard'))
@section('page-title', __('partner.dashboard'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('partner.dashboard') }}</li>
@endsection

@section('content')
<!--begin::Welcome Alert-->
<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
    <i class="ki-duotone ki-information-5 fs-2hx text-primary me-4">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    <div class="d-flex flex-column">
        <h4 class="mb-1 text-primary">{{ __('partner.welcome', ['name' => $partner->name]) }}</h4>
        <span>{{ __('partner.welcome_message') }}</span>
    </div>
</div>
<!--end::Welcome Alert-->

<!--begin::Stats Row-->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <div class="col-md-4">
        <div class="card bg-primary hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <i class="ki-duotone ki-ticket text-white fs-2x ms-n1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="text-white fw-bold fs-2 mt-4">{{ $bookingsThisMonth }}</div>
                <div class="text-white opacity-75 fw-semibold fs-6">{{ __('partner.bookings_this_month') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <i class="ki-duotone ki-people text-white fs-2x ms-n1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                </i>
                <div class="text-white fw-bold fs-2 mt-4">{{ $passengersThisMonth }}</div>
                <div class="text-white opacity-75 fw-semibold fs-6">{{ __('partner.total_passengers') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <a href="{{ route('partner.bookings.index', ['status' => 'suspended_request']) }}" class="card bg-warning hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <i class="ki-duotone ki-timer text-white fs-2x ms-n1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="text-white fw-bold fs-2 mt-4">{{ $pendingRequests }}</div>
                <div class="text-white opacity-75 fw-semibold fs-6">{{ __('partner.pending_requests') }}</div>
            </div>
        </a>
    </div>
</div>
<!--end::Stats Row-->

<div class="row g-5 g-xl-8">
    <!--begin::Quick Actions-->
    <div class="col-xl-4">
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('partner.quick_actions') }}</span>
                </h3>
            </div>
            <div class="card-body d-flex flex-column pt-0">
                <a href="{{ route('partner.bookings.create') }}" class="btn btn-primary btn-lg mb-5">
                    <i class="ki-duotone ki-plus-circle fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    {{ __('partner.new_booking') }}
                </a>
                <a href="{{ route('partner.bookings.index') }}" class="btn btn-light-primary">
                    <i class="ki-duotone ki-document fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ __('partner.view_all_bookings') }}
                </a>
            </div>
        </div>
    </div>
    <!--end::Quick Actions-->

    <!--begin::Recent Bookings-->
    <div class="col-xl-8">
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('partner.recent_bookings') }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ __('partner.your_latest_booking_requests') }}</span>
                </h3>
                <div class="card-toolbar">
                    <a href="{{ route('partner.bookings.index') }}" class="btn btn-sm btn-light-primary">{{ __('partner.view_all') }}</a>
                </div>
            </div>
            <div class="card-body py-3">
                @if($recentBookings->isEmpty())
                    <div class="text-center py-10">
                        <div class="text-muted mb-3">{{ __('partner.no_bookings_yet') }}</div>
                        <a href="{{ route('partner.bookings.create') }}" class="btn btn-sm btn-primary">{{ __('partner.create_first_booking') }}</a>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-120px">{{ __('partner.code') }}</th>
                                <th class="min-w-100px">{{ __('partner.tour') }}</th>
                                <th class="min-w-80px">{{ __('partner.date') }}</th>
                                <th class="min-w-60px">{{ __('partner.pax') }}</th>
                                <th class="min-w-80px">{{ __('partner.status') }}</th>
                                <th class="min-w-50px text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                            @php
                                $isPending = $booking->status === \App\Enums\BookingStatus::SUSPENDED_REQUEST;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('partner.bookings.show', $booking) }}" class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $booking->booking_code }}</a>
                                </td>
                                <td><span class="text-muted fw-semibold">{{ Str::limit($booking->tourDeparture?->tour?->name ?? '-', 20) }}</span></td>
                                <td><span class="text-muted fw-semibold">{{ $booking->tourDeparture?->date?->format('d/m') ?? '-' }} {{ $booking->tourDeparture?->time ?? '' }}</span></td>
                                <td>
                                    @php
                                        $paxCounts = $booking->passengers->groupBy('pax_type')->map->count();
                                    @endphp
                                    @if (isset($paxCounts['adult']))
                                        <span class="badge badge-light-primary">{{ $paxCounts['adult'] }} A</span>
                                    @endif
                                    @if (isset($paxCounts['child']))
                                        <span class="badge badge-light-info">{{ $paxCounts['child'] }} C</span>
                                    @endif
                                    @if (isset($paxCounts['infant']))
                                        <span class="badge badge-light-secondary">{{ $paxCounts['infant'] }} I</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light-{{ $booking->status->color() }}">{{ $booking->status->label() }}</span>
                                    @if ($isPending && $booking->suspended_until)
                                        @php
                                            $minutesLeft = (int) now()->diffInMinutes($booking->suspended_until, false);
                                        @endphp
                                        <br><small class="text-warning fw-semibold">{{ $minutesLeft > 0 ? __('partner.min_left', ['minutes' => $minutesLeft]) : __('partner.expired') }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if (in_array($booking->status, [\App\Enums\BookingStatus::CONFIRMED, \App\Enums\BookingStatus::COMPLETED]))
                                        <a href="{{ route('partner.bookings.voucher', $booking) }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" title="{{ __('partner.download_voucher') }}">
                                            <i class="ki-duotone ki-file-down fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $recentBookings->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <!--end::Recent Bookings-->
</div>

<!--begin::Upcoming Tours-->
<div class="card card-xl-stretch">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">{{ __('partner.your_upcoming_tours') }}</span>
            <span class="text-muted fw-semibold fs-7">{{ __('partner.tours_next_7_days') }}</span>
        </h3>
    </div>
    <div class="card-body py-3">
        <div class="row g-5 g-xl-8">
            @forelse($upcomingBookings as $booking)
            @php
                $departure = $booking->tourDeparture;
                if (!$departure) continue;
                $isToday = $departure->date->isToday();
                $isTomorrow = $departure->date->isTomorrow();
                $dateBadge = $isToday ? __('partner.today') : ($isTomorrow ? __('partner.tomorrow') : $departure->date->format('d M'));
                $borderClass = $isToday || $isTomorrow ? 'border-primary' : 'border-gray-300';
            @endphp
            <div class="col-md-6 col-lg-3">
                <div class="card border border-2 {{ $borderClass }} h-100">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="badge {{ $isToday ? 'badge-danger' : ($isTomorrow ? 'badge-primary' : 'badge-info') }}">{{ $dateBadge }}</span>
                            <span class="badge badge-light-{{ $booking->status->color() }}">{{ $booking->status->label() }}</span>
                        </div>
                        <h6 class="fw-bold text-gray-800 mb-2">{{ Str::limit($departure->tour?->name ?? '-', 25) }}</h6>
                        <p class="text-muted fs-7 mb-4">
                            <i class="ki-duotone ki-time fs-6 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ $departure->time }}
                            <i class="ki-duotone ki-people fs-6 ms-3 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            {{ $booking->passengers->count() }} pax
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('partner.bookings.show', $booking) }}" class="btn btn-sm btn-light-primary flex-grow-1">
                                <i class="ki-duotone ki-eye fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                {{ __('partner.details') }}
                            </a>
                            @if (in_array($booking->status, [\App\Enums\BookingStatus::CONFIRMED, \App\Enums\BookingStatus::COMPLETED]))
                            <a href="{{ route('partner.bookings.voucher', $booking) }}" class="btn btn-sm btn-primary" title="{{ __('partner.download_voucher') }}">
                                <i class="ki-duotone ki-file-down fs-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            @endforelse

            <div class="col-md-6 col-lg-3">
                <div class="card border border-2 border-dashed border-gray-300 h-100">
                    <div class="card-body p-6 d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="ki-duotone ki-plus-circle text-gray-400 fs-3x mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <a href="{{ route('partner.bookings.create') }}" class="stretched-link text-gray-600 text-hover-primary fw-semibold">
                            {{ __('partner.create_new_booking') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            {{ $upcomingBookings->withQueryString()->links() }}
        </div>
    </div>
</div>
<!--end::Upcoming Tours-->
@endsection
