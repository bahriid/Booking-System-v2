<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
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
        $this->configureMailFromDatabase();
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
