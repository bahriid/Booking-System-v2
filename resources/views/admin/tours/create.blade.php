@extends('layouts.admin')

@section('title', isset($tour) ? __('tours.edit_tour') : __('tours.new_tour'))
@section('page-title', isset($tour) ? __('tours.edit_tour') : __('tours.new_tour'))

@section('breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('admin.tours.index') }}" class="text-muted text-hover-primary">{{ __('tours.breadcrumb_tours') }}</a>
</li>
<li class="breadcrumb-item">
    <span class="bullet bg-gray-500 w-5px h-2px"></span>
</li>
<li class="breadcrumb-item text-muted">{{ isset($tour) ? __('tours.breadcrumb_edit') : __('tours.breadcrumb_new') }}</li>
@endsection

@section('content')
{{-- Validation Errors --}}
@if($errors->any())
<x-ui.alert type="danger" dismissible>
    <strong>{{ __('tours.validation_errors') }}</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</x-ui.alert>
@endif

<form action="{{ isset($tour) ? route('admin.tours.update', $tour) : route('admin.tours.store') }}" method="POST">
    @csrf
    @if(isset($tour))
    @method('PUT')
    @endif

    <div class="row g-5 g-xl-10">
        <!--begin::Main Column-->
        <div class="col-xl-8">
            <!--begin::Tour Info Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <span class="me-2">
                            <i class="ki-duotone ki-information-3 fs-2 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                        {{ __('tours.tour_information') }}
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <x-forms.input
                                name="code"
                                :label="__('tours.tour_code')"
                                :placeholder="__('tours.tour_code_placeholder')"
                                :value="old('code', $tour->code ?? '')"
                                :hint="__('tours.tour_code_hint')"
                                required
                            />
                        </div>
                        <div class="col-md-8">
                            <x-forms.input
                                name="name"
                                :label="__('tours.tour_name')"
                                :placeholder="__('tours.tour_name_placeholder')"
                                :value="old('name', $tour->name ?? '')"
                                required
                            />
                        </div>
                        <div class="col-12">
                            <x-forms.textarea
                                name="description"
                                :label="__('tours.description')"
                                :placeholder="__('tours.description_placeholder')"
                                :value="old('description', $tour->description ?? '')"
                                :rows="3"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Tour Info Card-->

            <!--begin::Capacity & Rules Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <span class="me-2">
                            <i class="ki-duotone ki-setting-2 fs-2 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        {{ __('tours.capacity_rules') }}
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold fs-6">{{ __('tours.standard_capacity') }}</label>
                            <div class="input-group input-group-solid">
                                <input type="number" class="form-control form-control-solid @error('default_capacity') is-invalid @enderror" name="default_capacity" value="{{ old('default_capacity', $tour->default_capacity ?? 50) }}" min="1" max="500" required>
                                <span class="input-group-text">{{ __('tours.pax') }}</span>
                            </div>
                            @error('default_capacity')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold fs-6">{{ __('tours.booking_cutoff') }}</label>
                            <div class="input-group input-group-solid">
                                <input type="number" class="form-control form-control-solid @error('cutoff_hours') is-invalid @enderror" name="cutoff_hours" value="{{ old('cutoff_hours', $tour->cutoff_hours ?? 24) }}" min="1" max="168" required>
                                <span class="input-group-text">{{ __('tours.hours_before') }}</span>
                            </div>
                            @error('cutoff_hours')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Capacity & Rules Card-->

            <!--begin::Seasonality Card-->
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
                        {{ __('tours.seasonality') }}
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <x-forms.date-picker
                                name="seasonality_start"
                                :label="__('tours.season_start_date')"
                                :value="old('seasonality_start', isset($tour) ? $tour->seasonality_start->format('d/m/Y') : '')"
                                placeholder="dd/mm/yyyy"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <x-forms.date-picker
                                name="seasonality_end"
                                :label="__('tours.season_end_date')"
                                :value="old('seasonality_end', isset($tour) ? $tour->seasonality_end->format('d/m/Y') : '')"
                                placeholder="dd/mm/yyyy"
                                required
                            />
                        </div>
                    </div>
                    <div class="form-text mt-3">{{ __('tours.seasonality_hint') }}</div>
                </div>
            </div>
            <!--end::Seasonality Card-->
        </div>
        <!--end::Main Column-->

        <!--begin::Sidebar-->
        <div class="col-xl-4">
            <!--begin::Status Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">{{ __('tours.status') }}</div>
                </div>
                <div class="card-body py-4">
                    <x-forms.toggle
                        name="is_active"
                        :label="__('tours.tour_active')"
                        :checked="old('is_active', $tour->is_active ?? true)"
                        :hint="__('tours.tour_active_hint')"
                    />
                </div>
            </div>
            <!--end::Status Card-->

            <!--begin::Pickup Points Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">{{ __('tours.pickup_points') }}</div>
                </div>
                <div class="card-body py-4">
                    @forelse($pickupPoints ?? [] as $pickup)
                    <div class="form-check form-check-custom form-check-solid mb-3">
                        <input class="form-check-input" type="checkbox" id="pickup{{ $pickup->id }}" name="pickups[]" value="{{ $pickup->id }}" checked>
                        <label class="form-check-label fw-semibold" for="pickup{{ $pickup->id }}">
                            {{ $pickup->name }}
                            @if($pickup->default_time)
                            <span class="text-muted">({{ $pickup->default_time?->format('H:i') }})</span>
                            @endif
                        </label>
                    </div>
                    @empty
                    <div class="text-muted">{{ __('tours.no_pickup_points') }}</div>
                    @endforelse
                    <div class="separator my-5"></div>
                    <a href="{{ route('admin.settings') }}#pickups" class="btn btn-sm btn-light-primary w-100">
                        <i class="ki-duotone ki-setting-2 fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('tours.manage_pickup_points') }}
                    </a>
                </div>
            </div>
            <!--end::Pickup Points Card-->

            <!--begin::Actions Card-->
            <div class="card">
                <div class="card-body py-4">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-duotone ki-check fs-3 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ isset($tour) ? __('tours.update_tour') : __('tours.create_tour_button') }}
                        </button>
                        <a href="{{ route('admin.tours.index') }}" class="btn btn-light">
                            <i class="ki-duotone ki-cross fs-3 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('tours.cancel') }}
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

@push('styles')
<style>
    .required:after {
        content: " *";
        color: var(--bs-danger);
    }
</style>
@endpush

@push('scripts')
<script>
    // Convert tour code to uppercase as user types
    document.querySelector('input[name="code"]')?.addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    // Initialize Flatpickr for date picker components
    document.querySelectorAll('[data-datepicker="true"]').forEach(function(el) {
        var options = JSON.parse(el.getAttribute('data-datepicker-options') || '{}');
        flatpickr(el, options);
    });
</script>
@endpush
