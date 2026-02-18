@extends('layouts.admin')

@section('title', __('dashboard.title'))
@section('page-title', __('dashboard.title'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('dashboard.title') }}</li>
@endsection

@section('content')
<!--begin::Stats Row-->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <!--begin::Col-->
    <div class="col-md-6 col-xl-3">
        <div class="card bg-primary hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <i class="ki-duotone ki-ticket text-white fs-2x ms-n1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="text-white fw-bold fs-2 mt-4">{{ $todaysBookingsCount }}</div>
                <div class="text-white opacity-75 fw-semibold fs-6">{{ __('dashboard.todays_bookings') }}</div>
            </div>
        </div>
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-md-6 col-xl-3">
        <div class="card bg-success hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <i class="ki-duotone ki-people text-white fs-2x ms-n1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                </i>
                <div class="text-white fw-bold fs-2 mt-4">{{ $weeklyPassengersCount }}</div>
                <div class="text-white opacity-75 fw-semibold fs-6">{{ __('dashboard.weekly_passengers') }}</div>
            </div>
        </div>
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-md-6 col-xl-3">
        <div class="card bg-warning hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <i class="ki-duotone ki-timer text-white fs-2x ms-n1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="text-white fw-bold fs-2 mt-4">{{ $pendingRequestsCount }}</div>
                <div class="text-white opacity-75 fw-semibold fs-6">{{ __('dashboard.pending_requests') }}</div>
            </div>
        </div>
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-md-6 col-xl-3">
        <div class="card bg-danger hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <i class="ki-duotone ki-wallet text-white fs-2x ms-n1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
                <div class="text-white fw-bold fs-2 mt-4">${{ number_format($totalOutstanding, 0) }}</div>
                <div class="text-white opacity-75 fw-semibold fs-6">{{ __('dashboard.outstanding_balance') }}</div>
            </div>
        </div>
    </div>
    <!--end::Col-->
</div>
<!--end::Stats Row-->

