@extends('layouts.admin')

@section('title', __('bookings.create_booking'))
@section('page-title', __('bookings.create_booking'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('admin.bookings.index') }}" class="text-muted text-hover-primary">{{ __('bookings.bookings') }}</a>
</li>
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('bookings.create_booking') }}</li>
@endsection

@section('content')
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

<!--begin::Stepper-->
<div class="stepper stepper-pills stepper-column d-flex flex-column flex-xl-row flex-row-fluid gap-10" id="kt_create_booking_stepper">
    <!--begin::Aside-->
    <div class="card d-flex justify-content-center justify-content-xl-start flex-row-auto w-100 w-xl-300px w-xxl-400px">
        <div class="card-body px-6 px-lg-10 px-xxl-15 py-20">
            <!--begin::Nav-->
            <div class="stepper-nav">
                <!--begin::Step 1-->
                <div class="stepper-item current" data-kt-stepper-element="nav">
                    <div class="stepper-wrapper">
                        <div class="stepper-icon w-40px h-40px">
                            <i class="ki-duotone ki-check fs-2 stepper-check"></i>
                            <span class="stepper-number">1</span>
                        </div>
                        <div class="stepper-label">
                            <h3 class="stepper-title">{{ __('bookings.select_partner') }}</h3>
                            <div class="stepper-desc fw-semibold">{{ __('bookings.choose_partner') }}</div>
                        </div>
                    </div>
                    <div class="stepper-line h-40px"></div>
                </div>
                <!--end::Step 1-->
                <!--begin::Step 2-->
                <div class="stepper-item" data-kt-stepper-element="nav">
                    <div class="stepper-wrapper">
                        <div class="stepper-icon w-40px h-40px">
                            <i class="ki-duotone ki-check fs-2 stepper-check"></i>
                            <span class="stepper-number">2</span>
                        </div>
                        <div class="stepper-label">
                            <h3 class="stepper-title">{{ __('bookings.select_tour') }}</h3>
                            <div class="stepper-desc fw-semibold">{{ __('bookings.choose_tour') }}</div>
                        </div>
                    </div>
                    <div class="stepper-line h-40px"></div>
                </div>
                <!--end::Step 2-->
                <!--begin::Step 3-->
                <div class="stepper-item" data-kt-stepper-element="nav">
                    <div class="stepper-wrapper">
                        <div class="stepper-icon w-40px h-40px">
                            <i class="ki-duotone ki-check fs-2 stepper-check"></i>
                            <span class="stepper-number">3</span>
                        </div>
                        <div class="stepper-label">
                            <h3 class="stepper-title">{{ __('bookings.date_passengers') }}</h3>
                            <div class="stepper-desc fw-semibold">{{ __('bookings.select_date_count') }}</div>
                        </div>
                    </div>
                    <div class="stepper-line h-40px"></div>
                </div>
                <!--end::Step 3-->
                <!--begin::Step 4-->
                <div class="stepper-item" data-kt-stepper-element="nav">
                    <div class="stepper-wrapper">
                        <div class="stepper-icon w-40px h-40px">
                            <i class="ki-duotone ki-check fs-2 stepper-check"></i>
                            <span class="stepper-number">4</span>
                        </div>
                        <div class="stepper-label">
                            <h3 class="stepper-title">{{ __('bookings.passenger_details') }}</h3>
                            <div class="stepper-desc fw-semibold">{{ __('bookings.enter_passenger_info') }}</div>
                        </div>
                    </div>
                    <div class="stepper-line h-40px"></div>
                </div>
                <!--end::Step 4-->
                <!--begin::Step 5-->
                <div class="stepper-item" data-kt-stepper-element="nav">
                    <div class="stepper-wrapper">
                        <div class="stepper-icon w-40px h-40px">
                            <i class="ki-duotone ki-check fs-2 stepper-check"></i>
                            <span class="stepper-number">5</span>
                        </div>
                        <div class="stepper-label">
                            <h3 class="stepper-title">{{ __('bookings.review_submit') }}</h3>
                            <div class="stepper-desc fw-semibold">{{ __('bookings.confirm_booking') }}</div>
                        </div>
                    </div>
                </div>
                <!--end::Step 5-->
            </div>
            <!--end::Nav-->
        </div>
    </div>
    <!--end::Aside-->

    <!--begin::Content-->
    <div class="card d-flex flex-row-fluid flex-center">
        <form class="card-body py-20 w-100 mw-xl-700px px-9" id="kt_create_booking_form" method="POST" action="{{ route('admin.bookings.store') }}">
            <!--begin::Step 1 - Partner Selection-->
            <div class="current" data-kt-stepper-element="content">
                @csrf
                <input type="hidden" name="partner_id" id="partner_id" value="">
                <input type="hidden" name="tour_departure_id" id="tour_departure_id" value="">
                <div class="w-100">
                    <div class="pb-10 pb-lg-15">
                        <h2 class="fw-bold text-gray-900">{{ __('bookings.select_partner') }}</h2>
                        <div class="text-muted fw-semibold fs-6">{{ __('bookings.select_partner_for_booking') }}</div>
                    </div>
                    <div class="fv-row">
                        @if($partners->isEmpty())
                            <div class="alert alert-warning">
                                <i class="ki-duotone ki-information-5 fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                {{ __('bookings.no_active_partners') }}
                            </div>
                        @else
                            <label class="form-label required mb-3">{{ __('bookings.partner') }}</label>
                            <select class="form-select form-select-lg form-select-solid" id="partner_select" data-control="select2" data-placeholder="{{ __('bookings.select_partner') }}">
                                <option value="">{{ __('bookings.select_partner') }}</option>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}" data-name="{{ $partner->name }}">{{ $partner->name }} ({{ $partner->company_name }})</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>
            <!--end::Step 1-->

            <!--begin::Step 2 - Tour Selection-->
            <div data-kt-stepper-element="content">
                <div class="w-100">
                    <div class="pb-10 pb-lg-15">
                        <h2 class="fw-bold text-gray-900">{{ __('bookings.select_tour') }}</h2>
                        <div class="text-muted fw-semibold fs-6">{{ __('bookings.choose_tour_to_book') }}</div>
                    </div>
                    <div class="fv-row">
                        @if($tours->isEmpty())
                            <div class="alert alert-warning">
                                <i class="ki-duotone ki-information-5 fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                {{ __('bookings.no_tours_available') }}
                            </div>
                        @else
                            <div class="row g-5" id="tour-selection">
                                @php
                                    $colors = ['primary', 'success', 'warning', 'danger', 'info'];
                                @endphp
                                @foreach($tours as $index => $tour)
                                    @php $color = $colors[$index % count($colors)]; @endphp
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check tour-radio" name="tour_id" value="{{ $tour->id }}" id="tour_{{ $tour->id }}" data-tour-code="{{ $tour->code }}" data-tour-name="{{ $tour->name }}" {{ $index === 0 ? 'checked' : '' }} />
                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-{{ $color }} p-7 d-flex align-items-center h-100" for="tour_{{ $tour->id }}">
                                            <span class="symbol symbol-circle symbol-50px me-5">
                                                <span class="symbol-label bg-light-{{ $color }}">
                                                    <i class="ki-duotone ki-geolocation fs-2x text-{{ $color }}">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                            </span>
                                            <span class="d-block fw-semibold text-start">
                                                <span class="text-gray-900 fw-bold d-block fs-4 mb-2">{{ $tour->name }}</span>
                                                <span class="text-muted fw-semibold fs-6">{{ $tour->code }}</span>
                                                <span class="d-flex gap-2 mt-2">
                                                    <span class="badge badge-light-{{ $color }}">{{ __('bookings.max_pax', ['count' => $tour->default_capacity]) }}</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!--end::Step 2-->

            <!--begin::Step 3 - Date & Passengers-->
            <div data-kt-stepper-element="content">
                <div class="w-100">
                    <div class="pb-10 pb-lg-12">
                        <h2 class="fw-bold text-gray-900">{{ __('bookings.date_passengers') }}</h2>
                        <div class="text-muted fw-semibold fs-6">{{ __('bookings.select_departure_date') }}</div>
                    </div>

                    <div class="fv-row mb-10">
                        <label class="form-label required">{{ __('bookings.tour_date') }}</label>
                        <input type="text" class="form-control form-control-lg form-control-solid" id="tour_date" placeholder="{{ __('bookings.select_date') }}" readonly />
                    </div>

                    <div class="fv-row mb-10" id="time-slots-container" style="display: none;">
                        <label class="d-flex align-items-center form-label mb-5">{{ __('bookings.available_time_slots') }}</label>
                        <div class="row g-5" id="time-slots">
                            <!-- Populated by JavaScript -->
                        </div>
                        <div id="no-slots-message" class="alert alert-warning mt-3" style="display: none;">
                            {{ __('bookings.no_departures_available') }}
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-4 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">{{ __('bookings.adults') }}</label>
                            <input type="number" class="form-control form-control-lg form-control-solid pax-input" id="adults" name="adults" value="1" min="0" max="50" />
                            <div class="form-text">{{ __('bookings.years_12_plus') }}</div>
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">{{ __('bookings.children') }}</label>
                            <input type="number" class="form-control form-control-lg form-control-solid pax-input" id="children_count" name="children_count" value="0" min="0" max="50" />
                            <div class="form-text">{{ __('bookings.years_2_11') }}</div>
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">{{ __('bookings.infants') }}</label>
                            <input type="number" class="form-control form-control-lg form-control-solid pax-input" id="infants" name="infants" value="0" min="0" max="20" />
                            <div class="form-text">{{ __('bookings.years_0_1') }}</div>
                        </div>
                    </div>

                    <div id="capacity-info" class="alert alert-info d-flex align-items-center p-5" style="display: none;">
                        <i class="ki-duotone ki-information-5 fs-2hx text-info me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold">{{ __('bookings.available_seats') }}: <span id="available-seats">-</span></span>
                            <span>{{ __('bookings.selected_passengers') }}: <span id="total-pax">1</span></span>
                        </div>
                    </div>

                    <div id="overbooking-warning" class="alert alert-warning d-flex align-items-center p-5" style="display: none;">
                        <i class="ki-duotone ki-information-2 fs-2hx text-warning me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold">{{ __('bookings.overbooking_request') }}</span>
                            <span>{{ __('bookings.overbooking_warning') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Step 3-->

            <!--begin::Step 4 - Passenger Details-->
            <div data-kt-stepper-element="content">
                <div class="w-100">
                    <div class="pb-10 pb-lg-12">
                        <h2 class="fw-bold text-gray-900">{{ __('bookings.passenger_details') }}</h2>
                        <div class="text-muted fw-semibold fs-6">{{ __('bookings.enter_details_each_passenger') }}</div>
                    </div>

                    <div id="passengers-container">
                        <!-- Passenger forms will be generated by JavaScript -->
                    </div>
                </div>
            </div>
            <!--end::Step 4-->

            <!--begin::Step 5 - Review-->
            <div data-kt-stepper-element="content">
                <div class="w-100">
                    <div class="pb-8 pb-lg-10">
                        <h2 class="fw-bold text-gray-900">{{ __('bookings.review_submit') }}</h2>
                        <div class="text-muted fw-semibold fs-6">{{ __('bookings.review_before_submit') }}</div>
                    </div>

                    <div class="bg-light rounded p-8 mb-10">
                        <div class="row mb-5">
                            <div class="col-6">
                                <span class="text-muted d-block mb-1">{{ __('bookings.partner') }}:</span>
                                <span class="fw-bold" id="review-partner">-</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block mb-1">{{ __('bookings.tour') }}:</span>
                                <span class="fw-bold" id="review-tour">-</span>
                            </div>
                        </div>
                        <div class="separator separator-dashed mb-5"></div>
                        <div class="row mb-5">
                            <div class="col-6">
                                <span class="text-muted d-block mb-1">{{ __('bookings.date_time') }}:</span>
                                <span class="fw-bold" id="review-datetime">-</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block mb-1">{{ __('bookings.passengers') }}:</span>
                                <span class="fw-bold" id="review-pax">-</span>
                            </div>
                        </div>
                        <div class="separator separator-dashed mb-5"></div>
                        <div class="row">
                            <div class="col-6">
                                <span class="text-muted d-block mb-1">{{ __('bookings.status') }}:</span>
                                <span id="review-status">-</span>
                            </div>
                        </div>
                    </div>

                    <div id="review-passengers" class="mb-10">
                        <!-- Passenger review list will be generated by JavaScript -->
                    </div>

                    <div id="review-overbooking-notice" class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4" style="display: none;">
                        <i class="ki-duotone ki-information-2 fs-2tx text-warning me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-800 fw-bold">{{ __('bookings.overbooking_request') }}</div>
                                <div class="fs-7 text-gray-700">{{ __('bookings.overbooking_notice') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Step 5-->

            <!--begin::Actions-->
            <div class="d-flex flex-stack pt-15">
                <div class="mr-2">
                    <button type="button" class="btn btn-lg btn-light-primary me-3" data-kt-stepper-action="previous">
                        <i class="ki-duotone ki-arrow-left fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('general.back') }}
                    </button>
                </div>
                <div>
                    <button type="submit" class="btn btn-lg btn-primary" data-kt-stepper-action="submit">
                        <span class="indicator-label">{{ __('bookings.submit_booking') }}
                            <i class="ki-duotone ki-arrow-right fs-4 ms-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="indicator-progress">{{ __('general.please_wait') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="next">{{ __('general.continue') }}
                        <i class="ki-duotone ki-arrow-right fs-4 ms-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </button>
                </div>
            </div>
            <!--end::Actions-->
        </form>
    </div>
    <!--end::Content-->
</div>
<!--end::Stepper-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration
    const pickupPoints = @json($pickupPoints);
    const departuresUrl = '{{ url("/admin/tours") }}';

    // Translations
    const trans = {
        cutoff: '{{ __('bookings.cutoff') }}',
        seats: '{{ __('bookings.seats_remaining') }}',
        full: '{{ __('bookings.full') }}',
        errorLoadingDepartures: '{{ __('bookings.error_loading_departures') }}',
        pleaseSelectPartner: '{{ __('bookings.please_select_partner') }}',
        pleaseSelectTour: '{{ __('bookings.please_select_tour') }}',
        pleaseSelectDateTime: '{{ __('bookings.please_select_date_time') }}',
        atLeastOneAdult: '{{ __('bookings.at_least_one_adult') }}',
        fillPassengerDetails: '{{ __('bookings.fill_passenger_details') }}',
        selectPickupPoint: '{{ __('bookings.select_pickup_point') }}',
        firstName: '{{ __('bookings.first_name') }}',
        lastName: '{{ __('bookings.last_name') }}',
        pickupPoint: '{{ __('bookings.pickup_point') }}',
        phone: '{{ __('bookings.phone') }}',
        allergies: '{{ __('bookings.allergies') }}',
        notes: '{{ __('bookings.notes') }}',
        adult: '{{ __('bookings.adult') }}',
        child: '{{ __('bookings.child') }}',
        infant: '{{ __('bookings.infant') }}',
        passengers: '{{ __('bookings.passengers') }}',
        pendingApproval: '{{ __('bookings.pending_approval') }}',
        willBeConfirmed: '{{ __('bookings.will_be_confirmed') }}',
    };

    // State
    let selectedPartnerId = null;
    let selectedPartnerName = null;
    let selectedTourId = document.querySelector('.tour-radio:checked')?.value;
    let selectedDepartureId = null;
    let selectedDeparture = null;
    let departures = [];
    let isOverbooking = false;

    // Elements
    const stepperEl = document.querySelector('#kt_create_booking_stepper');
    const form = document.querySelector('#kt_create_booking_form');
    const partnerSelect = document.querySelector('#partner_select');
    const partnerIdInput = document.querySelector('#partner_id');
    const tourDateInput = document.querySelector('#tour_date');
    const timeSlotsContainer = document.querySelector('#time-slots-container');
    const timeSlotsEl = document.querySelector('#time-slots');
    const noSlotsMessage = document.querySelector('#no-slots-message');
    const capacityInfo = document.querySelector('#capacity-info');
    const overbookingWarning = document.querySelector('#overbooking-warning');
    const passengersContainer = document.querySelector('#passengers-container');
    const tourDepartureInput = document.querySelector('#tour_departure_id');

    // Initialize Select2 for partner
    if (partnerSelect) {
        $(partnerSelect).on('change', function() {
            selectedPartnerId = this.value;
            selectedPartnerName = this.options[this.selectedIndex]?.dataset?.name || '';
            partnerIdInput.value = selectedPartnerId;
        });
    }

    // Initialize Flatpickr date picker
    const datePicker = flatpickr(tourDateInput, {
        dateFormat: 'd/m/Y',
        minDate: 'today',
        onChange: function(selectedDates, dateStr) {
            if (selectedDates.length > 0) {
                loadDepartures(selectedDates[0]);
            }
        }
    });

    // Initialize Stepper
    const stepper = new KTStepper(stepperEl);

    stepper.on('kt.stepper.next', function(stepper) {
        const currentStep = stepper.getCurrentStepIndex();

        // Validate current step before proceeding
        if (!validateStep(currentStep)) {
            return;
        }

        // Special handling before step 4 (passenger details)
        if (currentStep === 3) {
            generatePassengerForms();
        }

        // Special handling before step 5 (review)
        if (currentStep === 4) {
            populateReview();
        }

        stepper.goNext();
    });

    stepper.on('kt.stepper.submit', function(stepper) {
        form.submit();
    });

    stepper.on('kt.stepper.previous', function(stepper) {
        stepper.goPrevious();
    });

    // Tour selection change
    document.querySelectorAll('.tour-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            selectedTourId = this.value;
            // Reset date and time selection when tour changes
            datePicker.clear();
            timeSlotsContainer.style.display = 'none';
            timeSlotsEl.innerHTML = '';
            selectedDepartureId = null;
            selectedDeparture = null;
        });
    });

    // Passenger count changes
    document.querySelectorAll('.pax-input').forEach(input => {
        input.addEventListener('change', updateCapacityInfo);
    });

    // Load departures for selected date
    function loadDepartures(date) {
        if (!selectedTourId) return;

        const formattedDate = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

        fetch(`${departuresUrl}/${selectedTourId}/departures?date=${formattedDate}`)
            .then(response => response.json())
            .then(data => {
                departures = data;
                renderTimeSlots(data);
            })
            .catch(error => {
                console.error('Error loading departures:', error);
                timeSlotsEl.innerHTML = `<div class="alert alert-danger">${trans.errorLoadingDepartures}</div>`;
            });
    }

    // Render time slots
    function renderTimeSlots(slots) {
        timeSlotsContainer.style.display = 'block';

        if (slots.length === 0) {
            timeSlotsEl.innerHTML = '';
            noSlotsMessage.style.display = 'block';
            capacityInfo.style.display = 'none';
            return;
        }

        noSlotsMessage.style.display = 'none';

        let html = '';
        slots.forEach((slot, index) => {
            const isDisabled = slot.past_cutoff;
            const statusBadge = slot.past_cutoff
                ? `<span class="badge badge-light-danger">${trans.cutoff}</span>`
                : (slot.remaining > 0
                    ? `<span class="badge badge-light-success">${trans.seats.replace(':count', slot.remaining)}</span>`
                    : `<span class="badge badge-light-warning">${trans.full}</span>`);

            html += `
                <div class="col-md-4">
                    <input type="radio" class="btn-check time-slot-radio" name="time_slot" value="${slot.id}" id="time_${slot.id}" ${isDisabled ? 'disabled' : ''} data-departure='${JSON.stringify(slot)}' />
                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-5 d-flex flex-column align-items-center ${isDisabled ? 'opacity-50' : ''}" for="time_${slot.id}">
                        <span class="fs-3 fw-bold">${slot.time}</span>
                        ${statusBadge}
                    </label>
                </div>
            `;
        });

        timeSlotsEl.innerHTML = html;

        // Add event listeners to time slots
        document.querySelectorAll('.time-slot-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                selectedDepartureId = this.value;
                selectedDeparture = JSON.parse(this.dataset.departure);
                tourDepartureInput.value = selectedDepartureId;
                updateCapacityInfo();
            });
        });
    }

    // Update capacity info display
    function updateCapacityInfo() {
        if (!selectedDeparture) {
            capacityInfo.style.display = 'none';
            overbookingWarning.style.display = 'none';
            return;
        }

        const adults = parseInt(document.querySelector('#adults').value) || 0;
        const children = parseInt(document.querySelector('#children_count').value) || 0;
        const infants = parseInt(document.querySelector('#infants').value) || 0;
        const totalPax = adults + children + infants;
        const seatsNeeded = adults + children; // Infants don't need seats

        document.querySelector('#available-seats').textContent = selectedDeparture.remaining;
        document.querySelector('#total-pax').textContent = totalPax;

        capacityInfo.style.display = 'flex';

        isOverbooking = seatsNeeded > selectedDeparture.remaining;
        overbookingWarning.style.display = isOverbooking ? 'flex' : 'none';
    }

    // Validate current step
    function validateStep(step) {
        switch(step) {
            case 1: // Partner selection
                if (!selectedPartnerId) {
                    alert(trans.pleaseSelectPartner);
                    return false;
                }
                return true;

            case 2: // Tour selection
                if (!selectedTourId) {
                    alert(trans.pleaseSelectTour);
                    return false;
                }
                return true;

            case 3: // Date & Passengers
                if (!selectedDepartureId) {
                    alert(trans.pleaseSelectDateTime);
                    return false;
                }
                const adults = parseInt(document.querySelector('#adults').value) || 0;
                if (adults < 1) {
                    alert(trans.atLeastOneAdult);
                    return false;
                }
                return true;

            case 4: // Passenger details
                // Validate all passenger forms
                let valid = true;
                document.querySelectorAll('.passenger-form').forEach(form => {
                    const firstName = form.querySelector('[name$="[first_name]"]').value.trim();
                    const lastName = form.querySelector('[name$="[last_name]"]').value.trim();
                    const pickupPoint = form.querySelector('[name$="[pickup_point_id]"]').value;

                    if (!firstName || !lastName || !pickupPoint) {
                        valid = false;
                    }
                });

                if (!valid) {
                    alert(trans.fillPassengerDetails);
                }
                return valid;

            default:
                return true;
        }
    }

    // Generate passenger forms
    function generatePassengerForms() {
        const adults = parseInt(document.querySelector('#adults').value) || 0;
        const children = parseInt(document.querySelector('#children_count').value) || 0;
        const infants = parseInt(document.querySelector('#infants').value) || 0;

        let html = '';
        let passengerIndex = 0;

        // Generate adult forms
        for (let i = 0; i < adults; i++) {
            html += generatePassengerForm(passengerIndex++, 'adult', `${trans.adult} ${i + 1}`);
        }

        // Generate child forms
        for (let i = 0; i < children; i++) {
            html += generatePassengerForm(passengerIndex++, 'child', `${trans.child} ${i + 1}`);
        }

        // Generate infant forms
        for (let i = 0; i < infants; i++) {
            html += generatePassengerForm(passengerIndex++, 'infant', `${trans.infant} ${i + 1}`);
        }

        passengersContainer.innerHTML = html;
    }

    // Generate single passenger form
    function generatePassengerForm(index, paxType, label) {
        const paxColor = paxType === 'adult' ? 'primary' : (paxType === 'child' ? 'info' : 'secondary');

        let pickupOptions = `<option value="">${trans.selectPickupPoint}</option>`;
        pickupPoints.forEach(point => {
            pickupOptions += `<option value="${point.id}">${point.name}${point.default_time ? ' (' + point.default_time + ')' : ''}</option>`;
        });

        return `
            <div class="passenger-form mb-10 p-8 border border-dashed border-gray-300 rounded">
                <h4 class="fw-bold mb-5">
                    <span class="badge badge-${paxColor} me-2">${index + 1}</span>
                    ${label}
                </h4>
                <input type="hidden" name="passengers[${index}][pax_type]" value="${paxType}">

                <div class="row g-9 mb-5">
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">${trans.firstName}</label>
                        <input type="text" class="form-control form-control-solid" name="passengers[${index}][first_name]" placeholder="${trans.firstName}" required />
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">${trans.lastName}</label>
                        <input type="text" class="form-control form-control-solid" name="passengers[${index}][last_name]" placeholder="${trans.lastName}" required />
                    </div>
                </div>

                <div class="row g-9 mb-5">
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">${trans.pickupPoint}</label>
                        <select class="form-select form-select-solid" name="passengers[${index}][pickup_point_id]" required>
                            ${pickupOptions}
                        </select>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">${trans.phone}</label>
                        <input type="tel" class="form-control form-control-solid" name="passengers[${index}][phone]" placeholder="+39 xxx xxx xxxx" />
                    </div>
                </div>

                <div class="row g-9">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">${trans.allergies}</label>
                        <input type="text" class="form-control form-control-solid" name="passengers[${index}][allergies]" placeholder="${trans.allergies}..." />
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">${trans.notes}</label>
                        <input type="text" class="form-control form-control-solid" name="passengers[${index}][notes]" placeholder="${trans.notes}..." />
                    </div>
                </div>
            </div>
        `;
    }

    // Populate review section
    function populateReview() {
        const tourRadio = document.querySelector('.tour-radio:checked');
        const tourName = tourRadio ? tourRadio.dataset.tourName : '-';

        const dateStr = tourDateInput.value;
        const timeStr = selectedDeparture ? selectedDeparture.time : '-';

        const adults = parseInt(document.querySelector('#adults').value) || 0;
        const children = parseInt(document.querySelector('#children_count').value) || 0;
        const infants = parseInt(document.querySelector('#infants').value) || 0;

        let paxSummary = [];
        if (adults > 0) paxSummary.push(`${adults} ${trans.adult}${adults > 1 ? 's' : ''}`);
        if (children > 0) paxSummary.push(`${children} ${trans.child}${children > 1 ? 'ren' : ''}`);
        if (infants > 0) paxSummary.push(`${infants} ${trans.infant}${infants > 1 ? 's' : ''}`);

        document.querySelector('#review-partner').textContent = selectedPartnerName || '-';
        document.querySelector('#review-tour').textContent = tourName;
        document.querySelector('#review-datetime').textContent = `${dateStr} - ${timeStr}`;
        document.querySelector('#review-pax').textContent = paxSummary.join(', ');

        // Status
        const statusEl = document.querySelector('#review-status');
        if (isOverbooking) {
            statusEl.innerHTML = `<span class="badge badge-light-warning">${trans.pendingApproval}</span>`;
            document.querySelector('#review-overbooking-notice').style.display = 'flex';
        } else {
            statusEl.innerHTML = `<span class="badge badge-light-success">${trans.willBeConfirmed}</span>`;
            document.querySelector('#review-overbooking-notice').style.display = 'none';
        }

        // Passenger list
        let passengersHtml = `<h5 class="fw-bold mb-4">${trans.passengers}</h5><div class="table-responsive"><table class="table table-row-bordered gy-4"><tbody>`;
        document.querySelectorAll('.passenger-form').forEach((form, index) => {
            const firstName = form.querySelector('[name$="[first_name]"]').value;
            const lastName = form.querySelector('[name$="[last_name]"]').value;
            const paxType = form.querySelector('[name$="[pax_type]"]').value;
            const pickupSelect = form.querySelector('[name$="[pickup_point_id]"]');
            const pickupName = pickupSelect.options[pickupSelect.selectedIndex]?.text || '-';

            const paxColor = paxType === 'adult' ? 'primary' : (paxType === 'child' ? 'info' : 'secondary');
            const paxLabel = paxType === 'adult' ? trans.adult : (paxType === 'child' ? trans.child : trans.infant);

            passengersHtml += `
                <tr>
                    <td class="fw-bold">${index + 1}. ${firstName} ${lastName}</td>
                    <td><span class="badge badge-light-${paxColor}">${paxLabel}</span></td>
                    <td class="text-muted">${pickupName}</td>
                </tr>
            `;
        });
        passengersHtml += '</tbody></table></div>';
        document.querySelector('#review-passengers').innerHTML = passengersHtml;
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('[data-kt-stepper-action="submit"]');
        submitBtn.setAttribute('data-kt-indicator', 'on');
        submitBtn.disabled = true;
    });
});
</script>
@endpush
