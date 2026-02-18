@extends('layouts.admin')

@section('title', __('pickup_points.pickup_points'))
@section('page-title', __('pickup_points.pickup_points'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('pickup_points.pickup_points') }}</li>
@endsection

@section('toolbar-actions')
<a href="{{ route('admin.pickup-points.create') }}" class="btn btn-sm btn-primary">
    <i class="ki-duotone ki-plus fs-5 me-1">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    {{ __('pickup_points.add_pickup_point') }}
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="text-muted fs-7">{{ __('pickup_points.manage_pickup_points') }}</span>
        </div>
    </div>
    <div class="card-body py-4">
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center p-5 mb-5">
                <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center p-5 mb-5">
                <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($pickupPoints->isEmpty())
            <div class="text-center py-10">
                <i class="ki-duotone ki-geolocation text-muted fs-3x mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <p class="text-muted fs-6">{{ __('pickup_points.no_pickup_points_found') }}</p>
                <a href="{{ route('admin.pickup-points.create') }}" class="btn btn-primary mt-3">
                    {{ __('pickup_points.add_pickup_point') }}
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">{{ __('pickup_points.order') }}</th>
                            <th>{{ __('pickup_points.name') }}</th>
                            <th>{{ __('pickup_points.location') }}</th>
                            <th>{{ __('pickup_points.default_time') }}</th>
                            <th>{{ __('pickup_points.status') }}</th>
                            <th class="text-end">{{ __('pickup_points.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @foreach($pickupPoints as $pickupPoint)
                        <tr>
                            <td>
                                <span class="badge badge-light-primary">{{ $pickupPoint->sort_order }}</span>
                            </td>
                            <td class="fw-bold">{{ $pickupPoint->name }}</td>
                            <td>{{ $pickupPoint->location ?? '-' }}</td>
                            <td>
                                @if($pickupPoint->default_time)
                                    {{ \Carbon\Carbon::parse($pickupPoint->default_time)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($pickupPoint->is_active)
                                    <span class="badge badge-light-success">{{ __('pickup_points.active') }}</span>
                                @else
                                    <span class="badge badge-light-danger">{{ __('pickup_points.inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.pickup-points.edit', $pickupPoint) }}" class="btn btn-sm btn-icon btn-light btn-active-light-primary me-1" title="{{ __('pickup_points.edit') }}">
                                    <i class="ki-duotone ki-pencil fs-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </a>
                                <form action="{{ route('admin.pickup-points.toggle-active', $pickupPoint) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-icon btn-light btn-active-light-{{ $pickupPoint->is_active ? 'danger' : 'success' }} me-1"
                                            title="{{ $pickupPoint->is_active ? __('pickup_points.deactivate') : __('pickup_points.activate') }}">
                                        <i class="ki-duotone ki-{{ $pickupPoint->is_active ? 'cross' : 'check' }} fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.pickup-points.destroy', $pickupPoint) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ __('pickup_points.confirm_delete') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-light btn-active-light-danger" title="{{ __('pickup_points.delete') }}">
                                        <i class="ki-duotone ki-trash fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-5">
                {{ $pickupPoints->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
