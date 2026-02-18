@extends('layouts.admin')

@section('title', __('logs.backup_logs'))
@section('page-title', __('logs.database_backups'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('logs.backup_logs') }}</li>
@endsection

@section('toolbar-actions')
<form action="{{ route('admin.backup-logs.run') }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-sm btn-primary">
        <i class="ki-duotone ki-arrows-circle fs-5 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        {{ __('logs.run_backup_now') }}
    </button>
</form>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success d-flex align-items-center mb-5">
    <i class="ki-duotone ki-check-circle fs-2hx text-success me-3">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
    <div>{{ session('success') }}</div>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger d-flex align-items-center mb-5">
    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-3">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
    <div>{{ session('error') }}</div>
</div>
@endif

<!--begin::Stats-->
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-md-6 col-lg-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-primary">
                            <i class="ki-duotone ki-folder-down fs-2x text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-7 text-muted fw-semibold">{{ __('logs.total_backups') }}</div>
                    </div>
                </div>
                <div class="fs-2hx fw-bold text-gray-900">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-success">
                            <i class="ki-duotone ki-check-circle fs-2x text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-7 text-muted fw-semibold">{{ __('logs.successful') }}</div>
                    </div>
                </div>
                <div class="fs-2hx fw-bold text-success">{{ $stats['successful'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-danger">
                            <i class="ki-duotone ki-cross-circle fs-2x text-danger">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-7 text-muted fw-semibold">{{ __('logs.failed') }}</div>
                    </div>
                </div>
                <div class="fs-2hx fw-bold text-danger">{{ $stats['failed'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-info">
                            <i class="ki-duotone ki-time fs-2x text-info">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-7 text-muted fw-semibold">{{ __('logs.last_backup') }}</div>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-gray-900">
                    @if($stats['last_backup'])
                        {{ $stats['last_backup']->ran_at->diffForHumans() }}
                    @else
                        {{ __('logs.never') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Stats-->

<!--begin::Backup Logs Table-->
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <i class="ki-duotone ki-folder-down fs-2 text-primary me-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            {{ __('logs.backup_history') }}
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th class="min-w-150px">{{ __('logs.date_time') }}</th>
                        <th class="min-w-100px">{{ __('logs.status') }}</th>
                        <th class="min-w-150px">{{ __('logs.file_size') }}</th>
                        <th class="min-w-250px">{{ __('logs.notes') }}</th>
                        <th class="min-w-100px text-end">{{ __('logs.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backupLogs as $log)
                    <tr>
                        <td>
                            <span class="text-gray-800 fw-semibold">{{ $log->ran_at->format('M d, Y') }}</span>
                            <span class="text-muted fw-semibold d-block fs-7">{{ $log->ran_at->format('H:i:s') }}</span>
                        </td>
                        <td>
                            @if($log->success)
                                <span class="badge badge-light-success">
                                    <i class="ki-duotone ki-check fs-6 text-success me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('logs.success') }}
                                </span>
                            @else
                                <span class="badge badge-light-danger">
                                    <i class="ki-duotone ki-cross fs-6 text-danger me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('logs.failed') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($log->file_size)
                                <span class="text-gray-800 fw-semibold">{{ $log->formatted_file_size }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($log->success)
                                <span class="text-muted fs-7">{{ Str::limit($log->notes, 50) }}</span>
                            @else
                                <span class="text-danger fs-7">{{ Str::limit($log->error_message, 50) }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($log->success && $log->file_path && file_exists($log->file_path))
                                <a href="{{ route('admin.backup-logs.download', $log) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="{{ __('logs.download') }}">
                                    <i class="ki-duotone ki-file-down fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-10">
                            <i class="ki-duotone ki-folder-down fs-2x text-gray-300 mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="fs-5">{{ __('logs.no_backups_found') }}</div>
                            <div class="fs-7 text-muted">{{ __('logs.run_first_backup') }}</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($backupLogs->hasPages())
        <div class="d-flex justify-content-end mt-5">
            {{ $backupLogs->links() }}
        </div>
        @endif
    </div>
</div>
<!--end::Backup Logs Table-->

<div class="card mt-5">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <i class="ki-duotone ki-information-5 fs-2x text-primary me-3">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            <div>
                <h4 class="mb-1">{{ __('logs.automatic_backups') }}</h4>
                <p class="text-muted mb-0">
                    {{ __('logs.automatic_backups_description') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
