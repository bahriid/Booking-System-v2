@extends('layouts.admin')

@section('title', __('calendar.title'))
@section('page-title', __('calendar.title'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('calendar.breadcrumb') }}</li>
@endsection

@section('toolbar-actions')
<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bulkAvailabilityModal">
    <i class="ki-duotone ki-calendar-add fs-4 me-2">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
        <span class="path4"></span>
        <span class="path5"></span>
        <span class="path6"></span>
    </i>
    {{ __('calendar.create_availability') }}
</button>
@endsection

@section('content')
@if (session('success'))
    <x-ui.alert type="success" :message="session('success')" dismissible class="mb-5" />
@endif

@if (session('warning'))
    <x-ui.alert type="warning" :message="session('warning')" dismissible class="mb-5" />
@endif

@if (session('error'))
    <x-ui.alert type="danger" :message="session('error')" dismissible class="mb-5" />
@endif

@if ($errors->any())
    <div class="alert alert-danger mb-5">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-5 g-xl-8">
    <!--begin::Sidebar-->
    <div class="col-xl-3">
        <!--begin::Tour Filter-->
        <div class="card mb-5">
            <div class="card-header border-0 pt-5 pb-0">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('calendar.select_tour') }}</span>
                </h3>
            </div>
            <div class="card-body pt-5">
                <select class="form-select form-select-solid" id="tourFilter" data-control="select2" data-placeholder="{{ __('calendar.all_tours') }}">
                    <option value="">{{ __('calendar.all_tours') }}</option>
                    @foreach ($tours as $tour)
                        <option value="{{ $tour->id }}" {{ $selectedTourId == $tour->id ? 'selected' : '' }}>{{ $tour->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!--end::Tour Filter-->

        <!--begin::Quick Actions-->
        <div class="card mb-5">
            <div class="card-header border-0 pt-5 pb-0">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('calendar.quick_actions') }}</span>
                </h3>
            </div>
            <div class="card-body pt-5 d-grid gap-2">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bulkAvailabilityModal">
                    <i class="ki-duotone ki-calendar-add fs-4 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                        <span class="path6"></span>
                    </i>
                    {{ __('calendar.create_availability') }}
                </button>
                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bulkCloseModal">
                    <i class="ki-duotone ki-cross-circle fs-4 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ __('calendar.quick_close_range') }}
                </button>
            </div>
        </div>
        <!--end::Quick Actions-->

        <!--begin::Legend-->
        <div class="card mb-5">
            <div class="card-header border-0 pt-5 pb-0">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('calendar.legend') }}</span>
                </h3>
            </div>
            <div class="card-body pt-5">
                <div class="d-flex align-items-center mb-3">
                    <span class="bullet bullet-vertical h-15px w-4px me-3" style="background-color: #009ef7;"></span>
                    <span class="text-gray-600 fw-semibold fs-7">{{ __('calendar.legend_available') }}</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <span class="bullet bullet-vertical h-15px w-4px me-3" style="background-color: #50cd89;"></span>
                    <span class="text-gray-600 fw-semibold fs-7">{{ __('calendar.legend_almost_full') }}</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <span class="bullet bullet-vertical h-15px w-4px me-3" style="background-color: #ffc700;"></span>
                    <span class="text-gray-600 fw-semibold fs-7">{{ __('calendar.legend_full') }}</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <span class="bullet bullet-vertical h-15px w-4px me-3" style="background-color: #7239ea;"></span>
                    <span class="text-gray-600 fw-semibold fs-7">{{ __('calendar.legend_closed') }}</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="bullet bullet-vertical h-15px w-4px me-3" style="background-color: #f1416c;"></span>
                    <span class="text-gray-600 fw-semibold fs-7">{{ __('calendar.legend_cancelled') }}</span>
                </div>
            </div>
        </div>
        <!--end::Legend-->
    </div>
    <!--end::Sidebar-->

    <!--begin::Calendar-->
    <div class="col-xl-9">
        <div class="card">
            <div class="card-body">
                <div id="kt_calendar"></div>
            </div>
        </div>
    </div>
    <!--end::Calendar-->
</div>

