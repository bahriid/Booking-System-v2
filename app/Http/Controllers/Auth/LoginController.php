<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles user authentication (login/logout).
 */
final class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): Response
    {
        return Inertia::render('auth/login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact support.',
                ])->onlyInput('email');
            }

            // Redirect based on role
            return redirect()->intended($this->redirectPath($user->role));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Get the redirect path based on user role.
     */
    private function redirectPath(UserRole $role): string
    {
        return match ($role) {
            UserRole::ADMIN => route('admin.dashboard'),
            UserRole::PARTNER => route('partner.dashboard'),
            UserRole::DRIVER => route('driver.dashboard'),
        };
    }
}
