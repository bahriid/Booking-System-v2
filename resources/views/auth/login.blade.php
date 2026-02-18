<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ __('auth.page_title') }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
</head>
<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center">
    <!--begin::Theme mode setup-->
    <script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
    <!--end::Theme mode setup-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Authentication-->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Form-->
                        <form class="form w-100" action="{{ route('login') }}" method="POST">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 fw-bolder mb-3">{{ __('auth.sign_in') }}</h1>
                                <!--end::Title-->
                                <!--begin::Subtitle-->
                                <div class="text-gray-500 fw-semibold fs-6">{{ __('auth.access_account') }}</div>
                                <!--end::Subtitle-->
                            </div>
                            <!--end::Heading-->

                            <!--begin::Success Alert-->
                            @if (session('status'))
                            <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                                <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span>{{ session('status') }}</span>
                                </div>
                            </div>
                            @endif
                            <!--end::Success Alert-->

                            <!--begin::Error Alert-->
                            @if ($errors->any())
                            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                                <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span>{{ $errors->first() }}</span>
                                </div>
                            </div>
                            @endif
                            <!--end::Error Alert-->

                            <!--begin::Input group-->
                            <div class="fv-row mb-8">
                                <label class="form-label fw-semibold fs-6 mb-2">{{ __('auth.email') }}</label>
                                <input type="email" placeholder="{{ __('auth.email_placeholder') }}" name="email" value="{{ old('email') }}" autocomplete="email" class="form-control form-control-solid @error('email') is-invalid @enderror" required />
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="fv-row mb-3">
                                <label class="form-label fw-semibold fs-6 mb-2">{{ __('auth.password') }}</label>
                                <input type="password" placeholder="{{ __('auth.password_placeholder') }}" name="password" autocomplete="current-password" class="form-control form-control-solid @error('password') is-invalid @enderror" required />
                            </div>
                            <!--end::Input group-->

                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <label class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="remember" value="1" />
                                    <span class="form-check-label text-gray-700 fs-6">{{ __('auth.remember_me') }}</span>
                                </label>
                                <!--begin::Link-->
                                <a href="{{ route('password.request') }}" class="link-primary">{{ __('auth.forgot_password') }}</a>
                                <!--end::Link-->
                            </div>
                            <!--end::Wrapper-->

                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" class="btn btn-primary">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">{{ __('auth.sign_in_button') }}</span>
                                    <!--end::Indicator label-->
                                </button>
                            </div>
                            <!--end::Submit button-->

                            <!--begin::Demo accounts-->
                            <div class="text-center">
                                <div class="text-gray-500 fw-semibold fs-6 mb-5">{{ __('auth.demo_accounts') }}</div>
                                <div class="d-flex flex-column gap-3">
                                    <div class="bg-light rounded p-4">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-light-primary me-3">{{ __('auth.demo_admin') }}</span>
                                            <div class="text-start flex-grow-1">
                                                <div class="fw-bold fs-7">admin@magship.test</div>
                                                <div class="text-muted fs-8">{{ __('auth.password') }}: {{ __('auth.demo_password') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-light rounded p-4">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-light-success me-3">{{ __('auth.demo_partner') }}</span>
                                            <div class="text-start flex-grow-1">
                                                <div class="fw-bold fs-7">bookings+staff@excelsiorvittoria.com</div>
                                                <div class="text-muted fs-8">{{ __('auth.password') }}: {{ __('auth.demo_password') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-light rounded p-4">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-light-info me-3">{{ __('auth.demo_driver') }}</span>
                                            <div class="text-start flex-grow-1">
                                                <div class="fw-bold fs-7">driver@magship.test</div>
                                                <div class="text-muted fs-8">{{ __('auth.password') }}: {{ __('auth.demo_password') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Demo accounts-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Form-->

                <!--begin::Footer-->
                <div class="d-flex flex-center flex-wrap px-5">
                    <div class="text-gray-500 fw-semibold fs-7">
                        {{ date('Y') }} &copy; MagShip B2B Booking
                    </div>
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Body-->

            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2" style="background: linear-gradient(135deg, #1e3a5f 0%, #3a7ca5 100%);">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                    <!--begin::Logo-->
                    <a href="#" class="mb-12">
                        <img alt="MagShip" src="{{ asset('assets/media/logos/magship-logo.png') }}" class="h-60px rounded" style="padding: 8px; background: white;" />
                    </a>
                    <!--end::Logo-->
                    <!--begin::Title-->
                    <h1 class="text-white fs-2qx fw-bold text-center mb-7">{{ __('auth.sidebar_title') }}</h1>
                    <!--end::Title-->
                    <!--begin::Text-->
                    <div class="text-white fs-base text-center mb-10">
                        {{ __('auth.sidebar_description') }}
                    </div>
                    <!--end::Text-->
                    <!--begin::Features-->
                    <div class="d-flex flex-column gap-5 text-white">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-check-circle fs-2x text-white me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fs-5">{{ __('auth.feature_availability') }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-check-circle fs-2x text-white me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fs-5">{{ __('auth.feature_partner_portal') }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-check-circle fs-2x text-white me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fs-5">{{ __('auth.feature_vouchers') }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-check-circle fs-2x text-white me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fs-5">{{ __('auth.feature_accounting') }}</span>
                        </div>
                    </div>
                    <!--end::Features-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Aside-->
        </div>
        <!--end::Authentication-->
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->
    <script>var hostUrl = "{{ asset('assets/') }}/";</script>
    <!--begin::Global Javascript Bundle-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->
    <!--end::Javascript-->
</body>
</html>