<!--begin::Bulk Availability Modal-->
<div class="modal fade" id="bulkAvailabilityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.departures.bulk') }}" method="POST" id="bulkAvailabilityForm">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('calendar.bulk_availability_title') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="row g-9 mb-8">
                        <div class="col-md-6">
                            <label class="required fs-6 fw-semibold mb-2">{{ __('calendar.tour') }}</label>
                            <select class="form-select form-select-solid" name="tour_id" data-control="select2" data-placeholder="{{ __('calendar.select_tour_placeholder') }}" data-dropdown-parent="#bulkAvailabilityModal" required>
                                <option></option>
                                @foreach ($tours as $tour)
                                    <option value="{{ $tour->id }}" {{ $selectedTourId == $tour->id ? 'selected' : '' }}>{{ $tour->name }} ({{ $tour->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="required fs-6 fw-semibold mb-2">{{ __('calendar.season') }}</label>
                            <select class="form-select form-select-solid" name="season" required>
                                @foreach (\App\Enums\Season::cases() as $season)
                                    <option value="{{ $season->value }}">{{ $season->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-6">
                            <label class="required fs-6 fw-semibold mb-2">{{ __('calendar.start_date') }}</label>
                            <input type="date" class="form-control form-control-solid" name="start_date" required min="{{ date('Y-m-d') }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="required fs-6 fw-semibold mb-2">{{ __('calendar.end_date') }}</label>
                            <input type="date" class="form-control form-control-solid" name="end_date" required min="{{ date('Y-m-d') }}" />
                        </div>
                    </div>
                    <div class="mb-8">
                        <label class="required fs-6 fw-semibold mb-2">{{ __('calendar.days_of_week') }}</label>
                        <div class="d-flex flex-wrap gap-5">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="day_mon" name="days[]" value="1" checked />
                                <label class="form-check-label" for="day_mon">{{ __('calendar.mon') }}</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="day_tue" name="days[]" value="2" checked />
                                <label class="form-check-label" for="day_tue">{{ __('calendar.tue') }}</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="day_wed" name="days[]" value="3" checked />
                                <label class="form-check-label" for="day_wed">{{ __('calendar.wed') }}</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="day_thu" name="days[]" value="4" checked />
                                <label class="form-check-label" for="day_thu">{{ __('calendar.thu') }}</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="day_fri" name="days[]" value="5" checked />
                                <label class="form-check-label" for="day_fri">{{ __('calendar.fri') }}</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="day_sat" name="days[]" value="6" checked />
                                <label class="form-check-label" for="day_sat">{{ __('calendar.sat') }}</label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="day_sun" name="days[]" value="0" checked />
                                <label class="form-check-label" for="day_sun">{{ __('calendar.sun') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-4">
                            <label class="required fs-6 fw-semibold mb-2">{{ __('calendar.time') }}</label>
                            <input type="time" class="form-control form-control-solid" name="time" value="09:00" required />
                        </div>
                        <div class="col-md-4">
                            <label class="required fs-6 fw-semibold mb-2">{{ __('calendar.capacity') }}</label>
                            <div class="input-group input-group-solid">
                                <input type="number" class="form-control" name="capacity" value="8" min="1" max="500" required />
                                <span class="input-group-text">{{ __('calendar.pax') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fs-6 fw-semibold mb-2">{{ __('calendar.notes') }}</label>
                            <input type="text" class="form-control form-control-solid" name="notes" placeholder="{{ __('calendar.optional_notes') }}" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('calendar.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('calendar.create_availability') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Bulk Availability Modal-->

<!--begin::Edit Departure Modal-->
<div class="modal fade" id="editDepartureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editDepartureForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('calendar.edit_departure_title') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <!--begin::Info-->
                    <div class="d-flex flex-stack bg-light rounded p-5 mb-8">
                        <div class="text-center flex-grow-1">
                            <span class="text-muted fw-semibold d-block fs-7">{{ __('calendar.tour_label') }}</span>
                            <span class="text-gray-800 fw-bold fs-6" id="modalTourName">-</span>
                        </div>
                        <div class="text-center flex-grow-1 border-start border-gray-300">
                            <span class="text-muted fw-semibold d-block fs-7">{{ __('calendar.date_label') }}</span>
                            <span class="text-gray-800 fw-bold fs-6" id="modalDate">-</span>
                        </div>
                        <div class="text-center flex-grow-1 border-start border-gray-300">
                            <span class="text-muted fw-semibold d-block fs-7">{{ __('calendar.time_label') }}</span>
                            <span class="text-gray-800 fw-bold fs-6" id="modalTime">-</span>
                        </div>
                    </div>
                    <!--end::Info-->

                    <div class="row g-9 mb-8">
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold mb-2">{{ __('calendar.capacity') }}</label>
                            <div class="input-group input-group-solid">
                                <input type="number" class="form-control" name="capacity" id="modalCapacity" min="1" max="500" required />
                                <span class="input-group-text">{{ __('calendar.pax') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold mb-2">{{ __('calendar.status') }}</label>
                            <select class="form-select form-select-solid" name="status" id="modalStatus" required>
                                @foreach (\App\Enums\TourDepartureStatus::cases() as $status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold mb-2">{{ __('calendar.time') }}</label>
                            <input type="time" class="form-control form-control-solid" name="time" id="modalTimeInput" required />
                        </div>
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold mb-2">{{ __('calendar.assigned_driver') }}</label>
                            <select class="form-select form-select-solid" name="driver_id" id="modalDriverId">
                                <option value="">{{ __('calendar.no_driver_assigned') }}</option>
                                @foreach (\App\Models\User::where('role', \App\Enums\UserRole::DRIVER)->where('is_active', true)->orderBy('name')->get() as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-8">
                        <label class="fs-6 fw-semibold mb-2">{{ __('calendar.notes') }}</label>
                        <textarea class="form-control form-control-solid" name="notes" id="modalNotes" rows="2"></textarea>
                    </div>

                    <!--begin::Current Bookings-->
                    <div class="border border-dashed border-gray-300 rounded p-5">
                        <h6 class="fw-bold mb-4">{{ __('calendar.current_bookings') }}</h6>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">{{ __('calendar.booked_seats') }}</span>
                            <span class="fw-bold" id="modalBookedSeats">0 / 0</span>
                        </div>
                        <div class="progress h-8px mb-5">
                            <div class="progress-bar bg-primary" id="modalProgressBar" style="width: 0%"></div>
                        </div>
                        <a href="#" class="btn btn-light-primary btn-sm w-100" id="modalViewBookings">
                            <i class="ki-duotone ki-eye fs-4 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            {{ __('calendar.view_departure_details') }}
                        </a>
                    </div>
                    <!--end::Current Bookings-->
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('calendar.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('calendar.save_changes') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Edit Departure Modal-->

<!--begin::Bulk Close Modal-->
<div class="modal fade" id="bulkCloseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.departures.bulk-close') }}" method="POST" id="bulkCloseForm">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('calendar.bulk_close_title') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-8">
                        <i class="ki-duotone ki-information fs-2tx text-warning me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-700">
                                    {{ __('calendar.bulk_close_warning') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-12">
                            <label class="fs-6 fw-semibold mb-2">{{ __('calendar.tour_optional') }}</label>
                            <select class="form-select form-select-solid" name="tour_id" data-control="select2" data-placeholder="{{ __('calendar.all_tours') }}" data-dropdown-parent="#bulkCloseModal" data-allow-clear="true">
                                <option value="">{{ __('calendar.all_tours') }}</option>
                                @foreach ($tours as $tour)
                                    <option value="{{ $tour->id }}" {{ $selectedTourId == $tour->id ? 'selected' : '' }}>{{ $tour->name }} ({{ $tour->code }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('calendar.leave_empty_all_tours') }}</div>
                        </div>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-6">
                            <label class="required fs-6 fw-semibold mb-2">{{ __('calendar.start_date') }}</label>
                            <input type="date" class="form-control form-control-solid" name="start_date" required min="{{ date('Y-m-d') }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="required fs-6 fw-semibold mb-2">{{ __('calendar.end_date') }}</label>
                            <input type="date" class="form-control form-control-solid" name="end_date" required min="{{ date('Y-m-d') }}" />
                        </div>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-12">
                            <label class="fs-6 fw-semibold mb-2">{{ __('calendar.reason_optional') }}</label>
                            <input type="text" class="form-control form-control-solid" name="reason" placeholder="{{ __('calendar.reason_placeholder') }}" />
                            <div class="form-text">{{ __('calendar.reason_notification_note') }}</div>
                        </div>
                    </div>
                    <div class="row g-9">
                        <div class="col-12">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="notify_partners" id="notifyPartners" value="1" checked>
                                <label class="form-check-label fw-semibold" for="notifyPartners">
                                    {{ __('calendar.notify_partners') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('calendar.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">
                        <span class="indicator-label">{{ __('calendar.close_departures') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Bulk Close Modal-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('kt_calendar');
    var tourFilter = document.getElementById('tourFilter');
    var selectedTourId = '{{ $selectedTourId }}';
    var editModal = new bootstrap.Modal(document.getElementById('editDepartureModal'));
    var tourCapacities = @json($tours->pluck('default_capacity', 'id'));

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: {
            today: '{{ __('calendar.today') }}',
            month: '{{ __('calendar.month') }}',
            week: '{{ __('calendar.week') }}',
            list: '{{ __('calendar.list') }}'
        },
        events: function(info, successCallback, failureCallback) {
            var url = '{{ route("admin.departures.events") }}';
            var params = new URLSearchParams({
                start: info.startStr,
                end: info.endStr
            });

            if (selectedTourId) {
                params.append('tour', selectedTourId);
            }

            fetch(url + '?' + params.toString())
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        eventClick: function(info) {
            var event = info.event;
            var props = event.extendedProps;

            // Populate modal
            document.getElementById('modalTourName').textContent = props.tour_name;
            document.getElementById('modalDate').textContent = event.start.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('modalTime').textContent = props.time;
            document.getElementById('modalCapacity').value = props.capacity;
            // Set max capacity based on tour
            var maxCapacity = tourCapacities[props.tour_id] || 500;
            document.getElementById('modalCapacity').max = maxCapacity;
            document.getElementById('modalStatus').value = props.status;
            // Normalize time to HH:MM (strip seconds if present)
            var timeValue = props.time ? props.time.substring(0, 5) : '';
            document.getElementById('modalTimeInput').value = timeValue;
            document.getElementById('modalDriverId').value = props.driver_id || '';
            document.getElementById('modalNotes').value = props.notes || '';
            document.getElementById('modalBookedSeats').textContent = props.booked + ' / ' + props.capacity;

            var percentage = props.capacity > 0 ? (props.booked / props.capacity * 100) : 0;
            document.getElementById('modalProgressBar').style.width = percentage + '%';

            // Update form action
            document.getElementById('editDepartureForm').action = '{{ url("admin/departures") }}/' + event.id;
            document.getElementById('modalViewBookings').href = '{{ url("admin/departures") }}/' + event.id;

            editModal.show();
        },
        dateClick: function(info) {
            // Open bulk create modal with date pre-filled
            document.querySelector('[name="start_date"]').value = info.dateStr;
            document.querySelector('[name="end_date"]').value = info.dateStr;
            var modal = new bootstrap.Modal(document.getElementById('bulkAvailabilityModal'));
            modal.show();
        }
    });

    calendar.render();

    // Update capacity max when tour is selected in bulk create modal
    var bulkTourSelect = document.querySelector('#bulkAvailabilityForm [name="tour_id"]');
    var bulkCapacityInput = document.querySelector('#bulkAvailabilityForm [name="capacity"]');
    if (bulkTourSelect && bulkCapacityInput) {
        $(bulkTourSelect).on('change', function() {
            var tourId = this.value;
            var maxCap = tourCapacities[tourId] || 500;
            bulkCapacityInput.max = maxCap;
            if (parseInt(bulkCapacityInput.value) > maxCap) {
                bulkCapacityInput.value = maxCap;
            }
        });
        // Trigger on load if a tour is pre-selected
        if (bulkTourSelect.value) {
            var maxCap = tourCapacities[bulkTourSelect.value] || 500;
            bulkCapacityInput.max = maxCap;
        }
    }

    // Tour filter change
    $(tourFilter).on('change', function() {
        selectedTourId = this.value;
        // Update URL without reloading
        var url = new URL(window.location.href);
        if (selectedTourId) {
            url.searchParams.set('tour', selectedTourId);
        } else {
            url.searchParams.delete('tour');
        }
        window.history.replaceState({}, '', url);
        calendar.refetchEvents();
    });
});
</script>
@endpush

@push('styles')
<style>
    .fc-event {
        cursor: pointer;
        font-size: 0.8rem;
        padding: 2px 4px;
    }
</style>
@endpush