<div class="row g-5 g-xl-8">
    <!--begin::Pending Overbooking Requests-->
    <div class="col-xl-6">
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('dashboard.pending_overbooking_requests') }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ __('dashboard.require_approval_within') }}</span>
                </h3>
                <div class="card-toolbar">
                    <span class="badge badge-light-warning fs-7 fw-bold">{{ $pendingRequests->total() }} {{ __('dashboard.pending') }}</span>
                </div>
            </div>
            <div class="card-body py-3">
                @if($pendingRequests->isEmpty())
                    <div class="text-center py-10">
                        <i class="ki-duotone ki-check-circle text-success fs-3x mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <p class="text-muted fs-6">{{ __('dashboard.no_pending_requests') }}</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-120px">{{ __('dashboard.code') }}</th>
                                <th class="min-w-120px">{{ __('dashboard.partner') }}</th>
                                <th class="min-w-100px">{{ __('dashboard.pax') }}</th>
                                <th class="min-w-80px">{{ __('dashboard.expires') }}</th>
                                <th class="min-w-80px text-end">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingRequests as $booking)
                            @php
                                $expiresAt = $booking->suspended_until ?? $booking->created_at->addHours(2);
                                $minutesRemaining = (int) now()->diffInMinutes($expiresAt, false);
                                $expiresText = $minutesRemaining <= 0 ? 'Expired' : ($minutesRemaining < 60 ? "{$minutesRemaining} min" : floor($minutesRemaining / 60) . 'h ' . ($minutesRemaining % 60) . 'm');
                                $urgencyClass = $minutesRemaining <= 30 ? 'text-danger' : ($minutesRemaining <= 60 ? 'text-warning' : 'text-muted');
                            @endphp
                            <tr>
                                <td><a href="{{ route('admin.bookings.show', $booking) }}" class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $booking->booking_code }}</a></td>
                                <td>
                                    <span class="text-muted fw-semibold d-block fs-7">{{ $booking->partner->name }}</span>
                                    <span class="text-gray-800 fw-bold fs-7">{{ $booking->tourDeparture?->tour?->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @php
                                        $adults = $booking->passengers->where('pax_type', \App\Enums\PaxType::ADULT)->count();
                                        $children = $booking->passengers->where('pax_type', \App\Enums\PaxType::CHILD)->count();
                                        $infants = $booking->passengers->where('pax_type', \App\Enums\PaxType::INFANT)->count();
                                    @endphp
                                    @if($adults > 0)
                                        <span class="badge badge-light-primary">{{ $adults }} ADU</span>
                                    @endif
                                    @if($children > 0)
                                        <span class="badge badge-light-info">{{ $children }} CHD</span>
                                    @endif
                                    @if($infants > 0)
                                        <span class="badge badge-light-secondary">{{ $infants }} INF</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $urgencyClass }} fw-bold">{{ $expiresText }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-icon btn-light-success" title="{{ __('dashboard.approve') }}">
                                                <i class="ki-duotone ki-check fs-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $booking->id }}" title="{{ __('dashboard.reject') }}">
                                            <i class="ki-duotone ki-cross fs-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h3 class="modal-title">{{ __('dashboard.reject_overbooking_request') }}</h3>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>{{ __('dashboard.reject_confirm', ['code' => $booking->booking_code]) }}</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('dashboard.reason_optional') }}</label>
                                                            <textarea name="reason" class="form-control" rows="3" placeholder="{{ __('dashboard.enter_rejection_reason') }}"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                                        <button type="submit" class="btn btn-danger">{{ __('dashboard.reject_request') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $pendingRequests->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <!--end::Pending Overbooking Requests-->

    <!--begin::Today's Departures-->
    <div class="col-xl-6">
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('dashboard.todays_departures') }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ __('dashboard.tours_scheduled_today') }}</span>
                </h3>
                <div class="card-toolbar">
                    <span class="badge badge-light-primary fs-7 fw-bold">{{ $todaysDepartures->total() }} {{ __('dashboard.tours') }}</span>
                </div>
            </div>
            <div class="card-body py-3">
                @if($todaysDepartures->isEmpty())
                    <div class="text-center py-10">
                        <i class="ki-duotone ki-calendar text-muted fs-3x mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <p class="text-muted fs-6">{{ __('dashboard.no_departures_today') }}</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-50px">{{ __('dashboard.time') }}</th>
                                <th class="min-w-140px">{{ __('dashboard.tour') }}</th>
                                <th class="min-w-120px">{{ __('dashboard.capacity') }}</th>
                                <th class="min-w-80px">{{ __('dashboard.status') }}</th>
                                <th class="min-w-80px text-end">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todaysDepartures as $departure)
                            @php
                                $bookedPax = $departure->bookings->sum(fn($b) => $b->passengers->count());
                                $pendingPax = $departure->bookings->where('status', \App\Enums\BookingStatus::SUSPENDED_REQUEST)->sum(fn($b) => $b->passengers->count());
                                $capacity = $departure->tour?->capacity ?? 0;
                                $fillPercent = $capacity > 0 ? round(($bookedPax / $capacity) * 100) : 0;

                                // Progress class
                                $progressClass = match(true) {
                                    $fillPercent >= 100 => 'bg-success',
                                    $fillPercent >= 75 => 'bg-primary',
                                    $fillPercent >= 50 => 'bg-warning',
                                    default => 'bg-danger',
                                };

                                // Status
                                if ($bookedPax >= $capacity) {
                                    $statusText = $pendingPax > 0 ? 'Overbooking' : 'Full';
                                    $statusClass = $pendingPax > 0 ? 'badge-light-warning' : 'badge-light-success';
                                } elseif ($bookedPax > 0) {
                                    $statusText = 'Open';
                                    $statusClass = 'badge-light-primary';
                                } else {
                                    $statusText = 'Empty';
                                    $statusClass = 'badge-light-secondary';
                                }
                            @endphp
                            <tr>
                                <td><span class="fw-bold">{{ $departure->time }}</span></td>
                                <td><span class="text-gray-800 fw-bold fs-6">{{ $departure->tour?->name ?? 'N/A' }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress h-6px w-100 me-2 bg-light-{{ str_replace('bg-', '', $progressClass) }}">
                                            <div class="progress-bar {{ $progressClass }}" style="width: {{ min($fillPercent, 100) }}%"></div>
                                        </div>
                                        <span class="text-muted fs-7 fw-semibold">{{ $bookedPax }}/{{ $capacity }}</span>
                                    </div>
                                </td>
                                <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.departures.manifest', $departure) }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('dashboard.manifest') }}</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $todaysDepartures->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <!--end::Today's Departures-->
</div>

<div class="row g-5 g-xl-8">
    <!--begin::Recent Bookings-->
    <div class="col-xl-8">
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('dashboard.recent_bookings') }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ __('dashboard.latest_bookings_desc') }}</span>
                </h3>
                <div class="card-toolbar">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-light-primary">{{ __('dashboard.view_all') }}</a>
                </div>
            </div>
            <div class="card-body py-3">
                @if($recentBookings->isEmpty())
                    <div class="text-center py-10">
                        <i class="ki-duotone ki-document text-muted fs-3x mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <p class="text-muted fs-6">{{ __('dashboard.no_recent_bookings') }}</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-140px">{{ __('dashboard.booking_code') }}</th>
                                <th class="min-w-120px">{{ __('dashboard.partner') }}</th>
                                <th class="min-w-120px">{{ __('dashboard.tour') }}</th>
                                <th class="min-w-100px">{{ __('dashboard.date_time') }}</th>
                                <th class="min-w-80px">{{ __('dashboard.pax') }}</th>
                                <th class="min-w-80px">{{ __('dashboard.status') }}</th>
                                <th class="min-w-80px">{{ __('dashboard.payment') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                            <tr>
                                <td><a href="{{ route('admin.bookings.show', $booking) }}" class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $booking->booking_code }}</a></td>
                                <td><span class="text-gray-800 fw-semibold">{{ $booking->partner->name }}</span></td>
                                <td><span class="text-muted fw-semibold">{{ $booking->tourDeparture?->tour?->name ?? 'N/A' }}</span></td>
                                <td><span class="text-muted fw-semibold">{{ $booking->tourDeparture?->date?->format('d/m/y') ?? '-' }} {{ $booking->tourDeparture?->time ?? '' }}</span></td>
                                <td>
                                    @php
                                        $adults = $booking->passengers->where('pax_type', \App\Enums\PaxType::ADULT)->count();
                                        $children = $booking->passengers->where('pax_type', \App\Enums\PaxType::CHILD)->count();
                                        $infants = $booking->passengers->where('pax_type', \App\Enums\PaxType::INFANT)->count();
                                    @endphp
                                    @if($adults > 0)
                                        <span class="badge badge-light-primary">{{ $adults }} ADU</span>
                                    @endif
                                    @if($children > 0)
                                        <span class="badge badge-light-info">{{ $children }} CHD</span>
                                    @endif
                                    @if($infants > 0)
                                        <span class="badge badge-light-secondary">{{ $infants }} INF</span>
                                    @endif
                                </td>
                                <td><x-booking.status-badge :status="$booking->status" /></td>
                                <td><x-booking.payment-badge :status="$booking->payment_status" /></td>
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

    <!--begin::Partner Outstanding-->
    <div class="col-xl-4">
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900 fs-3 mb-1">{{ __('dashboard.outstanding_by_partner') }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ __('dashboard.unpaid_booking_balances') }}</span>
                </h3>
            </div>
            <div class="card-body pt-5">
                @if($partnerOutstanding->isEmpty())
                    <div class="text-center py-10">
                        <i class="ki-duotone ki-check-circle text-success fs-3x mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <p class="text-muted fs-6">{{ __('dashboard.all_partners_up_to_date') }}</p>
                    </div>
                @else
                    @php
                        $colors = ['primary', 'success', 'warning', 'info', 'danger'];
                    @endphp
                    @foreach($partnerOutstanding as $index => $partner)
                    <!--begin::Item-->
                    <div class="d-flex align-items-center @if(!$loop->last) mb-7 @endif">
                        <div class="symbol symbol-50px me-5">
                            <span class="symbol-label bg-light-{{ $colors[$index % count($colors)] }}">
                                <i class="ki-duotone ki-building fs-2x text-{{ $colors[$index % count($colors)] }}">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                        </div>
                        <div class="d-flex flex-stack flex-row-fluid">
                            <div class="me-5">
                                <a href="{{ route('admin.partners.show', $partner) }}" class="text-gray-800 fw-bold text-hover-primary fs-6">{{ $partner->name }}</a>
                                <span class="text-gray-500 fw-semibold fs-7 d-block">{{ $partner->unpaid_bookings }} {{ __('dashboard.bookings') }}</span>
                            </div>
                            <span class="text-danger fw-bold fs-6">${{ number_format($partner->outstanding_balance, 0) }}</span>
                        </div>
                    </div>
                    <!--end::Item-->
                    @endforeach
                    <div class="mt-4">
                        {{ $partnerOutstanding->withQueryString()->links() }}
                    </div>
                    <div class="separator separator-dashed my-5"></div>
                    <a href="{{ route('admin.accounting.index') }}" class="btn btn-light-primary w-100">
                        <i class="ki-duotone ki-eye fs-4 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        {{ __('dashboard.view_all_outstanding') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
    <!--end::Partner Outstanding-->
</div>
@endsection
