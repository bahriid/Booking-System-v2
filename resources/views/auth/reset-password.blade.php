<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ __('auth.reset_password_title') }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
</head>
<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center">
    <script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <div class="w-lg-500px p-10">
                        <form class="form w-100" action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <!--begin::Heading-->
                            <div class="text-center mb-10">
                                <h1 class="text-gray-900 fw-bolder mb-3">{{ __('auth.reset_password_heading') }}</h1>
                                <div class="text-gray-500 fw-semibold fs-6">{{ __('auth.reset_password_description') }}</div>
                            </div>
                            <!--end::Heading-->

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
                                <input type="email" placeholder="{{ __('auth.email_placeholder') }}" name="email" value="{{ old('email', $email) }}" autocomplete="email" class="form-control form-control-solid @error('email') is-invalid @enderror" required />
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="fv-row mb-8">
                                <label class="form-label fw-semibold fs-6 mb-2">{{ __('auth.new_password') }}</label>
                                <input type="password" placeholder="{{ __('auth.new_password_placeholder') }}" name="password" autocomplete="new-password" class="form-control form-control-solid @error('password') is-invalid @enderror" required />
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="fv-row mb-8">
                                <label class="form-label fw-semibold fs-6 mb-2">{{ __('auth.confirm_password') }}</label>
                                <input type="password" placeholder="{{ __('auth.confirm_password_placeholder') }}" name="password_confirmation" autocomplete="new-password" class="form-control form-control-solid" required />
                            </div>
                            <!--end::Input group-->

                            <!--begin::Actions-->
                            <div class="d-flex flex-wrap justify-content-center pb-lg-0">
                                <button type="submit" class="btn btn-primary me-4">
                                    <span class="indicator-label">{{ __('auth.reset_password_button') }}</span>
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-light">{{ __('auth.back_to_login') }}</a>
                            </div>
                            <!--end::Actions-->
                        </form>
                    </div>
                </div>

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
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                    <a href="#" class="mb-12">
                        <img alt="MagShip" src="{{ asset('assets/media/logos/magship-logo.png') }}" class="h-60px rounded" style="padding: 8px; background: white;" />
                    </a>
                    <h1 class="text-white fs-2qx fw-bold text-center mb-7">{{ __('auth.sidebar_title') }}</h1>
                    <div class="text-white fs-base text-center mb-10">
                        {{ __('auth.sidebar_description') }}
                    </div>
                </div>
            </div>
            <!--end::Aside-->
        </div>
    </div>
    <script>var hostUrl = "{{ asset('assets/') }}/";</script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
</body>
</html>
