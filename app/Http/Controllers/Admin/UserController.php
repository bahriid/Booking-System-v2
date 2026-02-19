<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Admin User CRUD Controller.
 * Manages admin and driver user accounts.
 */
final class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): InertiaResponse
    {
        $users = User::query()
            ->whereIn('role', [UserRole::ADMIN, UserRole::DRIVER])
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('admin/users/index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): InertiaResponse
    {
        $roles = [
            ['value' => UserRole::ADMIN->value, 'label' => UserRole::ADMIN->label()],
            ['value' => UserRole::DRIVER->value, 'label' => UserRole::DRIVER->label()],
        ];

        return Inertia::render('admin/users/create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => $request->validated('role'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User '{$user->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): InertiaResponse
    {
        // Only allow editing admin and driver users
        if (!in_array($user->role, [UserRole::ADMIN, UserRole::DRIVER])) {
            abort(403, 'Cannot edit partner users from this page.');
        }

        $roles = [
            ['value' => UserRole::ADMIN->value, 'label' => UserRole::ADMIN->label()],
            ['value' => UserRole::DRIVER->value, 'label' => UserRole::DRIVER->label()],
        ];

        return Inertia::render('admin/users/edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // Only allow updating admin and driver users
        if (!in_array($user->role, [UserRole::ADMIN, UserRole::DRIVER])) {
            abort(403, 'Cannot edit partner users from this page.');
        }

        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            'is_active' => $request->boolean('is_active'),
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user): RedirectResponse
    {
        // Prevent deactivating own account
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot deactivate your own account.');
        }

        // Only allow toggling admin and driver users
        if (!in_array($user->role, [UserRole::ADMIN, UserRole::DRIVER])) {
            abort(403, 'Cannot modify partner users from this page.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User '{$user->name}' has been {$status}.");
    }

    /**
     * Reset user password and send notification.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        // Only allow resetting admin and driver users
        if (!in_array($user->role, [UserRole::ADMIN, UserRole::DRIVER])) {
            abort(403, 'Cannot modify partner users from this page.');
        }

        // Generate a random password
        $newPassword = bin2hex(random_bytes(8));

        $user->update([
            'password' => $newPassword,
        ]);

        // In a real application, you would send this via email
        // For now, we'll show it in a flash message (not recommended for production)
        return redirect()
            ->route('admin.users.index')
            ->with('success', "Password reset for '{$user->name}'. New password: {$newPassword}");
    }
}
