<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure the authenticated user has one of the required roles.
 *
 * Usage in routes:
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,partner')
 */
final class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     * @param string ...$roles Allowed role values (e.g., 'admin', 'partner', 'driver')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Convert string roles to enum values and check
        $allowedRoles = array_map(
            fn (string $role) => UserRole::tryFrom($role),
            $roles
        );

        if (!in_array($user->role, $allowedRoles, true)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
