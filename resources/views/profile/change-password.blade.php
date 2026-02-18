@php
    $layout = match(auth()->user()->role->value) {
        'admin' => 'layouts.admin',
        'partner' => 'layouts.partner',
        'driver' => 'layouts.driver',
        default => 'layouts.admin',
    };
    $dashboardRoute = match(auth()->user()->role->value) {
        'admin' => 'admin.dashboard',
        'partner' => 'partner.dashboard',
        'driver' => 'driver.dashboard',
        default => 'admin.dashboard',
    };
@endphp

@extends($layout)

@section('title', __('profile.change_password'))
@section('page-title', __('profile.change_password'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('profile.change_password') }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('profile.change_password') }}</h3>
    </div>
    <div class="card-body">
        @if (session('success'))
            <x-ui.alert type="success" :message="session('success')" dismissible class="mb-5" />
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

        <form method="POST" action="{{ route('profile.password.update') }}" class="mw-500px">
            @csrf
            @method('PUT')

            <div class="mb-10">
                <label class="form-label required">{{ __('profile.current_password') }}</label>
                <input type="password" name="current_password" class="form-control form-control-solid @error('current_password') is-invalid @enderror" autocomplete="current-password" />
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-10">
                <label class="form-label required">{{ __('profile.new_password') }}</label>
                <input type="password" name="password" class="form-control form-control-solid @error('password') is-invalid @enderror" autocomplete="new-password" />
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">{{ __('profile.password_requirements') }}</div>
            </div>

            <div class="mb-10">
                <label class="form-label required">{{ __('profile.confirm_new_password') }}</label>
                <input type="password" name="password_confirmation" class="form-control form-control-solid" autocomplete="new-password" />
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary">
                    <i class="ki-duotone ki-lock-3 fs-4 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    {{ __('profile.update_password') }}
                </button>
                <a href="{{ route($dashboardRoute) }}" class="btn btn-light">{{ __('general.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
