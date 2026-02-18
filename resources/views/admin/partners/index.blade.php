@extends('layouts.admin')

@section('title', __('partners.title'))
@section('page-title', __('partners.partner_management'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('partners.title') }}</li>
@endsection

@section('toolbar-actions')
<a href="{{ route('admin.partners.create') }}" class="btn btn-sm btn-primary">
    <i class="ki-duotone ki-plus fs-4 me-2">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    {{ __('partners.new_partner') }}
</a>
@endsection

@section('content')
@if (session('success'))
    <x-ui.alert type="success" :message="session('success')" dismissible class="mb-5" />
@endif

@if (session('error'))
    <x-ui.alert type="danger" :message="session('error')" dismissible class="mb-5" />
@endif

<!--begin::Partners Card-->
<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title">
            <!--begin::Search-->
            <form method="GET" action="{{ route('admin.partners.index') }}" class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid w-250px ps-13" placeholder="{{ __('partners.search_partner') }}" />
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
            </form>
            <!--end::Search-->
        </div>
        <!--end::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar">
            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end gap-3" data-kt-partner-table-toolbar="base">
                <form method="GET" action="{{ route('admin.partners.index') }}" class="d-flex gap-3">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select class="form-select form-select-solid w-140px" name="type" onchange="this.form.submit()">
                        <option value="">{{ __('partners.all_types') }}</option>
                        @foreach (\App\Enums\PartnerType::cases() as $type)
                            <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-solid w-140px" name="status" onchange="this.form.submit()">
                        <option value="">{{ __('partners.all_status') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('partners.active') }}</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>{{ __('partners.suspended') }}</option>
                    </select>
                    @if(request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('admin.partners.index') }}" class="btn btn-icon btn-light-danger" title="{{ __('partners.clear_filters') }}">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                    @endif
                </form>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body py-4">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_partners_table">
            <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-200px">{{ __('partners.partner') }}</th>
                    <th class="min-w-100px">{{ __('partners.type') }}</th>
                    <th class="min-w-150px">{{ __('partners.email') }}</th>
                    <th class="min-w-100px">{{ __('partners.bookings') }}</th>
                    <th class="min-w-100px">{{ __('partners.outstanding') }}</th>
                    <th class="min-w-80px">{{ __('partners.status') }}</th>
                    <th class="text-end min-w-100px">{{ __('partners.actions') }}</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @forelse ($partners as $partner)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-50px me-3">
                                <span class="symbol-label bg-light-{{ $partner->type->color() }}">
                                    <i class="ki-duotone ki-{{ $partner->type === \App\Enums\PartnerType::HOTEL ? 'building' : 'globe' }} fs-2x text-{{ $partner->type->color() }}">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.partners.show', $partner) }}" class="text-gray-800 text-hover-primary fw-bold">{{ $partner->name }}</a>
                                @if ($partner->vat_number)
                                    <span class="text-muted fs-7">{{ __('partners.vat_number') }}: {{ $partner->vat_number }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-light-{{ $partner->type->color() }}">{{ $partner->type->shortLabel() }}</span></td>
                    <td>{{ $partner->email }}</td>
                    <td>
                        <span class="fw-bold d-block">{{ $partner->bookings_count }}</span>
                        <span class="text-muted fs-7">{{ __('partners.this_month') }}</span>
                    </td>
                    <td>
                        @if ($partner->outstanding_balance > 0)
                            <span class="text-danger fw-bold">{{ number_format($partner->outstanding_balance, 2) }}</span>
                        @else
                            <span class="text-success fw-bold">{{ number_format(0, 2) }}</span>
                        @endif
                    </td>
                    <td>
                        @if ($partner->is_active)
                            <span class="badge badge-light-success">{{ __('partners.active') }}</span>
                        @else
                            <span class="badge badge-light-secondary">{{ __('partners.suspended') }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="#" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <i class="ki-duotone ki-dots-square fs-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ route('admin.partners.show', $partner) }}" class="menu-link px-3">{{ __('partners.view') }}</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="{{ route('admin.partners.edit', $partner) }}" class="menu-link px-3">{{ __('partners.edit') }}</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="{{ route('admin.partners.edit', $partner) }}#prices" class="menu-link px-3">{{ __('partners.price_list') }}</a>
                            </div>
                            @if ($partner->is_active)
                                <div class="separator my-2"></div>
                                <div class="menu-item px-3">
                                    <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('{{ __('partners.delete_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="menu-link px-3 w-100 border-0 bg-transparent text-start text-hover-danger">{{ __('partners.delete') }}</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-10">
                        <div class="text-muted">{{ __('partners.no_partners') }}</div>
                        <a href="{{ route('admin.partners.create') }}" class="btn btn-sm btn-primary mt-3">{{ __('partners.create_first_partner') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!--end::Table-->

        <!--begin::Pagination-->
        {{ $partners->withQueryString()->links() }}
        <!--end::Pagination-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Partners Card-->
@endsection

