@extends('layouts.admin')

@section('title', __('logs.audit_logs'))
@section('page-title', __('logs.audit_logs'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('logs.audit_logs') }}</li>
@endsection

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-shield-tick fs-3 position-absolute ms-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <span class="ps-13 fs-5 fw-semibold text-gray-700">{{ __('logs.activity_history') }}</span>
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-light-primary" data-bs-toggle="collapse" data-bs-target="#filters">
                <i class="ki-duotone ki-filter fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ __('logs.filters') }}
            </button>
        </div>
    </div>

    <div class="collapse {{ request()->hasAny(['action', 'entity_type', 'user_id', 'date_from', 'date_to']) ? 'show' : '' }}" id="filters">
        <div class="card-body border-top pt-6">
            <form action="{{ route('admin.audit-logs.index') }}" method="GET">
                <div class="row g-4">
                    <div class="col-md-2">
                        <label class="form-label">{{ __('logs.action') }}</label>
                        <select name="action" class="form-select form-select-solid">
                            <option value="">{{ __('logs.all_actions') }}</option>
                            @foreach($actions as $value => $label)
                                <option value="{{ $value }}" {{ request('action') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('logs.entity_type') }}</label>
                        <select name="entity_type" class="form-select form-select-solid">
                            <option value="">{{ __('logs.all_types') }}</option>
                            @foreach($entityTypes as $type)
                                <option value="{{ $type['label'] }}" {{ request('entity_type') === $type['label'] ? 'selected' : '' }}>{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('logs.from_date') }}</label>
                        <input type="date" name="date_from" class="form-control form-control-solid" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('logs.to_date') }}</label>
                        <input type="date" name="date_to" class="form-control form-control-solid" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">{{ __('logs.filter') }}</button>
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-light">{{ __('logs.clear') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th class="min-w-120px">{{ __('logs.date_time') }}</th>
                        <th class="min-w-100px">{{ __('logs.user') }}</th>
                        <th class="min-w-80px">{{ __('logs.action') }}</th>
                        <th class="min-w-120px">{{ __('logs.entity') }}</th>
                        <th class="min-w-200px">{{ __('logs.changes') }}</th>
                        <th class="min-w-80px text-end">{{ __('logs.details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                    <tr>
                        <td>
                            <span class="text-gray-800 fw-semibold">{{ $log->created_at->format('M d, Y') }}</span>
                            <span class="text-muted fw-semibold d-block fs-7">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        <td>
                            @if($log->user)
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-30px me-3">
                                        <span class="symbol-label bg-light-primary text-primary fw-bold fs-7">
                                            {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <span class="text-gray-800 fw-semibold">{{ $log->user->name }}</span>
                                </div>
                            @else
                                <span class="text-muted">{{ __('logs.system') }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $actionBadge = match($log->action) {
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
                            <span class="badge {{ $actionBadge }}">{{ $actionLabels[$log->action] ?? ucfirst($log->action) }}</span>
                        </td>
                        <td>
                            <span class="text-gray-800 fw-semibold">{{ class_basename($log->entity_type) }}</span>
                            @if($log->entity_id)
                                <span class="text-muted fw-semibold d-block fs-7">#{{ $log->entity_id }}</span>
                            @endif
                        </td>
                        <td>
                            @if($log->action === 'updated' && $log->new_values)
                                @php
                                    $changedFields = array_keys($log->new_values);
                                    $displayFields = array_slice($changedFields, 0, 3);
                                    $moreCount = count($changedFields) - 3;
                                @endphp
                                <span class="text-muted fs-7">
                                    {{ __('logs.changed') }} {{ implode(', ', $displayFields) }}
                                    @if($moreCount > 0)
                                        <span class="badge badge-light">+{{ $moreCount }} {{ __('logs.more') }}</span>
                                    @endif
                                </span>
                            @elseif($log->action === 'created')
                                <span class="text-muted fs-7">{{ __('logs.new_record_created') }}</span>
                            @elseif($log->action === 'deleted')
                                <span class="text-muted fs-7">{{ __('logs.record_deleted') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.audit-logs.show', $log) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                <i class="ki-duotone ki-eye fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-10">
                            <i class="ki-duotone ki-shield-tick fs-2x text-gray-300 mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="fs-5">{{ __('logs.no_audit_logs') }}</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($auditLogs->hasPages())
        <div class="d-flex justify-content-end mt-5">
            {{ $auditLogs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
