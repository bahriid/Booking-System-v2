<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            'auth' => fn () => $this->getAuthData($request),

            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
            ],

            'locale' => fn () => app()->getLocale(),

            'translations' => fn () => $this->getTranslations(),

            'notifications' => fn () => $this->getNotifications($request),
        ];
    }

    /**
     * Get the authenticated user data.
     *
     * @return array<string, mixed>|null
     */
    private function getAuthData(Request $request): ?array
    {
        $user = $request->user();

        if (! $user) {
            return null;
        }

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'role_label' => $user->role->label(),
                'initials' => $user->initials,
                'partner_id' => $user->partner_id,
                'locale' => $user->locale,
            ],
        ];
    }

    /**
     * Get all translation strings for the current locale.
     *
     * @return array<string, array<string, string>>
     */
    private function getTranslations(): array
    {
        $locale = app()->getLocale();
        $langPath = lang_path($locale);
        $translations = [];

        if (is_dir($langPath)) {
            foreach (glob($langPath . '/*.php') as $file) {
                $key = basename($file, '.php');
                $translations[$key] = require $file;
            }
        }

        return $translations;
    }

    /**
     * Get admin notifications (overbooking requests, new bookings, cancellations).
     *
     * @return array<string, mixed>|null
     */
    private function getNotifications(Request $request): ?array
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::ADMIN) {
            return null;
        }

        $overbookingRequests = Booking::with(['partner', 'passengers'])
            ->where('status', BookingStatus::SUSPENDED_REQUEST)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentBookings = Booking::with(['partner', 'passengers'])
            ->where('status', BookingStatus::CONFIRMED)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentCancellations = Booking::with(['partner', 'passengers'])
            ->where('status', BookingStatus::CANCELLED)
            ->where('cancelled_at', '>=', now()->subHours(24))
            ->orderBy('cancelled_at', 'desc')
            ->limit(10)
            ->get();

        $notifications = collect();

        foreach ($overbookingRequests as $booking) {
            $notifications->push([
                'type' => 'overbooking',
                'booking' => [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'partner_name' => $booking->partner?->company_name,
                    'passenger_count' => $booking->passengers->count(),
                ],
                'created_at' => $booking->created_at?->toISOString(),
            ]);
        }

        foreach ($recentBookings as $booking) {
            $notifications->push([
                'type' => 'new_booking',
                'booking' => [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'partner_name' => $booking->partner?->company_name,
                    'passenger_count' => $booking->passengers->count(),
                ],
                'created_at' => $booking->created_at?->toISOString(),
            ]);
        }

        foreach ($recentCancellations as $booking) {
            $notifications->push([
                'type' => 'cancellation',
                'booking' => [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'partner_name' => $booking->partner?->company_name,
                    'passenger_count' => $booking->passengers->count(),
                ],
                'created_at' => $booking->cancelled_at?->toISOString(),
            ]);
        }

        $sorted = $notifications->sortByDesc('created_at')->take(10)->values()->all();

        return [
            'items' => $sorted,
            'count' => count($sorted),
        ];
    }
}
