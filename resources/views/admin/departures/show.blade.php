@extends('layouts.admin')

@section('title', ($departure->tour?->name ?? 'Unknown Tour') . ' - ' . $departure->date->format('M d, Y'))
@section('page-title', __('departures.departure_details'))

@section('breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('admin.calendar') }}" class="text-muted text-hover-primary">{{ __('departures.calendar') }}</a>
</li>
<li class="breadcrumb-item">
    <span class="bullet bg-gray-500 w-5px h-2px"></span>
</li>
<li class="breadcrumb-item text-muted">{{ $departure->tour?->code ?? '-' }} - {{ $departure->date->format('M d') }}</li>
@endsection

@section('toolbar-actions')
<div class="d-flex gap-2">
    @if ($departure->bookings->count() > 0)
    <a href="{{ route('admin.departures.manifest', $departure) }}" class="btn btn-sm btn-light-primary">
        <i class="ki-duotone ki-document fs-5 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        {{ __('departures.download_manifest') }}
    </a>
    @endif
    <a href="{{ route('admin.calendar', ['tour' => $departure->tour_id]) }}" class="btn btn-sm btn-light">
        <i class="ki-duotone ki-arrow-left fs-5 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        {{ __('departures.back_to_calendar') }}
    </a>
</div>
@endsection

@section('content')
@if (session('success'))
    <x-ui.alert type="success" :message="session('success')" dismissible class="mb-5" />
@endif

@if (session('error'))
    <x-ui.alert type="danger" :message="session('error')" dismissible class="mb-5" />
@endif

<div class="row g-5 g-xl-10">
    <!--begin::Main Column-->
    <div class="col-xl-8">
        <!--begin::Departure Summary Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <span class="me-2">
                        <i class="ki-duotone ki-calendar-8 fs-2 text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                        </i>
                    </span>
                    {{ __('departures.departure_information') }}
                </div>
                <div class="card-toolbar">
                    <span class="badge badge-light-{{ $departure->status->color() }} fs-7 fw-bold">{{ $departure->status->label() }}</span>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted fw-semibold w-150px">{{ __('departures.tour') }}</td>
                                <td class="fw-bold">
                                    @if($departure->tour)
                                    <a href="{{ route('admin.tours.show', $departure->tour) }}" class="text-primary">{{ $departure->tour->name }}</a>
                                    <span class="badge badge-light ms-2">{{ $departure->tour->code }}</span>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('departures.date') }}</td>
                                <td class="fw-bold fs-5">{{ $departure->date->format('l, F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('departures.time') }}</td>
                                <td>{{ $departure->time }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('departures.season') }}</td>
                                <td><span class="badge badge-light-info">{{ $departure->season->label() }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted fw-semibold w-150px">{{ __('departures.capacity') }}</td>
                                <td class="fw-bold">{{ $departure->capacity }} {{ __('departures.passengers') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('departures.booked') }}</td>
                                <td class="fw-bold">{{ $departure->booked_seats }} {{ __('departures.passengers') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('departures.available') }}</td>
                                <td class="fw-bold text-{{ $departure->remaining_seats > 0 ? 'success' : 'danger' }}">{{ $departure->remaining_seats }} {{ __('departures.seats') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">{{ __('departures.driver') }}</td>
                                <td>{{ $departure->driver?->name ?? __('departures.not_assigned') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if ($departure->notes)
                <div class="separator my-4"></div>
                <div class="row">
                    <div class="col-12">
                        <span class="text-muted fw-semibold">{{ __('departures.notes') }}:</span>
                        <p class="mb-0 mt-2">{{ $departure->notes }}</p>
                    </div>
                </div>
                @endif

                <!--begin::Progress Bar-->
                <div class="separator my-4"></div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fw-semibold">{{ __('departures.capacity_utilization') }}</span>
                    <span class="fw-bold">{{ $departure->capacity > 0 ? round(($departure->booked_seats / $departure->capacity) * 100) : 0 }}%</span>
                </div>
                @php
                    $percentage = $departure->capacity > 0 ? ($departure->booked_seats / $departure->capacity) * 100 : 0;
                    $progressColor = $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success');
                @endphp
                <div class="progress h-10px">
                    <div class="progress-bar bg-{{ $progressColor }}" style="width: {{ $percentage }}%"></div>
                </div>
                <!--end::Progress Bar-->
            </div>
        </div>
        <!--end::Departure Summary Card-->

        <!--begin::Bookings Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <span class="me-2">
                        <i class="ki-duotone ki-document fs-2 text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                    {{ __('departures.bookings') }}
                    <span class="badge badge-light-primary ms-2">{{ __('departures.bookings_count', ['count' => $departure->bookings->count()]) }}</span>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th>{{ __('departures.booking_code') }}</th>
                                <th>{{ __('departures.partner') }}</th>
                                <th>{{ ucfirst(__('departures.passengers')) }}</th>
                                <th>{{ __('departures.status') }}</th>
                                <th>{{ __('departures.amount') }}</th>
                                <th class="text-end">{{ __('departures.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse ($departure->bookings as $booking)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary fw-bold">{{ $booking->booking_code }}</a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.partners.show', $booking->partner) }}" class="text-gray-800 text-hover-primary">{{ $booking->partner->name }}</a>
                                </td>
                                <td>
                                    @php
                                        $paxCounts = $booking->passengers->groupBy('pax_type')->map->count();
                                    @endphp
                                    @foreach ($paxCounts as $type => $count)
                                        <span class="badge badge-light-{{ $type === 'adult' ? 'primary' : ($type === 'child' ? 'info' : 'secondary') }} me-1">{{ $count }} {{ strtoupper(substr($type, 0, 3)) }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge badge-light-{{ $booking->status->color() }}">{{ $booking->status->label() }}</span>
                                </td>
                                <td>${{ number_format($booking->total_amount, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-light">{{ __('departures.view') }}</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-muted">{{ __('departures.no_bookings_for_departure') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end::Bookings Card-->
    </div>
    <!--end::Main Column-->

    <!--begin::Sidebar-->
    <div class="col-xl-4">
        <!--begin::Status Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('departures.actions') }}</div>
            </div>
            <div class="card-body py-4">
                @if ($departure->status !== \App\Enums\TourDepartureStatus::CANCELLED)
                <div class="d-flex flex-column gap-3">
                    <!--begin::Edit Form-->
                    <form action="{{ route('admin.departures.update', $departure) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">{{ __('departures.status') }}</label>
                            <select name="status" class="form-select form-select-solid">
                                @foreach (\App\Enums\TourDepartureStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ $departure->status === $status ? 'selected' : '' }}>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">{{ __('departures.capacity') }}</label>
                            <input type="number" name="capacity" class="form-control form-control-solid" value="{{ $departure->capacity }}" min="1" max="500">
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">{{ __('departures.time') }}</label>
                            <input type="time" name="time" class="form-control form-control-solid" value="{{ $departure->time }}">
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">{{ __('departures.driver') }}</label>
                            <select name="driver_id" class="form-select form-select-solid">
                                <option value="">{{ __('departures.not_assigned') }}</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ $departure->driver_id === $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">{{ __('departures.notes') }}</label>
                            <textarea name="notes" class="form-control form-control-solid" rows="2">{{ $departure->notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ki-duotone ki-check fs-5 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('departures.save_changes') }}
                        </button>
                    </form>
                    <!--end::Edit Form-->

                    <div class="separator my-3"></div>

                    <!--begin::Cancel Button-->
                    <button type="button" class="btn btn-light-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelDepartureModal">
                        <i class="ki-duotone ki-cross-circle fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('departures.cancel_departure') }}
                    </button>
                    <!--end::Cancel Button-->
                </div>
                @else
                <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4">
                    <i class="ki-duotone ki-information-2 fs-2tx text-danger me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <span class="fs-6 text-gray-800 fw-bold">{{ __('departures.departure_cancelled') }}</span>
                        <span class="fs-7 text-muted">{{ __('departures.no_further_changes') }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <!--end::Status Card-->

        <!--begin::Passengers Summary Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('departures.passengers_summary') }}</div>
            </div>
            <div class="card-body py-4">
                @php
                    $allPassengers = $departure->bookings->flatMap->passengers;
                    $paxCounts = $allPassengers->groupBy(fn($p) => $p->pax_type->value)->map->count();
                @endphp
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('departures.adults') }}</span>
                    <span class="fw-bold">{{ $paxCounts['adult'] ?? 0 }}</span>
                </div>
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('departures.children') }}</span>
                    <span class="fw-bold">{{ $paxCounts['child'] ?? 0 }}</span>
                </div>
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('departures.infants') }}</span>
                    <span class="fw-bold">{{ $paxCounts['infant'] ?? 0 }}</span>
                </div>
                <div class="separator my-4"></div>
                <div class="d-flex flex-stack">
                    <span class="fw-bold">{{ __('departures.total_passengers') }}</span>
                    <span class="fw-bold fs-5 text-primary">{{ $allPassengers->count() }}</span>
                </div>
            </div>
        </div>
        <!--end::Passengers Summary Card-->
    </div>
    <!--end::Sidebar-->
</div>

@if ($departure->status !== \App\Enums\TourDepartureStatus::CANCELLED)
<!--begin::Cancel Departure Modal-->
<div class="modal fade" id="cancelDepartureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.departures.cancel', $departure) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('departures.cancel_departure_title') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body">
                    @if ($departure->bookings->count() > 0)
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                        <i class="ki-duotone ki-information-2 fs-2tx text-warning me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <span class="fs-6 text-gray-800 fw-bold">{{ __('departures.has_active_bookings', ['count' => $departure->bookings->count()]) }}</span>
                            <span class="fs-7 text-muted">{{ __('departures.bookings_will_be_cancelled') }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="mb-5">
                        <label class="form-label fw-semibold fs-6">{{ __('departures.cancellation_reason') }}</label>
                        <textarea class="form-control form-control-solid" name="reason" rows="3" placeholder="{{ __('departures.reason_placeholder') }}"></textarea>
                    </div>

                    <div class="mb-5">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="is_bad_weather" id="isBadWeather" value="1">
                            <label class="form-check-label fw-semibold" for="isBadWeather">
                                {{ __('departures.bad_weather_cancellation') }}
                            </label>
                        </div>
                        <div class="form-text mt-2">{{ __('departures.bad_weather_help') }}</div>
                    </div>

                    <div class="mb-0">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="notify_partners" id="notifyPartners" value="1" checked>
                            <label class="form-check-label fw-semibold" for="notifyPartners">
                                {{ __('departures.notify_partners_email') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('departures.close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('departures.confirm_cancellation') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Cancel Departure Modal-->
@endif
@endsection
