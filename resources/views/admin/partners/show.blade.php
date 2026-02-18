@extends('layouts.admin')

@section('title', $partner->name)
@section('page-title', __('partners.partner_details'))

@section('breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('admin.partners.index') }}" class="text-muted text-hover-primary">{{ __('partners.title') }}</a>
</li>
<li class="breadcrumb-item">
    <span class="bullet bg-gray-500 w-5px h-2px"></span>
</li>
<li class="breadcrumb-item text-muted">{{ $partner->name }}</li>
@endsection

@section('toolbar-actions')
<a href="{{ route('admin.partners.edit', $partner) }}" class="btn btn-sm btn-light-warning">
    <i class="ki-duotone ki-pencil fs-5 me-1">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
    {{ __('partners.edit') }}
</a>
@endsection

@section('content')
@if (session('success'))
    <x-ui.alert type="success" :message="session('success')" dismissible class="mb-5" />
@endif

<div class="row g-5 g-xl-10">
    <!--begin::Main Column-->
    <div class="col-xl-8">
        <!--begin::Partner Summary Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-body pt-9 pb-0">
                <!--begin::Details-->
                <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                    <!--begin::Avatar-->
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                            <span class="symbol-label bg-light-{{ $partner->type->color() }} text-{{ $partner->type->color() }} fs-2hx fw-bold">{{ $partner->initials }}</span>
                            <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-{{ $partner->is_active ? 'success' : 'secondary' }} rounded-circle border border-4 border-body h-20px w-20px"></div>
                        </div>
                    </div>
                    <!--end::Avatar-->
                    <!--begin::Info-->
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-gray-900 fs-2 fw-bold me-3">{{ $partner->name }}</span>
                                    @if ($partner->is_active)
                                        <span class="badge badge-light-success fs-8 fw-bold">{{ __('partners.active') }}</span>
                                    @else
                                        <span class="badge badge-light-secondary fs-8 fw-bold">{{ __('partners.suspended') }}</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                    <span class="d-flex align-items-center text-gray-500 me-5 mb-2">
                                        <i class="ki-duotone ki-{{ $partner->type === \App\Enums\PartnerType::HOTEL ? 'building' : 'globe' }} fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        {{ $partner->type->label() }}
                                    </span>
                                    <a href="mailto:{{ $partner->email }}" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                        <i class="ki-duotone ki-sms fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ $partner->email }}
                                    </a>
                                    @if ($partner->phone)
                                    <span class="d-flex align-items-center text-gray-500 mb-2">
                                        <i class="ki-duotone ki-phone fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ $partner->phone }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap flex-stack">
                            <div class="d-flex flex-column flex-grow-1 pe-8">
                                <div class="d-flex flex-wrap">
                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-document fs-2 text-primary me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="fs-2 fw-bold">{{ $partner->bookings->count() }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-500">{{ __('partners.total_bookings') }}</div>
                                    </div>
                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-people fs-2 text-info me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                            <div class="fs-2 fw-bold">{{ $partner->passengers()->count() }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-500">{{ __('partners.total_passengers') }}</div>
                                    </div>
                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-dollar fs-2 text-success me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <div class="fs-2 fw-bold">{{ number_format($partner->bookings->sum('total_amount'), 2) }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-500">{{ __('partners.total_revenue') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Info-->
                </div>
                <!--end::Details-->
                <!--begin::Navs-->
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-5 me-6 active" data-bs-toggle="tab" href="#tab_bookings">{{ __('partners.bookings_tab') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_accounting">{{ __('partners.accounting_tab') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_info">{{ __('partners.info_billing') }}</a>
                    </li>
                </ul>
                <!--end::Navs-->
            </div>
        </div>
        <!--end::Partner Summary Card-->

        <!--begin::Tab Content-->
        <div class="tab-content">
            <!--begin::Tab Bookings-->
            <div class="tab-pane fade show active" id="tab_bookings">
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            {{ __('partners.recent_bookings') }}
                        </div>
                        <div class="card-toolbar">
                            <a href="{{ route('admin.bookings.index') }}?partner={{ $partner->id }}" class="btn btn-sm btn-light">{{ __('partners.view_all') }}</a>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_bookings_table">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>{{ __('partners.booking_code') }}</th>
                                        <th>{{ __('partners.tour') }}</th>
                                        <th>{{ __('partners.date') }}</th>
                                        <th>{{ __('partners.pax') }}</th>
                                        <th>{{ __('partners.status') }}</th>
                                        <th class="text-end">{{ __('partners.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse ($partner->bookings as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary fw-bold">{{ $booking->booking_code }}</a>
                                        </td>
                                        <td>{{ $booking->tourDeparture?->tour?->name ?? 'N/A' }}</td>
                                        <td>{{ $booking->tourDeparture->date?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $paxCounts = $booking->passengers->groupBy('pax_type')->map->count();
                                            @endphp
                                            @foreach ($paxCounts as $type => $count)
                                                <span class="badge badge-light-{{ $type === 'adult' ? 'primary' : ($type === 'child' ? 'info' : 'secondary') }}">{{ $count }} {{ strtoupper(substr($type, 0, 3)) }}</span>
                                            @endforeach
                                        </td>
                                        <td><span class="badge badge-light-{{ $booking->status->color() }}">{{ $booking->status->label() }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-icon btn-light btn-active-light-primary">
                                                <i class="ki-duotone ki-eye fs-5">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">{{ __('partners.no_bookings_found') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Tab Bookings-->

            <!--begin::Tab Accounting-->
            <div class="tab-pane fade" id="tab_accounting">
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            {{ __('partners.account_statement') }}
                        </div>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-light-success me-2" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                                <i class="ki-duotone ki-wallet fs-5 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                {{ __('partners.record_payment') }}
                            </button>
                            <a href="{{ route('admin.accounting.index') }}?partner={{ $partner->id }}" class="btn btn-sm btn-light">
                                <i class="ki-duotone ki-file-down fs-5 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ __('partners.export_csv') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <!--begin::Summary-->
                        @php
                            $activeBilled = $partner->bookings->whereNotIn('status', ['cancelled', 'expired', 'rejected'])->sum('total_amount');
                            $cancelledPenalties = $partner->bookings->where('status', 'cancelled')->where('penalty_amount', '>', 0)->sum('penalty_amount');
                            $totalRevenue = $activeBilled + $cancelledPenalties;
                            $totalPaid = $partner->payments->sum('amount');
                            $outstanding = $totalRevenue - $totalPaid;
                        @endphp
                        <div class="row g-5 mb-8">
                            <div class="col-md-4">
                                <div class="bg-light-primary rounded p-5">
                                    <div class="text-primary fw-bold fs-6 mb-1">{{ __('partners.total_revenue') }}</div>
                                    <div class="text-gray-900 fw-bold fs-2">{{ number_format($totalRevenue, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light-success rounded p-5">
                                    <div class="text-success fw-bold fs-6 mb-1">{{ __('partners.paid') }}</div>
                                    <div class="text-gray-900 fw-bold fs-2">{{ number_format($totalPaid, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light-{{ $outstanding > 0 ? 'danger' : 'success' }} rounded p-5">
                                    <div class="text-{{ $outstanding > 0 ? 'danger' : 'success' }} fw-bold fs-6 mb-1">{{ __('partners.outstanding') }}</div>
                                    <div class="text-gray-900 fw-bold fs-2">{{ number_format($outstanding, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <!--end::Summary-->

                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>{{ __('partners.date') }}</th>
                                        <th>{{ __('partners.description') }}</th>
                                        <th class="text-end">{{ __('partners.debit') }}</th>
                                        <th class="text-end">{{ __('partners.credit') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse ($partner->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                        <td>Payment - {{ ucfirst($payment->method ?? 'Bank Transfer') }}</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end text-success">{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">{{ __('partners.no_payment_records') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Tab Accounting-->

            <!--begin::Tab Info-->
            <div class="tab-pane fade" id="tab_info">
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            {{ __('partners.company_billing_information') }}
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="row">
                            <div class="col-md-6 mb-8">
                                <h5 class="text-gray-800 fw-bold mb-4">{{ __('partners.company_details') }}</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted fw-semibold w-150px">{{ __('partners.company_name') }}</td>
                                        <td class="fw-bold">{{ $partner->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">{{ __('partners.type') }}</td>
                                        <td>{{ $partner->type->label() }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">{{ __('partners.email') }}</td>
                                        <td>{{ $partner->email }}</td>
                                    </tr>
                                    @if ($partner->phone)
                                    <tr>
                                        <td class="text-muted fw-semibold">{{ __('partners.phone') }}</td>
                                        <td>{{ $partner->phone }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6 mb-8">
                                <h5 class="text-gray-800 fw-bold mb-4">{{ __('partners.billing_information') }}</h5>
                                <table class="table table-borderless">
                                    @if ($partner->vat_number)
                                    <tr>
                                        <td class="text-muted fw-semibold w-150px">{{ __('partners.vat_number') }}</td>
                                        <td class="fw-bold">{{ $partner->vat_number }}</td>
                                    </tr>
                                    @endif
                                    @if ($partner->sdi_pec)
                                    <tr>
                                        <td class="text-muted fw-semibold">{{ __('partners.sdi_pec') }}</td>
                                        <td>{{ $partner->sdi_pec }}</td>
                                    </tr>
                                    @endif
                                    @if ($partner->address)
                                    <tr>
                                        <td class="text-muted fw-semibold">{{ __('partners.address') }}</td>
                                        <td>{!! nl2br(e($partner->address)) !!}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        @if ($partner->notes)
                        <div class="separator my-5"></div>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-gray-800 fw-bold mb-4">{{ __('partners.internal_notes') }}</h5>
                                <div class="bg-light rounded p-5">
                                    <p class="text-gray-700 mb-0">{!! nl2br(e($partner->notes)) !!}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <!--end::Tab Info-->
        </div>
        <!--end::Tab Content-->
    </div>
    <!--end::Main Column-->

    <!--begin::Sidebar-->
    <div class="col-xl-4">
        <!--begin::Quick Actions Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('partners.quick_actions') }}</div>
            </div>
            <div class="card-body py-4">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.bookings.index') }}?partner={{ $partner->id }}&action=create" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-3 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        {{ __('partners.create_booking') }}
                    </a>
                    <button type="button" class="btn btn-light-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                        <i class="ki-duotone ki-wallet fs-3 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        {{ __('partners.record_payment') }}
                    </button>
                    <a href="mailto:{{ $partner->email }}" class="btn btn-light-info">
                        <i class="ki-duotone ki-sms fs-3 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('partners.send_email') }}
                    </a>
                </div>
            </div>
        </div>
        <!--end::Quick Actions Card-->

        <!--begin::Account Status Card-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('partners.account_status') }}</div>
            </div>
            <div class="card-body py-4">
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-light-{{ $partner->is_active ? 'success' : 'secondary' }}">
                            <i class="ki-duotone ki-{{ $partner->is_active ? 'check-circle' : 'lock' }} fs-2x text-{{ $partner->is_active ? 'success' : 'secondary' }}">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-4 fw-bold text-{{ $partner->is_active ? 'success' : 'secondary' }}">{{ $partner->is_active ? __('partners.active') : __('partners.suspended') }}</span>
                        <span class="text-muted fs-7">{{ $partner->is_active ? __('partners.can_login_and_book') : __('partners.cannot_login_or_book') }}</span>
                    </div>
                </div>
                <div class="separator my-4"></div>
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('partners.member_since') }}</span>
                    <span class="fw-bold">{{ $partner->created_at->format('M d, Y') }}</span>
                </div>
                @if ($partner->users->isNotEmpty())
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fw-semibold">{{ __('partners.last_login') }}</span>
                    <span class="fw-bold">{{ $partner->users->first()->last_login_at?->format('M d, Y') ?? __('partners.never') }}</span>
                </div>
                @endif
                @if ($partner->bookings->isNotEmpty())
                <div class="d-flex flex-stack mb-5">
                    <span class="text-muted fw-semibold">{{ __('partners.last_booking') }}</span>
                    <span class="fw-bold">{{ $partner->bookings->first()->created_at->format('M d, Y') }}</span>
                </div>
                @endif
                @if ($partner->is_active)
                <form action="{{ route('admin.partners.update', $partner) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_active" value="0">
                    <input type="hidden" name="name" value="{{ $partner->name }}">
                    <input type="hidden" name="type" value="{{ $partner->type->value }}">
                    <input type="hidden" name="email" value="{{ $partner->email }}">
                    <button type="submit" class="btn btn-sm btn-light-danger w-100" onclick="return confirm('{{ __('partners.suspend_confirm') }}');">
                        <i class="ki-duotone ki-lock fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        {{ __('partners.suspend_account') }}
                    </button>
                </form>
                @else
                <form action="{{ route('admin.partners.update', $partner) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_active" value="1">
                    <input type="hidden" name="name" value="{{ $partner->name }}">
                    <input type="hidden" name="type" value="{{ $partner->type->value }}">
                    <input type="hidden" name="email" value="{{ $partner->email }}">
                    <button type="submit" class="btn btn-sm btn-light-success w-100">
                        <i class="ki-duotone ki-check-circle fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('partners.activate_account') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
        <!--end::Account Status Card-->

        <!--begin::Price List Card-->
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">{{ __('partners.dedicated_prices') }}</div>
                <div class="card-toolbar">
                    <a href="{{ route('admin.partners.edit', $partner) }}#prices" class="btn btn-sm btn-light">{{ __('partners.edit') }}</a>
                </div>
            </div>
            <div class="card-body py-4">
                @if ($partner->priceLists->isEmpty())
                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                    <i class="ki-duotone ki-information fs-2tx text-info me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-7 text-gray-700">{{ __('partners.no_custom_prices') }}</div>
                        </div>
                    </div>
                </div>
                @else
                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-4">
                    <i class="ki-duotone ki-information fs-2tx text-info me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-7 text-gray-700">{{ __('partners.custom_prices_configured', ['count' => $tours->whereIn('id', $partner->priceLists->pluck('tour_id')->unique())->count()]) }}</div>
                        </div>
                    </div>
                </div>
                @foreach ($tours->whereIn('id', $partner->priceLists->pluck('tour_id')->unique()) as $tour)
                <div class="mb-4">
                    <div class="fw-bold text-gray-800 mb-2">{{ $tour->name }} ({{ $tour->code }})</div>
                    @foreach (\App\Enums\Season::cases() as $season)
                        @if (isset($priceMatrix[$tour->id][$season->value]))
                        @foreach ($priceMatrix[$tour->id][$season->value] as $paxType => $price)
                        <div class="d-flex flex-stack fs-7 mb-1">
                            <span class="text-muted">{{ ucfirst($paxType) }} ({{ $season->label() }})</span>
                            <span class="fw-bold">{{ number_format($price, 2) }}</span>
                        </div>
                        @endforeach
                        @endif
                    @endforeach
                </div>
                @endforeach
                @endif
            </div>
        </div>
        <!--end::Price List Card-->
    </div>
    <!--end::Sidebar-->
</div>

<!--begin::Record Payment Modal-->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.accounting.payment') }}" method="POST">
                @csrf
                <input type="hidden" name="partner_id" value="{{ $partner->id }}">
                <div class="modal-header">
                    <h3 class="modal-title">{{ __('partners.record_payment') }}</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-5">
                        <label class="form-label fw-semibold">{{ __('partners.partner') }}</label>
                        <input type="text" class="form-control form-control-solid" value="{{ $partner->name }}" readonly>
                        @php
                            $outstandingBalance = $partner->outstanding_balance ?? 0;
                        @endphp
                        @if($outstandingBalance > 0)
                        <div class="form-text text-danger">{{ __('partners.outstanding_balance') }}: €{{ number_format($outstandingBalance, 2) }}</div>
                        @endif
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">{{ __('partners.amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">{{ __('partners.payment_method') }}</label>
                            <select name="method" class="form-select" required>
                                <option value="bank_transfer">{{ __('partners.bank_transfer') }}</option>
                                <option value="cash">{{ __('partners.cash') }}</option>
                                <option value="card">{{ __('partners.card') }}</option>
                                <option value="other">{{ __('partners.other') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">{{ __('partners.payment_date') }}</label>
                            <input type="date" name="paid_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('partners.reference') }}</label>
                            <input type="text" name="reference" class="form-control" placeholder="e.g. INV-001">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('partners.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('partners.payment_notes_placeholder') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('partners.cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('partners.record_payment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Record Payment Modal-->
@endsection
