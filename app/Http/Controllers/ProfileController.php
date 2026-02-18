<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Handles user profile operations including password change.
 */
final class ProfileController extends Controller
{
    /**
     * Show the password change form.
     */
    public function showChangePasswordForm(): View
    {
        return view('profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ], [
            'current_password.required' => __('profile.current_password_required'),
            'password.required' => __('profile.new_password_required'),
            'password.confirmed' => __('profile.password_confirmation_mismatch'),
        ]);

        $user = $request->user();

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => __('profile.current_password_incorrect'),
            ]);
        }

        // Update password
        $user->update([
            'password' => $validated['password'],
        ]);

        return back()->with('success', __('profile.password_changed_successfully'));
    }
}
