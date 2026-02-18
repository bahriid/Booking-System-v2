@extends('layouts.admin')

@section('title', __('users.add_user'))
@section('page-title', __('users.add_user'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('admin.users.index') }}" class="text-muted text-hover-primary">{{ __('users.users') }}</a>
</li>
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('general.create') }}</li>
@endsection

@section('content')
<div class="card">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="card-header border-0 pt-6">
            <div class="card-title">{{ __('users.create_new_user') }}</div>
        </div>
        <div class="card-body py-4">
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-5">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div>
                        <h4 class="mb-1 text-danger">{{ __('users.validation_error') }}</h4>
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
                    <label class="form-label required fw-semibold fs-6">{{ __('users.name') }}</label>
                    <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="{{ __('users.enter_name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label required fw-semibold fs-6">{{ __('users.email') }}</label>
                    <input type="email" name="email" class="form-control form-control-solid @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="{{ __('users.enter_email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label required fw-semibold fs-6">{{ __('users.password') }}</label>
                    <input type="password" name="password" class="form-control form-control-solid @error('password') is-invalid @enderror"
                           placeholder="{{ __('users.enter_password') }}" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">{{ __('users.password_requirements') }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label required fw-semibold fs-6">{{ __('users.role') }}</label>
                    <select name="role" class="form-select form-select-solid @error('role') is-invalid @enderror" required>
                        <option value="">{{ __('users.select_role') }}</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" {{ old('role') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('users.active') }}</label>
                    </div>
                    <div class="form-text">{{ __('users.user_active_description') }}</div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end py-6">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light me-3">{{ __('users.cancel') }}</a>
            <button type="submit" class="btn btn-primary">
                <i class="ki-duotone ki-check fs-4 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ __('users.create_user') }}
            </button>
        </div>
    </form>
</div>
@endsection
