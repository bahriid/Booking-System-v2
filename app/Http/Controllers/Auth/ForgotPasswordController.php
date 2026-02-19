<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles sending password reset links via email.
 */
final class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showForm(): Response
    {
        return Inertia::render('auth/forgot-password');
    }

    /**
     * Send a password reset link to the given email.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __('auth.reset_link_sent'));
        }

        return back()->withErrors([
            'email' => __('auth.reset_link_error'),
        ])->onlyInput('email');
    }
}
