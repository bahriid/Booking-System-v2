@extends('layouts.admin')

@section('title', __('pickup_points.add_pickup_point'))
@section('page-title', __('pickup_points.add_pickup_point'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('admin.pickup-points.index') }}" class="text-muted text-hover-primary">{{ __('pickup_points.pickup_points') }}</a>
</li>
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('pickup_points.create') }}</li>
@endsection

@section('content')
<div class="card">
    <form action="{{ route('admin.pickup-points.store') }}" method="POST">
        @csrf
        <div class="card-header border-0 pt-6">
            <div class="card-title">{{ __('pickup_points.create_new_pickup_point') }}</div>
        </div>
        <div class="card-body py-4">
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-5">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div>
                        <h4 class="mb-1 text-danger">{{ __('pickup_points.validation_error') }}</h4>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="row g-5">
                <div class="col-md-6">
                    <label class="form-label required fw-semibold fs-6">{{ __('pickup_points.name') }}</label>
                    <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="{{ __('pickup_points.pickup_point_name_placeholder') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold fs-6">{{ __('pickup_points.location') }}</label>
                    <input type="text" name="location" class="form-control form-control-solid @error('location') is-invalid @enderror"
                           value="{{ old('location') }}" placeholder="{{ __('pickup_points.location_placeholder') }}">
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold fs-6">{{ __('pickup_points.default_time') }}</label>
                    <input type="time" name="default_time" class="form-control form-control-solid @error('default_time') is-invalid @enderror"
                           value="{{ old('default_time') }}">
                    @error('default_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">{{ __('pickup_points.default_pickup_time_description') }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold fs-6">{{ __('pickup_points.sort_order') }}</label>
                    <input type="number" name="sort_order" class="form-control form-control-solid @error('sort_order') is-invalid @enderror"
                           value="{{ old('sort_order') }}" placeholder="{{ __('pickup_points.auto_assign') }}" min="0">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">{{ __('pickup_points.sort_order_description') }}</div>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('pickup_points.active') }}</label>
                    </div>
                    <div class="form-text">{{ __('pickup_points.pickup_point_active_description') }}</div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end py-6">
            <a href="{{ route('admin.pickup-points.index') }}" class="btn btn-light me-3">{{ __('pickup_points.cancel') }}</a>
            <button type="submit" class="btn btn-primary">
                <i class="ki-duotone ki-check fs-4 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ __('pickup_points.create_pickup_point') }}
            </button>
        </div>
    </form>
</div>
@endsection
