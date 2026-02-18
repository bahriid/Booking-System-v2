<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>@yield('title', __('nav.dashboard')) - MagShip B2B</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets-->
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    @stack('styles')
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled">
    <!--begin::Theme mode setup-->
    <script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
    <!--end::Theme mode setup-->
    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="page d-flex flex-column flex-column-fluid">
            <!--begin::Header-->
            <div id="kt_header" class="header" data-kt-sticky="true" data-kt-sticky-name="header" data-kt-sticky-offset="{default: '200px', lg: '300px'}">
                <!--begin::Container-->
                <div class="header-top-container container-xxl d-flex flex-grow-1 flex-stack">
                    <!--begin::Header Logo-->
                    <div class="d-flex align-items-center me-5">
                        <!--begin::Heaeder menu toggle-->
                        <div class="d-lg-none btn btn-icon btn-color-white w-30px h-30px me-2 ms-n2" id="kt_header_menu_toggle">
                            <i class="ki-duotone ki-abstract-14 fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <!--end::Heaeder menu toggle-->
                        <!--begin::Logo-->
                        <a href="{{ route('admin.dashboard') }}">
                            <img alt="MagShip" src="{{ asset('assets/media/logos/magship-logo.png') }}" class="h-35px rounded" style="padding: 4px; background: white;" />
                        </a>
                        <!--end::Logo-->
                    </div>
                    <!--end::Header Logo-->
                    <!--begin::Toolbar wrapper-->
                    <div class="topbar d-flex align-items-stretch flex-shrink-0" id="kt_topbar">
                        <!--begin::Notifications-->
                        <div class="d-flex align-items-center ms-2 ms-lg-3">
                            <div class="btn btn-icon btn-custom w-30px h-30px w-md-40px h-md-40px position-relative" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-notification-status fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                @if($headerNotificationCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge badge-circle badge-danger">{{ $headerNotificationCount }}</span>
                                @endif
                            </div>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
                                <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}')">
                                    <h3 class="text-white fw-semibold px-9 mt-10 mb-6">{{ __('nav.notifications') }} <span class="fs-8 opacity-75 ps-3">{{ $headerNotificationCount }} {{ __('nav.pending') }}</span></h3>
                                </div>
                                <div class="scroll-y mh-325px my-5 px-8">
                                    @forelse($headerNotifications as $notification)
                                        <div class="d-flex flex-stack py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-35px me-4">
                                                    @if($notification['type'] === 'overbooking')
                                                        <span class="symbol-label bg-light-warning">
                                                            <i class="ki-duotone ki-timer fs-2 text-warning">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </span>
                                                    @elseif($notification['type'] === 'cancellation')
                                                        <span class="symbol-label bg-light-danger">
                                                            <i class="ki-duotone ki-cross-circle fs-2 text-danger">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </span>
                                                    @else
                                                        <span class="symbol-label bg-light-success">
                                                            <i class="ki-duotone ki-check fs-2 text-success">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="mb-0 me-2">
                                                    <a href="{{ route('admin.bookings.show', $notification['booking']) }}" class="fs-6 text-gray-800 text-hover-primary fw-bold">
                                                        @if($notification['type'] === 'overbooking')
                                                            {{ __('nav.overbooking_request') }}
                                                        @elseif($notification['type'] === 'cancellation')
                                                            {{ __('nav.booking_cancelled') }}
                                                        @else
                                                            {{ __('nav.new_booking') }}
                                                        @endif
                                                    </a>
                                                    <div class="text-gray-500 fs-7">{{ $notification['booking']->partner->name ?? '' }} - {{ $notification['booking']->passengers->count() }} pax</div>
                                                </div>
                                            </div>
                                            <span class="badge badge-light fs-8">{{ $notification['created_at']->diffForHumans(null, true, true) }}</span>
                                        </div>
                                    @empty
                                        <div class="py-4 text-center text-gray-500 fs-7">{{ __('nav.no_notifications') }}</div>
                                    @endforelse
                                </div>
                            </div>
                            <!--end::Menu-->
                        </div>
                        <!--end::Notifications-->
                        <!--begin::User menu-->
                        <div class="d-flex align-items-center ms-2 ms-lg-3">
                            <div class="btn btn-icon btn-custom w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-user fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--begin::User account menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-325px" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <div class="menu-content d-flex align-items-center px-3">
                                        <div class="symbol symbol-50px me-5 flex-shrink-0">
                                            <span class="symbol-label bg-light-primary text-primary fs-3 fw-bold">{{ auth()->user()->initials }}</span>
                                        </div>
                                        <div class="d-flex flex-column overflow-hidden">
                                            <div class="fw-bold fs-5 text-truncate">{{ auth()->user()->name }}</div>
                                            <span class="fw-semibold text-muted fs-7 text-truncate">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="separator my-2"></div>
                                <div class="menu-item px-5">
                                    <a href="{{ route('admin.settings') }}" class="menu-link px-5">{{ __('nav.settings') }}</a>
                                </div>
                                <div class="menu-item px-5">
                                    <a href="{{ route('profile.password') }}" class="menu-link px-5">{{ __('profile.change_password') }}</a>
                                </div>
                                <x-language-switcher />
                                <div class="separator my-2"></div>
                                <div class="menu-item px-5">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="menu-link px-5 bg-transparent border-0 w-100 text-start text-hover-danger">{{ __('nav.logout') }}</button>
                                    </form>
                                </div>
                            </div>
                            <!--end::User account menu-->
                        </div>
                        <!--end::User menu-->
                    </div>
                    <!--end::Toolbar wrapper-->
                </div>
                <!--end::Container-->
                <!--begin::Container-->
                <div class="header-menu-container d-flex align-items-stretch flex-stack w-100" id="kt_header_nav">
                    <div class="header-menu container-xxl flex-column align-items-stretch flex-lg-row" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                        <!--begin::Menu-->
                        <div class="menu menu-column menu-lg-row menu-rounded menu-active-bg menu-title-gray-800 menu-state-primary menu-arrow-gray-500 fw-semibold my-5 my-lg-0 align-items-stretch px-2 px-lg-0" id="kt_header_menu" data-kt-menu="true">
                            <!--begin::Menu item - Dashboard-->
                            <div class="menu-item me-0 me-lg-2 {{ request()->routeIs('admin.dashboard') ? 'here show menu-here-bg' : '' }}">
                                <a class="menu-link py-3" href="{{ route('admin.dashboard') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-element-11 fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">{{ __('nav.dashboard') }}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item - Tours-->
                            <div class="menu-item me-0 me-lg-2 {{ request()->routeIs('admin.tours.*') ? 'here show menu-here-bg' : '' }}">
                                <a class="menu-link py-3" href="{{ route('admin.tours.index') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-map fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">{{ __('nav.tours') }}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item - Calendar-->
                            <div class="menu-item me-0 me-lg-2 {{ request()->routeIs('admin.calendar') ? 'here show menu-here-bg' : '' }}">
                                <a class="menu-link py-3" href="{{ route('admin.calendar') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-calendar-8 fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                            <span class="path6"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">{{ __('nav.calendar') }}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item - Bookings-->
                            <div class="menu-item me-0 me-lg-2 {{ request()->routeIs('admin.bookings.*') ? 'here show menu-here-bg' : '' }}">
                                <a class="menu-link py-3" href="{{ route('admin.bookings.index') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-document fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">{{ __('nav.bookings') }}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item - Partners-->
                            <div class="menu-item me-0 me-lg-2 {{ request()->routeIs('admin.partners.*') ? 'here show menu-here-bg' : '' }}">
                                <a class="menu-link py-3" href="{{ route('admin.partners.index') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-people fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">{{ __('nav.partners') }}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item - Accounting-->
                            <div class="menu-item me-0 me-lg-2 {{ request()->routeIs('admin.accounting.*') ? 'here show menu-here-bg' : '' }}">
                                <a class="menu-link py-3" href="{{ route('admin.accounting.index') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-wallet fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">{{ __('nav.accounting') }}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item - Reports-->
                            <div class="menu-item me-0 me-lg-2 {{ request()->routeIs('admin.reports.*') ? 'here show menu-here-bg' : '' }}">
                                <a class="menu-link py-3" href="{{ route('admin.reports.index') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-graph-up fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                            <span class="path6"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">{{ __('nav.reports') }}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item - System (dropdown)-->
                            <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2 {{ request()->routeIs('admin.email-logs.*') || request()->routeIs('admin.audit-logs.*') || request()->routeIs('admin.backup-logs.*') ? 'here show menu-here-bg' : '' }}">
                                <span class="menu-link py-3">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-code fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">{{ __('nav.system') }}</span>
                                    <span class="menu-arrow d-lg-none"></span>
                                </span>
                                <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('admin.email-logs.*') ? 'active' : '' }}" href="{{ route('admin.email-logs.index') }}">
                                            <span class="menu-icon">
                                                <i class="ki-duotone ki-sms fs-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                            <span class="menu-title">{{ __('nav.email_logs') }}</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}">
                                            <span class="menu-icon">
                                                <i class="ki-duotone ki-shield-tick fs-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                            <span class="menu-title">{{ __('nav.audit_logs') }}</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('admin.backup-logs.*') ? 'active' : '' }}" href="{{ route('admin.backup-logs.index') }}">
                                            <span class="menu-icon">
                                                <i class="ki-duotone ki-folder-down fs-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                            <span class="menu-title">{{ __('nav.backup_logs') }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item - Settings-->
                            <div class="menu-item me-0 me-lg-2 {{ request()->routeIs('admin.settings') ? 'here show menu-here-bg' : '' }}">
                                <a class="menu-link py-3" href="{{ route('admin.settings') }}">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-setting-2 fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">{{ __('nav.settings') }}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu-->
                    </div>
                </div>
                <!--end::Container-->
            </div>
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid container-xxl" id="kt_wrapper">
                <!--begin::Toolbar-->
                <div class="toolbar d-flex flex-stack flex-wrap py-4 gap-2" id="kt_toolbar">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column">
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 mb-1">
                            <li class="breadcrumb-item text-gray-700 fw-bold lh-1">
                                <a href="{{ route('admin.dashboard') }}" class="text-gray-600 text-hover-primary">
                                    <i class="ki-duotone ki-home text-gray-700 fs-6"></i>
                                </a>
                            </li>
                            @yield('breadcrumb')
                        </ul>
                        <!--end::Breadcrumb-->
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bolder fs-3 m-0">@yield('page-title', 'Dashboard')</h1>
                        <!--end::Title-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center">
                        @yield('toolbar-actions')
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar-->
                <!--begin::Main-->
                <div class="d-flex flex-row flex-column-fluid align-items-stretch">
                    <!--begin::Content-->
                    <div class="content flex-row-fluid" id="kt_content">
                        @yield('content')
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Main-->
            </div>
            <!--end::Wrapper-->
            <!--begin::Footer-->
            <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                <div class="container-xxl d-flex flex-column flex-md-row flex-stack">
                    <div class="text-gray-900 order-2 order-md-1">
                        <span class="text-muted fw-semibold me-1">{{ date('Y') }} &copy;</span>
                        <a href="#" class="text-gray-800 text-hover-primary">MagShip B2B Booking</a>
                    </div>
                </div>
            </div>
            <!--end::Footer-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->
    <!--end::Main-->
    <!--begin::Javascript-->
    <script>var hostUrl = "{{ asset('assets/') }}/";</script>
    <!--begin::Global Javascript Bundle-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript-->
    <script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <!--end::Vendors Javascript-->
    @stack('scripts')
    <!--end::Javascript-->
</body>
</html>
