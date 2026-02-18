<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects authenticated users away from guest-only pages (like login).
 */
final class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Redirect based on role
                return redirect($this->redirectPath($user->role));
            }
        }

        return $next($request);
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
