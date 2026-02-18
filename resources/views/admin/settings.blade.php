@extends('layouts.admin')

@section('title', __('settings.title'))
@section('page-title', __('settings.title'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <span class="bullet bg-gray-500 w-5px h-2px"></span>
</li>
<li class="breadcrumb-item text-muted">{{ __('settings.breadcrumb') }}</li>
@endsection

@section('content')
<div class="row g-5 g-xl-10">
    <!--begin::Sidebar-->
    <div class="col-xl-3">
        <div class="card">
            <div class="card-body py-4">
                <ul class="nav nav-pills flex-column border-0" id="settings_tabs" role="tablist">
                    <li class="nav-item mb-2" role="presentation">
                        <a class="nav-link d-flex align-items-center px-4 py-3 rounded active" data-bs-toggle="pill" href="#tab_general" role="tab">
                            <i class="ki-duotone ki-setting-2 fs-3 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fw-semibold">{{ __('settings.tabs.general') }}</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <a class="nav-link d-flex align-items-center px-4 py-3 rounded" data-bs-toggle="pill" href="#tab_booking" role="tab">
                            <i class="ki-duotone ki-document fs-3 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fw-semibold">{{ __('settings.tabs.booking_rules') }}</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <a class="nav-link d-flex align-items-center px-4 py-3 rounded" data-bs-toggle="pill" href="#tab_email" role="tab">
                            <i class="ki-duotone ki-sms fs-3 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fw-semibold">{{ __('settings.tabs.email_notifications') }}</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <a class="nav-link d-flex align-items-center px-4 py-3 rounded" data-bs-toggle="pill" href="#tab_language" role="tab">
                            <i class="ki-duotone ki-flag fs-3 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fw-semibold">{{ __('settings.tabs.language') }}</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <a class="nav-link d-flex align-items-center px-4 py-3 rounded" data-bs-toggle="pill" href="#tab_pickups" role="tab">
                            <i class="ki-duotone ki-geolocation fs-3 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <span class="fw-semibold">{{ __('settings.tabs.pickup_points') }}</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <a class="nav-link d-flex align-items-center px-4 py-3 rounded" data-bs-toggle="pill" href="#tab_users" role="tab">
                            <i class="ki-duotone ki-people fs-3 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            <span class="fw-semibold">{{ __('settings.tabs.users_admins') }}</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <a class="nav-link d-flex align-items-center px-4 py-3 rounded" data-bs-toggle="pill" href="#tab_voucher" role="tab">
                            <i class="ki-duotone ki-file fs-3 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fw-semibold">{{ __('settings.tabs.voucher') }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link d-flex align-items-center px-4 py-3 rounded" data-bs-toggle="pill" href="#tab_backup" role="tab">
                            <i class="ki-duotone ki-cloud-download fs-3 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fw-semibold">{{ __('settings.tabs.backup_logs') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Sidebar-->

    <!--begin::Content-->
    <div class="col-xl-9">
        <div class="tab-content">
            <!--begin::Tab General-->
            <div class="tab-pane fade show active" id="tab_general">
                <form action="{{ route('admin.settings.general') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">{{ __('settings.general.title') }}</div>
                        </div>
                        <div class="card-body py-4">
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.general.company_name') }}</label>
                                    <input type="text" name="company_name" class="form-control form-control-solid @error('company_name') is-invalid @enderror"
                                        value="{{ old('company_name', $generalSettings['company_name'] ?? '') }}" placeholder="{{ __('settings.general.company_name_placeholder') }}">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.general.contact_email') }}</label>
                                    <input type="email" name="company_email" class="form-control form-control-solid @error('company_email') is-invalid @enderror"
                                        value="{{ old('company_email', $generalSettings['company_email'] ?? '') }}" placeholder="{{ __('settings.general.contact_email_placeholder') }}">
                                    @error('company_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6">{{ __('settings.general.contact_phone') }}</label>
                                    <input type="tel" name="company_phone" class="form-control form-control-solid @error('company_phone') is-invalid @enderror"
                                        value="{{ old('company_phone', $generalSettings['company_phone'] ?? '') }}" placeholder="{{ __('settings.general.contact_phone_placeholder') }}">
                                    @error('company_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.general.timezone') }}</label>
                                    <select name="timezone" class="form-select form-select-solid @error('timezone') is-invalid @enderror" data-control="select2" data-hide-search="true">
                                        @foreach($timezones as $value => $label)
                                            <option value="{{ $value }}" {{ old('timezone', $generalSettings['timezone'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('timezone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.general.currency') }}</label>
                                    <select name="currency" class="form-select form-select-solid @error('currency') is-invalid @enderror" data-control="select2" data-hide-search="true">
                                        @foreach($currencies as $value => $label)
                                            <option value="{{ $value }}" {{ old('currency', $generalSettings['currency'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.general.date_format') }}</label>
                                    <select name="date_format" class="form-select form-select-solid @error('date_format') is-invalid @enderror" data-control="select2" data-hide-search="true">
                                        @foreach($dateFormats as $value => $label)
                                            <option value="{{ $value }}" {{ old('date_format', $generalSettings['date_format'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('date_format')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-6">{{ __('settings.general.company_address') }}</label>
                                    <textarea name="company_address" class="form-control form-control-solid @error('company_address') is-invalid @enderror" rows="3" placeholder="{{ __('settings.general.company_address_placeholder') }}">{{ old('company_address', $generalSettings['company_address'] ?? '') }}</textarea>
                                    @error('company_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="separator my-8"></div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-check fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('settings.save_changes') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!--end::Tab General-->

            <!--begin::Tab Booking-->
            <div class="tab-pane fade" id="tab_booking">
                <form action="{{ route('admin.settings.booking') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">{{ __('settings.booking.title') }}</div>
                        </div>
                        <div class="card-body py-4">
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.booking.cutoff_time') }}</label>
                                    <div class="input-group input-group-solid">
                                        <input type="number" name="cutoff_hours" class="form-control form-control-solid @error('cutoff_hours') is-invalid @enderror"
                                            value="{{ old('cutoff_hours', $bookingSettings['cutoff_hours'] ?? 24) }}" min="1" max="168">
                                        <span class="input-group-text">{{ __('settings.booking.hours_before_departure') }}</span>
                                    </div>
                                    <div class="form-text">{{ __('settings.booking.cutoff_help') }}</div>
                                    @error('cutoff_hours')
                                        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.booking.overbooking_duration') }}</label>
                                    <div class="input-group input-group-solid">
                                        <input type="number" name="overbooking_expiry_hours" class="form-control form-control-solid @error('overbooking_expiry_hours') is-invalid @enderror"
                                            value="{{ old('overbooking_expiry_hours', $bookingSettings['overbooking_expiry_hours'] ?? 2) }}" min="1" max="24">
                                        <span class="input-group-text">{{ __('settings.booking.hours') }}</span>
                                    </div>
                                    <div class="form-text">{{ __('settings.booking.overbooking_help') }}</div>
                                    @error('overbooking_expiry_hours')
                                        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <div class="separator my-3"></div>
                                    <h5 class="fw-bold mb-4">{{ __('settings.booking.cancellation_policy') }}</h5>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.booking.free_cancellation') }}</label>
                                    <div class="input-group input-group-solid">
                                        <input type="number" name="free_cancellation_hours" class="form-control form-control-solid @error('free_cancellation_hours') is-invalid @enderror"
                                            value="{{ old('free_cancellation_hours', $bookingSettings['free_cancellation_hours'] ?? 48) }}" min="1" max="168">
                                        <span class="input-group-text">{{ __('settings.booking.hours_before_departure') }}</span>
                                    </div>
                                    @error('free_cancellation_hours')
                                        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.booking.late_cancellation_penalty') }}</label>
                                    <div class="input-group input-group-solid">
                                        <input type="number" name="late_cancellation_penalty" class="form-control form-control-solid @error('late_cancellation_penalty') is-invalid @enderror"
                                            value="{{ old('late_cancellation_penalty', $bookingSettings['late_cancellation_penalty'] ?? 100) }}" min="0" max="100">
                                        <span class="input-group-text">{{ __('settings.booking.percent_of_booking') }}</span>
                                    </div>
                                    @error('late_cancellation_penalty')
                                        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="overbooking_enabled" id="allowOverbooking" value="1"
                                            {{ old('overbooking_enabled', $bookingSettings['overbooking_enabled'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="allowOverbooking">{{ __('settings.booking.allow_overbooking') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="separator my-8"></div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-check fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('settings.save_changes') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!--end::Tab Booking-->

            <!--begin::Tab Email-->
            <div class="tab-pane fade" id="tab_email">
                <form action="{{ route('admin.settings.email') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card mb-5">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">{{ __('settings.email.title') }}</div>
                        </div>
                        <div class="card-body py-4">
                            <div class="row g-5">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.email.smtp_host') }}</label>
                                    <input type="text" name="smtp_host" class="form-control form-control-solid @error('smtp_host') is-invalid @enderror"
                                        value="{{ old('smtp_host', $emailSettings['smtp_host'] ?? '') }}" placeholder="{{ __('settings.email.smtp_host_placeholder') }}">
                                    @error('smtp_host')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.email.smtp_port') }}</label>
                                    <input type="number" name="smtp_port" class="form-control form-control-solid @error('smtp_port') is-invalid @enderror"
                                        value="{{ old('smtp_port', $emailSettings['smtp_port'] ?? 587) }}" placeholder="587">
                                    @error('smtp_port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6">{{ __('settings.email.smtp_username') }}</label>
                                    <input type="text" name="smtp_username" class="form-control form-control-solid @error('smtp_username') is-invalid @enderror"
                                        value="{{ old('smtp_username', $emailSettings['smtp_username'] ?? '') }}">
                                    @error('smtp_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6">{{ __('settings.email.smtp_password') }}</label>
                                    <input type="password" name="smtp_password" class="form-control form-control-solid" placeholder="{{ __('settings.email.smtp_password_placeholder') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.email.from_name') }}</label>
                                    <input type="text" name="from_name" class="form-control form-control-solid @error('from_name') is-invalid @enderror"
                                        value="{{ old('from_name', $emailSettings['from_name'] ?? '') }}">
                                    @error('from_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.email.from_email') }}</label>
                                    <input type="email" name="from_email" class="form-control form-control-solid @error('from_email') is-invalid @enderror"
                                        value="{{ old('from_email', $emailSettings['from_email'] ?? '') }}">
                                    @error('from_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.email.admin_notification_email') }}</label>
                                    <input type="email" name="admin_email" class="form-control form-control-solid @error('admin_email') is-invalid @enderror"
                                        value="{{ old('admin_email', $emailSettings['admin_email'] ?? '') }}">
                                    @error('admin_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="separator my-5"></div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{ __('settings.email.save_smtp') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <form action="{{ route('admin.settings.test-email') }}" method="POST" class="mb-5">
                    @csrf
                    <button type="submit" class="btn btn-light-info">
                        <i class="ki-duotone ki-sms fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('settings.email.send_test_email') }}
                    </button>
                </form>

                <form action="{{ route('admin.settings.notifications') }}" method="POST">
                    @csrf
                    @method('PUT')
                    @php
                        $notifications = $emailSettings['notifications'] ?? [];
                    @endphp
                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">{{ __('settings.notifications.title') }}</div>
                        </div>
                        <div class="card-body py-4">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-4">
                                    <thead>
                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                            <th>{{ __('settings.notifications.event') }}</th>
                                            <th class="text-center">{{ __('settings.notifications.admin') }}</th>
                                            <th class="text-center">{{ __('settings.notifications.partner') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-semibold">
                                        <tr>
                                            <td>{{ __('settings.notifications.booking_confirmed') }}</td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="booking_confirmed_admin" value="1" {{ ($notifications['booking_confirmed']['admin'] ?? true) ? 'checked' : '' }}></td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="booking_confirmed_partner" value="1" {{ ($notifications['booking_confirmed']['partner'] ?? true) ? 'checked' : '' }}></td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('settings.notifications.overbooking_requested') }}</td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="overbooking_requested_admin" value="1" {{ ($notifications['overbooking_requested']['admin'] ?? true) ? 'checked' : '' }}></td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="overbooking_requested_partner" value="1" {{ ($notifications['overbooking_requested']['partner'] ?? true) ? 'checked' : '' }}></td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('settings.notifications.overbooking_resolved') }}</td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="overbooking_resolved_admin" value="1" {{ ($notifications['overbooking_resolved']['admin'] ?? true) ? 'checked' : '' }}></td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="overbooking_resolved_partner" value="1" {{ ($notifications['overbooking_resolved']['partner'] ?? true) ? 'checked' : '' }}></td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('settings.notifications.booking_cancelled') }}</td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="booking_cancelled_admin" value="1" {{ ($notifications['booking_cancelled']['admin'] ?? true) ? 'checked' : '' }}></td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="booking_cancelled_partner" value="1" {{ ($notifications['booking_cancelled']['partner'] ?? true) ? 'checked' : '' }}></td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('settings.notifications.booking_modified') }}</td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="booking_modified_admin" value="1" {{ ($notifications['booking_modified']['admin'] ?? true) ? 'checked' : '' }}></td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="booking_modified_partner" value="1" {{ ($notifications['booking_modified']['partner'] ?? true) ? 'checked' : '' }}></td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('settings.notifications.tour_cancelled') }}</td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="tour_cancelled_admin" value="1" {{ ($notifications['tour_cancelled']['admin'] ?? true) ? 'checked' : '' }}></td>
                                            <td class="text-center"><input class="form-check-input" type="checkbox" name="tour_cancelled_partner" value="1" {{ ($notifications['tour_cancelled']['partner'] ?? true) ? 'checked' : '' }}></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="separator my-5"></div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{ __('settings.notifications.save_notifications') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!--end::Tab Email-->

            <!--begin::Tab Language-->
            <div class="tab-pane fade" id="tab_language">
                <form action="{{ route('admin.settings.language') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">{{ __('settings.language.title') }}</div>
                        </div>
                        <div class="card-body py-4">
                            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-6">
                                <i class="ki-duotone ki-information fs-2tx text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <div class="fs-6 text-gray-700">{{ __('settings.language.info_message') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.language.default_language') }}</label>
                                    <select name="default_language" class="form-select form-select-solid @error('default_language') is-invalid @enderror" data-control="select2" data-hide-search="true">
                                        @foreach($languages as $value => $label)
                                            <option value="{{ $value }}" {{ old('default_language', $languageSettings['default_language'] ?? 'en') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">{{ __('settings.language.default_language_help') }}</div>
                                    @error('default_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 required">{{ __('settings.language.partner_language') }}</label>
                                    <select name="partner_language" class="form-select form-select-solid @error('partner_language') is-invalid @enderror" data-control="select2" data-hide-search="true">
                                        <option value="default" {{ old('partner_language', $languageSettings['partner_language'] ?? 'default') == 'default' ? 'selected' : '' }}>{{ __('settings.language.same_as_default') }}</option>
                                        @foreach($languages as $value => $label)
                                            <option value="{{ $value }}" {{ old('partner_language', $languageSettings['partner_language'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">{{ __('settings.language.partner_language_help') }}</div>
                                    @error('partner_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="separator my-8"></div>
                            <h5 class="fw-bold mb-5">{{ __('settings.language.available_languages') }}</h5>
                            <div class="row g-5">
                                @foreach($languages as $code => $name)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center border border-gray-300 border-dashed rounded p-4">
                                        <div class="symbol symbol-40px me-4">
                                            <span class="symbol-label bg-light">
                                                <span class="fs-4">{{ $code === 'en' ? '🇬🇧' : '🇮🇹' }}</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-bold fs-6">{{ $name }}</span>
                                            <div class="text-muted fs-7">{{ __('settings.language.translated') }}</div>
                                        </div>
                                        @if($code === ($languageSettings['default_language'] ?? 'en'))
                                            <span class="badge badge-light-success">{{ __('settings.language.active') }}</span>
                                        @else
                                            <span class="badge badge-light-primary">{{ __('settings.language.available') }}</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="separator my-8"></div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-check fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('settings.language.save_language') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!--end::Tab Language-->

            <!--begin::Tab Pickups-->
            <div class="tab-pane fade" id="tab_pickups">
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">{{ __('settings.pickups.title') }}</div>
                        <div class="card-toolbar">
                            <a href="{{ route('admin.pickup-points.create') }}" class="btn btn-sm btn-primary">
                                <i class="ki-duotone ki-plus fs-5 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                {{ __('settings.pickups.add') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>{{ __('settings.pickups.name') }}</th>
                                        <th>{{ __('settings.pickups.location') }}</th>
                                        <th>{{ __('settings.pickups.default_time') }}</th>
                                        <th>{{ __('settings.pickups.status') }}</th>
                                        <th class="text-end">{{ __('settings.pickups.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse($pickupPoints as $pickupPoint)
                                    <tr>
                                        <td class="fw-bold">{{ $pickupPoint->name }}</td>
                                        <td>{{ $pickupPoint->location }}</td>
                                        <td>{{ $pickupPoint->default_time }}</td>
                                        <td>
                                            @if($pickupPoint->is_active)
                                                <span class="badge badge-light-success">{{ __('settings.pickups.active') }}</span>
                                            @else
                                                <span class="badge badge-light-danger">{{ __('settings.pickups.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.pickup-points.edit', $pickupPoint) }}" class="btn btn-sm btn-icon btn-light btn-active-light-primary me-1">
                                                <i class="ki-duotone ki-pencil fs-5">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </a>
                                            <form action="{{ route('admin.pickup-points.toggle-active', $pickupPoint) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-icon btn-light btn-active-light-{{ $pickupPoint->is_active ? 'warning' : 'success' }}">
                                                    <i class="ki-duotone ki-{{ $pickupPoint->is_active ? 'cross' : 'check' }} fs-5">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-10">{{ __('settings.pickups.no_pickups') }} <a href="{{ route('admin.pickup-points.create') }}">{{ __('settings.pickups.create_one') }}</a></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Tab Pickups-->

            <!--begin::Tab Users-->
            <div class="tab-pane fade" id="tab_users">
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">{{ __('settings.users.title') }}</div>
                        <div class="card-toolbar">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                                <i class="ki-duotone ki-plus fs-5 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                {{ __('settings.users.add') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>{{ __('settings.users.user') }}</th>
                                        <th>{{ __('settings.users.role') }}</th>
                                        <th>{{ __('settings.users.last_login') }}</th>
                                        <th>{{ __('settings.users.status') }}</th>
                                        <th class="text-end">{{ __('settings.users.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse($users as $user)
                                    @php
                                        $initials = collect(explode(' ', $user->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->join('');
                                        $colorClass = match($user->role->value) {
                                            'admin' => 'primary',
                                            'driver' => 'info',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <span class="symbol-label bg-light-{{ $colorClass }} text-{{ $colorClass }} fw-bold">{{ $initials }}</span>
                                                </div>
                                                <div>
                                                    <span class="fw-bold">{{ $user->name }}</span>
                                                    <div class="text-muted fs-7">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-light-{{ $colorClass }}">{{ $user->role->label() }}</span></td>
                                        <td>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : __('settings.users.never') }}</td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge badge-light-success">{{ __('settings.users.active') }}</span>
                                            @else
                                                <span class="badge badge-light-danger">{{ __('settings.users.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-light">{{ __('settings.users.edit') }}</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-10">{{ __('settings.users.no_users') }} <a href="{{ route('admin.users.create') }}">{{ __('settings.users.create_one') }}</a></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Tab Users-->

            <!--begin::Tab Voucher-->
            <div class="tab-pane fade" id="tab_voucher">
                <form action="{{ route('admin.settings.voucher') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">{{ __('settings.voucher.title') }}</div>
                        </div>
                        <div class="card-body py-4">
                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-6">
                                <i class="ki-duotone ki-information fs-2tx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <div class="fs-6 text-gray-700">{{ __('settings.voucher.info') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-5">
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-6">{{ __('settings.voucher.header_text') }}</label>
                                    <textarea name="voucher_header" class="form-control form-control-solid @error('voucher_header') is-invalid @enderror" rows="3" placeholder="{{ __('settings.voucher.header_text_placeholder') }}">{{ old('voucher_header', $voucherSettings['voucher_header'] ?? '') }}</textarea>
                                    @error('voucher_header')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('settings.voucher.header_text_help') }}</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-6">{{ __('settings.voucher.operational_notes') }}</label>
                                    <textarea name="voucher_notes" class="form-control form-control-solid @error('voucher_notes') is-invalid @enderror" rows="4" placeholder="{{ __('settings.voucher.operational_notes_placeholder') }}">{{ old('voucher_notes', $voucherSettings['voucher_notes'] ?? '') }}</textarea>
                                    @error('voucher_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('settings.voucher.operational_notes_help') }}</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-6">{{ __('settings.voucher.footer_text') }}</label>
                                    <textarea name="voucher_footer" class="form-control form-control-solid @error('voucher_footer') is-invalid @enderror" rows="3" placeholder="{{ __('settings.voucher.footer_text_placeholder') }}">{{ old('voucher_footer', $voucherSettings['voucher_footer'] ?? '') }}</textarea>
                                    @error('voucher_footer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('settings.voucher.footer_text_help') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ki-duotone ki-check fs-3 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ __('settings.save_changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!--end::Tab Voucher-->

            <!--begin::Tab Backup-->
            <div class="tab-pane fade" id="tab_backup">
                <div class="card mb-5">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">{{ __('settings.backup.title') }}</div>
                        <div class="card-toolbar">
                            <form action="{{ route('admin.settings.backup') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="ki-duotone ki-cloud-download fs-5 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('settings.backup.create_now') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-6">
                            <i class="ki-duotone ki-information fs-2tx text-info me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="fs-6 text-gray-700">{{ __('settings.backup.info_message') }}</div>
                                </div>
                            </div>
                        </div>
                        <h6 class="fw-bold mb-4">{{ __('settings.backup.recent') }}</h6>
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>{{ __('settings.backup.date') }}</th>
                                        <th>{{ __('settings.backup.file') }}</th>
                                        <th>{{ __('settings.backup.size') }}</th>
                                        <th>{{ __('settings.backup.status') }}</th>
                                        <th class="text-end">{{ __('settings.backup.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse($backups as $backup)
                                    <tr>
                                        <td>{{ $backup->ran_at?->format('M d, Y H:i') ?? '-' }}</td>
                                        <td>{{ $backup->file_path ? basename($backup->file_path) : '-' }}</td>
                                        <td>{{ $backup->file_size ? number_format($backup->file_size / 1024 / 1024, 2) . ' MB' : '-' }}</td>
                                        <td>
                                            @if($backup->success)
                                                <span class="badge badge-light-success">{{ __('settings.backup.success') }}</span>
                                            @else
                                                <span class="badge badge-light-danger">{{ __('settings.backup.failed') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($backup->success && $backup->file_path)
                                                <a href="{{ route('admin.backup-logs.download', $backup) }}" class="btn btn-sm btn-light">{{ __('settings.backup.download') }}</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-10">{{ __('settings.backup.no_backups') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">{{ __('settings.logs.title') }}</div>
                        <div class="card-toolbar">
                            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-light me-2">
                                <i class="ki-duotone ki-eye fs-5 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                {{ __('settings.logs.view_audit') }}
                            </a>
                            <a href="{{ route('admin.email-logs.index') }}" class="btn btn-sm btn-light">
                                <i class="ki-duotone ki-sms fs-5 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ __('settings.logs.view_email') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="d-flex flex-stack p-4 bg-light rounded mb-3">
                            <div>
                                <span class="fw-bold">{{ __('settings.logs.audit_logs') }}</span>
                                <div class="text-muted fs-7">{{ __('settings.logs.audit_description') }}</div>
                            </div>
                            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-light">{{ __('settings.logs.view') }}</a>
                        </div>
                        <div class="d-flex flex-stack p-4 bg-light rounded mb-3">
                            <div>
                                <span class="fw-bold">{{ __('settings.logs.email_logs') }}</span>
                                <div class="text-muted fs-7">{{ __('settings.logs.email_description') }}</div>
                            </div>
                            <a href="{{ route('admin.email-logs.index') }}" class="btn btn-sm btn-light">{{ __('settings.logs.view') }}</a>
                        </div>
                        <div class="d-flex flex-stack p-4 bg-light rounded">
                            <div>
                                <span class="fw-bold">{{ __('settings.logs.backup_logs') }}</span>
                                <div class="text-muted fs-7">{{ __('settings.logs.backup_description') }}</div>
                            </div>
                            <a href="{{ route('admin.backup-logs.index') }}" class="btn btn-sm btn-light">{{ __('settings.logs.view') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Tab Backup-->
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection

@push('scripts')
<script>
    // Preserve active tab on page reload
    document.addEventListener('DOMContentLoaded', function() {
        var hash = window.location.hash;
        if (hash) {
            var tab = document.querySelector('a[href="' + hash + '"]');
            if (tab) {
                var bsTab = new bootstrap.Tab(tab);
                bsTab.show();
            }
        }
    });
</script>
@endpush
