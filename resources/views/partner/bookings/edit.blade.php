@extends('layouts.partner')

@section('title', __('partner.edit_booking') . ' ' . $booking->booking_code)
@section('page-title', __('partner.edit_booking'))

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
<li class="breadcrumb-item">
    <a href="{{ route('partner.bookings.show', $booking) }}" class="text-muted text-hover-primary">{{ $booking->booking_code }}</a>
</li>
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('partner.edit') }}</li>
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

@php
    $departure = $booking->tourDeparture;
    $tour = $departure->tour;
    $remainingSeats = $departure->remaining_seats;
    $maxCapacity = $tour->default_capacity;
    $currentPassengers = $booking->passengers->count();
    $canAddMore = $remainingSeats > 0;
@endphp

<form action="{{ route('partner.bookings.update', $booking) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-5 g-xl-10">
        <!--begin::Main Column-->
        <div class="col-xl-8">
            <!--begin::Booking Info Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <span class="me-2">
                            <i class="ki-duotone ki-document fs-2 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        {{ __('partner.booking_information') }}
                    </div>
                    <div class="card-toolbar">
                        <span class="badge badge-light-success fs-7 fw-bold">{{ __('partner.editable') }}</span>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                        <i class="ki-duotone ki-information-2 fs-2tx text-info me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-800 fw-bold">{{ __('partner.modify_passenger_details') }}</div>
                                <div class="fs-7 text-gray-700">{{ __('partner.cannot_change_tour_date') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted fw-semibold w-150px">{{ __('partner.booking_code') }}</td>
                                    <td class="fw-bold fs-5">{{ $booking->booking_code }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">{{ __('partner.tour') }}</td>
                                    <td>{{ $tour->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted fw-semibold w-150px">{{ __('partner.date') }}</td>
                                    <td>{{ $departure->date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">{{ __('partner.time') }}</td>
                                    <td>{{ $departure->time }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="separator my-4"></div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold fs-6">{{ __('partner.booking_notes') }}</label>
                        <textarea class="form-control form-control-solid @error('notes') is-invalid @enderror" name="notes" rows="2" placeholder="{{ __('partner.booking_notes_placeholder') }}">{{ old('notes', $booking->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <!--end::Booking Info Card-->

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
                        <span class="badge badge-light-primary ms-2" id="passengerCount">{{ $booking->passengers->count() }} {{ __('partner.total') }}</span>
                    </div>
                    <div class="card-toolbar">
                        <span class="text-muted fs-7">{{ __('partner.available_seats') }}: <strong id="remainingSeatsDisplay">{{ $remainingSeats }}</strong></span>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div id="passengers-container">
                        @foreach ($booking->passengers as $index => $passenger)
                        <div class="passenger-card mb-5 {{ $loop->last ? '' : 'border-bottom pb-5' }}" data-passenger-index="{{ $index }}" data-passenger-id="{{ $passenger->id }}" data-is-existing="true">
                            <input type="hidden" name="passengers[{{ $index }}][id]" value="{{ $passenger->id }}">

                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div>
                                    <span class="badge badge-light-{{ $passenger->pax_type->color() }} me-3">{{ $passenger->pax_type->label() }}</span>
                                    <span class="fw-bold fs-6">{{ __('partner.passenger', ['number' => $index + 1]) }}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-existing-passenger-btn" data-passenger-id="{{ $passenger->id }}" data-passenger-name="{{ $passenger->first_name }} {{ $passenger->last_name }}" title="{{ __('partner.remove_passenger') }}" {!! $booking->passengers->count() <= 1 ? 'style="display:none"' : '' !!}>
                                    <i class="ki-duotone ki-cross fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>
                            </div>

                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label required fw-semibold fs-6">{{ __('partner.first_name') }}</label>
                                    <input type="text" name="passengers[{{ $index }}][first_name]"
                                        class="form-control form-control-solid @error('passengers.'.$index.'.first_name') is-invalid @enderror"
                                        value="{{ old('passengers.'.$index.'.first_name', $passenger->first_name) }}" required>
                                    @error('passengers.'.$index.'.first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-semibold fs-6">{{ __('partner.last_name') }}</label>
                                    <input type="text" name="passengers[{{ $index }}][last_name]"
                                        class="form-control form-control-solid @error('passengers.'.$index.'.last_name') is-invalid @enderror"
                                        value="{{ old('passengers.'.$index.'.last_name', $passenger->last_name) }}" required>
                                    @error('passengers.'.$index.'.last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6">{{ __('partner.phone') }}</label>
                                    <input type="tel" name="passengers[{{ $index }}][phone]"
                                        class="form-control form-control-solid @error('passengers.'.$index.'.phone') is-invalid @enderror"
                                        value="{{ old('passengers.'.$index.'.phone', $passenger->phone) }}" placeholder="+39...">
                                    @error('passengers.'.$index.'.phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6">{{ __('partner.pickup_point') }}</label>
                                    <select name="passengers[{{ $index }}][pickup_point_id]" class="form-select form-select-solid @error('passengers.'.$index.'.pickup_point_id') is-invalid @enderror">
                                        <option value="">{{ __('partner.select_pickup') }}</option>
                                        @foreach ($pickupPoints as $pickupPoint)
                                            <option value="{{ $pickupPoint->id }}" {{ old('passengers.'.$index.'.pickup_point_id', $passenger->pickup_point_id) == $pickupPoint->id ? 'selected' : '' }}>
                                                {{ $pickupPoint->name }} ({{ $pickupPoint->default_time }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('passengers.'.$index.'.pickup_point_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6">{{ __('partner.allergies') }}</label>
                                    <input type="text" name="passengers[{{ $index }}][allergies]"
                                        class="form-control form-control-solid @error('passengers.'.$index.'.allergies') is-invalid @enderror"
                                        value="{{ old('passengers.'.$index.'.allergies', $passenger->allergies) }}" placeholder="{{ __('partner.any_food_allergies') }}">
                                    @error('passengers.'.$index.'.allergies')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6">{{ __('partner.notes') }}</label>
                                    <input type="text" name="passengers[{{ $index }}][notes]"
                                        class="form-control form-control-solid @error('passengers.'.$index.'.notes') is-invalid @enderror"
                                        value="{{ old('passengers.'.$index.'.notes', $passenger->notes) }}" placeholder="{{ __('partner.special_requirements') }}">
                                    @error('passengers.'.$index.'.notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!--begin::Removed Passengers Hidden Inputs-->
                    <div id="removed-passengers-container"></div>
                    <!--end::Removed Passengers Hidden Inputs-->

                    <!--begin::Add Passenger Button-->
                    <div id="add-passenger-section" class="mt-5">
                        <button type="button" id="addPassengerBtn" class="btn btn-light-primary w-100" {{ !$canAddMore ? 'disabled' : '' }}>
                            <i class="ki-duotone ki-plus fs-4 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            {{ __('partner.add_passenger') }}
                        </button>
                        @if (!$canAddMore)
                        <div class="text-muted fs-7 text-center mt-2">{{ __('partner.no_seats_available') }}</div>
                        @endif
                    </div>
                    <!--end::Add Passenger Button-->
                </div>
            </div>
            <!--end::Passengers Card-->
        </div>
        <!--end::Main Column-->

        <!--begin::Sidebar-->
        <div class="col-xl-4">
            <!--begin::Summary Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">{{ __('partner.summary') }}</div>
                </div>
                <div class="card-body py-4">
                    <div class="d-flex flex-stack mb-3">
                        <span class="text-muted fw-semibold">{{ __('partner.tour') }}</span>
                        <span class="fw-bold">{{ $tour->code }}</span>
                    </div>
                    <div class="d-flex flex-stack mb-3">
                        <span class="text-muted fw-semibold">{{ __('partner.date') }}</span>
                        <span class="fw-bold">{{ $departure->date->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex flex-stack mb-3">
                        <span class="text-muted fw-semibold">{{ __('partner.time') }}</span>
                        <span class="fw-bold">{{ $departure->time }}</span>
                    </div>
                    <div class="separator my-4"></div>
                    <div class="d-flex flex-stack mb-3">
                        <span class="text-muted fw-semibold">{{ __('partner.capacity') }}</span>
                        <span class="fw-bold">{{ $departure->capacity }} pax</span>
                    </div>
                    <div class="d-flex flex-stack mb-3">
                        <span class="text-muted fw-semibold">{{ __('partner.available_seats') }}</span>
                        <span class="fw-bold" id="sidebarRemainingSeats">{{ $remainingSeats }}</span>
                    </div>
                    <div class="separator my-4"></div>
                    @php
                        $paxCounts = $booking->passengers->groupBy('pax_type')->map->count();
                    @endphp
                    @foreach ($paxCounts as $type => $count)
                    <div class="d-flex flex-stack mb-3">
                        <span class="text-muted fw-semibold">{{ ucfirst($type) }}s</span>
                        <span class="fw-bold">{{ $count }}</span>
                    </div>
                    @endforeach
                    <div class="separator my-4"></div>
                    <div class="d-flex flex-stack">
                        <span class="fw-bold">{{ __('partner.total_amount') }}</span>
                        <span class="fw-bold fs-5 text-primary">${{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            <!--end::Summary Card-->

            <!--begin::Actions Card-->
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">{{ __('partner.actions') }}</div>
                </div>
                <div class="card-body py-4">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-duotone ki-check fs-4 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('partner.save_changes') }}
                        </button>
                        <a href="{{ route('partner.bookings.show', $booking) }}" class="btn btn-light">
                            <i class="ki-duotone ki-arrow-left fs-4 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('partner.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
            <!--end::Actions Card-->
        </div>
        <!--end::Sidebar-->
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pickupPoints = @json($pickupPoints);
    const maxCapacity = {{ $departure->capacity }};
    const currentBooked = {{ $departure->booked_seats }};
    let passengerIndex = {{ $currentPassengers }};
    let newPassengersAdded = 0;
    let removedExistingCount = 0;
    const remainingSeats = {{ $remainingSeats }};
    const initialPassengerCount = {{ $currentPassengers }};

    const container = document.getElementById('passengers-container');
    const addBtn = document.getElementById('addPassengerBtn');
    const removedContainer = document.getElementById('removed-passengers-container');

    addBtn.addEventListener('click', function() {
        if (newPassengersAdded >= (remainingSeats + removedExistingCount)) return;

        const html = generateNewPassengerForm(passengerIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        container.appendChild(wrapper.firstElementChild);

        passengerIndex++;
        newPassengersAdded++;

        updatePassengerCount();
        updateAddButtonState();
    });

    // Handle remove buttons for existing passengers
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-existing-passenger-btn');
        if (!btn) return;

        const passengerId = btn.dataset.passengerId;
        const passengerName = btn.dataset.passengerName;
        const visibleCards = container.querySelectorAll('.passenger-card:not([style*="display: none"])');

        if (visibleCards.length <= 1) {
            alert('{{ __('partner.cannot_remove_last_passenger') }}');
            return;
        }

        if (!confirm('{{ __('partner.confirm_remove_passenger') }}: ' + passengerName + '?')) {
            return;
        }

        const card = btn.closest('.passenger-card');
        card.style.display = 'none';

        // Disable all inputs inside so they won't be submitted
        card.querySelectorAll('input, select, textarea').forEach(input => {
            input.disabled = true;
        });

        // Add hidden input for removal
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'removed_passengers[]';
        hiddenInput.value = passengerId;
        removedContainer.appendChild(hiddenInput);

        removedExistingCount++;
        updatePassengerCount();
        updateAddButtonState();
        updateRemoveButtonVisibility();
    });

    function updateAddButtonState() {
        const effectiveRemaining = remainingSeats + removedExistingCount - newPassengersAdded;
        addBtn.disabled = effectiveRemaining <= 0;
    }

    function updateRemoveButtonVisibility() {
        const visibleCards = container.querySelectorAll('.passenger-card:not([style*="display: none"])');
        visibleCards.forEach(card => {
            const removeBtn = card.querySelector('.remove-existing-passenger-btn');
            if (removeBtn) {
                removeBtn.style.display = visibleCards.length <= 1 ? 'none' : '';
            }
        });
    }

    function updatePassengerCount() {
        const total = container.querySelectorAll('.passenger-card:not([style*="display: none"])').length;
        document.getElementById('passengerCount').textContent = total + ' {{ __('partner.total') }}';
        const effectiveRemaining = remainingSeats + removedExistingCount - newPassengersAdded;
        document.getElementById('remainingSeatsDisplay').textContent = effectiveRemaining;
        document.getElementById('sidebarRemainingSeats').textContent = effectiveRemaining;
    }

    function removeNewPassenger(index) {
        const card = document.querySelector(`.passenger-card[data-passenger-index="${index}"]`);
        if (card) {
            card.remove();
            newPassengersAdded--;
            updatePassengerCount();
            updateAddButtonState();
            updateRemoveButtonVisibility();
        }
    }

    // Expose to onclick handlers
    window.removeNewPassenger = removeNewPassenger;

    function generateNewPassengerForm(index) {
        let pickupOptions = '<option value="">{{ __('partner.select_pickup') }}</option>';
        pickupPoints.forEach(point => {
            pickupOptions += `<option value="${point.id}">${point.name}${point.default_time ? ' (' + point.default_time + ')' : ''}</option>`;
        });

        return `
            <div class="passenger-card mb-5 border-top pt-5" data-passenger-index="${index}">
                <input type="hidden" name="new_passengers[${index}][pax_type]" value="adult">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <span class="badge badge-light-success me-3">{{ __('partner.new') }}</span>
                        <span class="fw-bold fs-6">{{ __('partner.passenger', ['number' => '']) }}${index + 1}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-icon btn-light-danger" onclick="removeNewPassenger(${index})">
                        <i class="ki-duotone ki-cross fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </button>
                </div>
                <div class="row g-5">
                    <div class="col-md-4">
                        <label class="form-label required fw-semibold fs-6">{{ __('partner.type') }}</label>
                        <select name="new_passengers[${index}][pax_type]" class="form-select form-select-solid">
                            <option value="adult">{{ __('partner.adult') }}</option>
                            <option value="child">{{ __('partner.child') }}</option>
                            <option value="infant">{{ __('partner.infant') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required fw-semibold fs-6">{{ __('partner.first_name') }}</label>
                        <input type="text" class="form-control form-control-solid" name="new_passengers[${index}][first_name]" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required fw-semibold fs-6">{{ __('partner.last_name') }}</label>
                        <input type="text" class="form-control form-control-solid" name="new_passengers[${index}][last_name]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required fw-semibold fs-6">{{ __('partner.pickup_point') }}</label>
                        <select class="form-select form-select-solid" name="new_passengers[${index}][pickup_point_id]" required>
                            ${pickupOptions}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-6">{{ __('partner.phone') }}</label>
                        <input type="tel" class="form-control form-control-solid" name="new_passengers[${index}][phone]" placeholder="+39...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-6">{{ __('partner.allergies') }}</label>
                        <input type="text" class="form-control form-control-solid" name="new_passengers[${index}][allergies]">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold fs-6">{{ __('partner.notes') }}</label>
                        <input type="text" class="form-control form-control-solid" name="new_passengers[${index}][notes]">
                    </div>
                </div>
            </div>
        `;
    }
});
</script>
@endpush
