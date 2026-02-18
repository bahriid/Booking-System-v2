<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales.
     */
    protected array $supportedLocales = ['en', 'it'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);

        if (in_array($locale, $this->supportedLocales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Determine the locale from various sources.
     */
    protected function determineLocale(Request $request): string
    {
        // 1. Check URL parameter (for switching)
        if ($request->has('lang') && in_array($request->get('lang'), $this->supportedLocales)) {
            $locale = $request->get('lang');
            session(['locale' => $locale]);

            // Update user preference if logged in
            if (Auth::check() && Auth::user()->locale !== $locale) {
                Auth::user()->update(['locale' => $locale]);
            }

            return $locale;
        }

        // 2. Check authenticated user's preference
        if (Auth::check() && Auth::user()->locale) {
            return Auth::user()->locale;
        }

        // 3. Check session
        if (session()->has('locale')) {
            return session('locale');
        }

        // 4. Check browser Accept-Language header
        $browserLocale = $request->getPreferredLanguage($this->supportedLocales);
        if ($browserLocale) {
            return $browserLocale;
        }

        // 5. Default to app locale
        return config('app.locale', 'en');
    }
}
