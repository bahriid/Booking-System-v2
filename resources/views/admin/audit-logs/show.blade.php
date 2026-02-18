@extends('layouts.admin')

@section('title', __('logs.audit_log_details'))
@section('page-title', __('logs.audit_log_details'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('admin.audit-logs.index') }}" class="text-muted text-hover-primary">{{ __('logs.audit_logs') }}</a>
</li>
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('logs.details') }}</li>
@endsection

@section('content')
<div class="row g-5 g-xl-8">
    <div class="col-xl-8">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header">
                <h3 class="card-title">{{ __('logs.event_information') }}</h3>
            </div>
            <div class="card-body">
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.action') }}</label>
                    <div class="col-lg-8">
                        @php
                            $actionBadge = match($auditLog->action) {
                                'created' => 'badge-light-success',
                                'updated' => 'badge-light-warning',
                                'deleted' => 'badge-light-danger',
                                'restored' => 'badge-light-info',
                                default => 'badge-light',
                            };
                            $actionLabels = [
                                'created' => __('logs.action_created'),
                                'updated' => __('logs.action_updated'),
                                'deleted' => __('logs.action_deleted'),
                                'restored' => __('logs.action_restored'),
                            ];
                        @endphp
                        <span class="badge {{ $actionBadge }} fs-6">{{ $actionLabels[$auditLog->action] ?? ucfirst($auditLog->action) }}</span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.date_time') }}</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $auditLog->created_at->format('l, F d, Y \a\t H:i:s') }}</span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.user') }}</label>
                    <div class="col-lg-8">
                        @if($auditLog->user)
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-35px me-3">
                                    <span class="symbol-label bg-light-primary text-primary fw-bold">
                                        {{ strtoupper(substr($auditLog->user->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-bold text-gray-800">{{ $auditLog->user->name }}</span>
                                    <span class="text-muted d-block fs-7">{{ $auditLog->user->email }}</span>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">{{ __('logs.system') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.entity_type') }}</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ class_basename($auditLog->entity_type) }}</span>
                        <span class="text-muted d-block fs-7">{{ $auditLog->entity_type }}</span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.entity_id') }}</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">#{{ $auditLog->entity_id ?? __('logs.na') }}</span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.ip_address') }}</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $auditLog->ip_address ?? __('logs.na') }}</span>
                    </div>
                </div>

                @if($auditLog->user_agent)
                <div class="row">
                    <label class="col-lg-4 fw-semibold text-muted">{{ __('logs.user_agent') }}</label>
                    <div class="col-lg-8">
                        <span class="text-muted fs-7">{{ Str::limit($auditLog->user_agent, 100) }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($auditLog->action === 'updated' && $auditLog->old_values && $auditLog->new_values)
        <div class="card mb-5 mb-xl-8">
            <div class="card-header">
                <h3 class="card-title">{{ __('logs.changes') }}</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-5 gy-4 mb-0">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-5">{{ __('logs.field') }}</th>
                                <th>{{ __('logs.old_value') }}</th>
                                <th>{{ __('logs.new_value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLog->new_values as $field => $newValue)
                            <tr>
                                <td class="ps-5">
                                    <span class="fw-semibold text-gray-800">{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                                </td>
                                <td>
                                    <code class="text-danger bg-light-danger px-2 py-1 rounded">
                                        {{ is_array($auditLog->old_values[$field] ?? null) ? json_encode($auditLog->old_values[$field]) : ($auditLog->old_values[$field] ?? 'null') }}
                                    </code>
                                </td>
                                <td>
                                    <code class="text-success bg-light-success px-2 py-1 rounded">
                                        {{ is_array($newValue) ? json_encode($newValue) : $newValue }}
                                    </code>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if($auditLog->action === 'created' && $auditLog->new_values)
        <div class="card mb-5 mb-xl-8">
            <div class="card-header">
                <h3 class="card-title">{{ __('logs.created_values') }}</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-5 gy-4 mb-0">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-5">{{ __('logs.field') }}</th>
                                <th>{{ __('logs.value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLog->new_values as $field => $value)
                            <tr>
                                <td class="ps-5">
                                    <span class="fw-semibold text-gray-800">{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                                </td>
                                <td>
                                    <code class="text-gray-800 bg-light px-2 py-1 rounded">
                                        {{ is_array($value) ? json_encode($value) : ($value ?? 'null') }}
                                    </code>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if($auditLog->action === 'deleted' && $auditLog->old_values)
        <div class="card mb-5 mb-xl-8">
            <div class="card-header">
                <h3 class="card-title">{{ __('logs.deleted_values') }}</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-5 gy-4 mb-0">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-5">{{ __('logs.field') }}</th>
                                <th>{{ __('logs.value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLog->old_values as $field => $value)
                            <tr>
                                <td class="ps-5">
                                    <span class="fw-semibold text-gray-800">{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                                </td>
                                <td>
                                    <code class="text-danger bg-light-danger px-2 py-1 rounded">
                                        {{ is_array($value) ? json_encode($value) : ($value ?? 'null') }}
                                    </code>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-xl-4">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header">
                <h3 class="card-title">{{ __('logs.actions') }}</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-light-primary w-100">
                    <i class="ki-duotone ki-arrow-left fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ __('logs.back_to_audit_logs') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
