@extends('layouts.admin')

@section('title', __('tours.title'))
@section('page-title', __('tours.tour_management'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('tours.breadcrumb_tours') }}</li>
@endsection

@section('toolbar-actions')
<a href="{{ route('admin.tours.create') }}" class="btn btn-sm btn-primary">
    <i class="ki-duotone ki-plus fs-4 me-2">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    {{ __('tours.new_tour_button') }}
</a>
@endsection

@section('content')
{{-- Flash Messages --}}
@if(session('success'))
<x-ui.alert type="success" dismissible>{{ session('success') }}</x-ui.alert>
@endif
@if(session('error'))
<x-ui.alert type="danger" dismissible>{{ session('error') }}</x-ui.alert>
@endif

<!--begin::Tours Card-->
<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title">
            <!--begin::Search-->
            <form method="GET" action="{{ route('admin.tours.index') }}" class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid w-250px ps-13" placeholder="{{ __('tours.search_tour') }}" />
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
            <div class="d-flex justify-content-end gap-3" data-kt-tour-table-toolbar="base">
                <form method="GET" action="{{ route('admin.tours.index') }}" class="d-flex gap-3">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select class="form-select form-select-solid w-140px" name="status" onchange="this.form.submit()">
                        <option value="">{{ __('tours.all_status') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('tours.active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('tours.inactive') }}</option>
                    </select>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.tours.index') }}" class="btn btn-icon btn-light-danger" title="{{ __('tours.clear_filters') }}">
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
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_tours_table">
            <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-100px">{{ __('tours.code') }}</th>
                    <th class="min-w-200px">{{ __('tours.tour_name') }}</th>
                    <th class="min-w-100px">{{ __('tours.capacity') }}</th>
                    <th class="min-w-125px">{{ __('tours.season') }}</th>
                    <th class="min-w-80px">{{ __('tours.cutoff') }}</th>
                    <th class="min-w-100px">{{ __('tours.status') }}</th>
                    <th class="text-end min-w-100px">{{ __('tours.actions') }}</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @forelse($tours as $tour)
                <tr data-status="{{ $tour->is_active ? 'active' : 'inactive' }}">
                    <td>
                        <span class="badge badge-light-primary fs-7 fw-bold">{{ $tour->code }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-40px me-3">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-map fs-2 text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.tours.edit', $tour) }}" class="text-gray-800 text-hover-primary fw-bold">{{ $tour->name }}</a>
                                @if($tour->description)
                                <span class="text-muted fs-7">{{ Str::limit($tour->description, 40) }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-light-primary">{{ $tour->default_capacity }} pax</span>
                    </td>
                    <td>
                        <span class="badge badge-light-success">{{ $tour->seasonality_range }}</span>
                    </td>
                    <td>{{ $tour->cutoff_hours }}h</td>
                    <td>
                        @if($tour->is_active)
                        <span class="badge badge-light-success">{{ __('tours.active') }}</span>
                        @else
                        <span class="badge badge-light-secondary">{{ __('tours.inactive') }}</span>
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
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ route('admin.tours.edit', $tour) }}" class="menu-link px-3">{{ __('tours.edit') }}</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="{{ route('admin.calendar') }}?tour={{ $tour->id }}" class="menu-link px-3">{{ __('tours.calendar') }}</a>
                            </div>
                            <div class="menu-item px-3">
                                <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" onsubmit="return confirm('{{ __('tours.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="menu-link px-3 bg-transparent border-0 w-100 text-start text-hover-danger">{{ __('tours.delete') }}</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-10">
                        <div class="text-muted">{{ __('tours.no_tours') }}</div>
                        <a href="{{ route('admin.tours.create') }}" class="btn btn-sm btn-primary mt-3">{{ __('tours.create_first_tour') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!--end::Table-->

        <!--begin::Pagination-->
        {{ $tours->withQueryString()->links() }}
        <!--end::Pagination-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Tours Card-->
@endsection

