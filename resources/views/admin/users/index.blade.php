@extends('layouts.admin')

@section('title', __('users.users'))
@section('page-title', __('users.users'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('users.users') }}</li>
@endsection

@section('toolbar-actions')
<a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
    <i class="ki-duotone ki-plus fs-5 me-1">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    {{ __('users.add_user') }}
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="text-muted fs-7">{{ __('users.manage_users') }}</span>
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

        @if($users->isEmpty())
            <div class="text-center py-10">
                <i class="ki-duotone ki-people text-muted fs-3x mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                </i>
                <p class="text-muted fs-6">{{ __('users.no_users_found') }}</p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary mt-3">
                    {{ __('users.add_user') }}
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>{{ __('users.user') }}</th>
                            <th>{{ __('users.role') }}</th>
                            <th>{{ __('users.created_at') }}</th>
                            <th>{{ __('users.status') }}</th>
                            <th class="text-end">{{ __('users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-3">
                                        <span class="symbol-label bg-light-{{ $user->role->color() }} text-{{ $user->role->color() }} fw-bold">
                                            {{ $user->initials }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">{{ $user->name }}</span>
                                        <div class="text-muted fs-7">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-{{ $user->role->color() }}">{{ $user->role->label() }}</span>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge badge-light-success">{{ __('users.active_status') }}</span>
                                @else
                                    <span class="badge badge-light-danger">{{ __('users.inactive_status') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-light btn-active-light-primary me-1">
                                    {{ __('users.edit') }}
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-light btn-active-light-{{ $user->is_active ? 'danger' : 'success' }}">
                                            {{ $user->is_active ? __('users.deactivate') : __('users.activate') }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-5">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
