<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Supported locales.
     */
    protected array $supportedLocales = ['en', 'it'];

    /**
     * Switch the application language.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, $this->supportedLocales)) {
            $locale = 'en';
        }

        // Store in session
        session(['locale' => $locale]);

        // Update user preference if logged in
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        return redirect()->back();
    }
}
