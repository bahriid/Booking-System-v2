<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        $this->configureMailFromDatabase();

        View::composer('layouts.admin', function ($view) {
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
                    'booking' => $booking,
                    'created_at' => $booking->created_at,
                ]);
            }

            foreach ($recentBookings as $booking) {
                $notifications->push([
                    'type' => 'new_booking',
                    'booking' => $booking,
                    'created_at' => $booking->created_at,
                ]);
            }

            foreach ($recentCancellations as $booking) {
                $notifications->push([
                    'type' => 'cancellation',
                    'booking' => $booking,
                    'created_at' => $booking->cancelled_at,
                ]);
            }

            $notifications = $notifications->sortByDesc('created_at')->take(10);

            $view->with('headerNotifications', $notifications);
            $view->with('headerNotificationCount', $notifications->count());
        });
    }

    /**
     * Override Laravel mail config with SMTP settings from database.
     */
    private function configureMailFromDatabase(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $emailSettings = Setting::getGroup('email');

            if (empty($emailSettings)) {
                return;
            }

            if (! empty($emailSettings['smtp_host'])) {
                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp.host', $emailSettings['smtp_host']);
                Config::set('mail.mailers.smtp.port', (int) ($emailSettings['smtp_port'] ?? 587));
                Config::set('mail.mailers.smtp.username', $emailSettings['smtp_username'] ?? null);
                Config::set('mail.mailers.smtp.password', $emailSettings['smtp_password'] ?? null);

                $port = (int) ($emailSettings['smtp_port'] ?? 587);
                if ($port === 465) {
                    Config::set('mail.mailers.smtp.scheme', 'smtps');
                }
            }

            if (! empty($emailSettings['from_email'])) {
                Config::set('mail.from.address', $emailSettings['from_email']);
                Config::set('mail.from.name', $emailSettings['from_name'] ?? config('app.name'));
            }

            if (! empty($emailSettings['admin_email'])) {
                Config::set('mail.admin_email', $emailSettings['admin_email']);
            }
        } catch (\Throwable) {
            // Silently fail if DB is not available (e.g. during migrations)
        }
    }
}
