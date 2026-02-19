<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingSettingsRequest;
use App\Http\Requests\Admin\UpdateEmailSettingsRequest;
use App\Http\Requests\Admin\UpdateGeneralSettingsRequest;
use App\Http\Requests\Admin\UpdateLanguageSettingsRequest;
use App\Models\BackupLog;
use App\Models\PickupPoint;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Admin Settings Controller.
 * Manages application settings.
 */
final class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(): InertiaResponse
    {
        // Get all settings organized by group
        $generalSettings = Setting::getGroup('general');
        $bookingSettings = Setting::getGroup('booking');
        $emailSettings = Setting::getGroup('email');
        $languageSettings = Setting::getGroup('language');
        $voucherSettings = Setting::getGroup('voucher');

        // Get lookup data for dropdowns (convert associative arrays to indexed arrays for React)
        $timezones = array_keys(Setting::getTimezones());
        $currencies = array_keys(Setting::getCurrencies());
        $dateFormats = array_keys(Setting::getDateFormats());
        $languages = collect(Setting::getLanguages())->map(fn ($label, $code) => [
            'code' => $code,
            'label' => $label,
        ])->values()->all();

        // Get pickup points for tab
        $pickupPoints = PickupPoint::orderBy('default_time')->get();

        // Get admin/driver users for tab
        $users = User::whereIn('role', [UserRole::ADMIN, UserRole::DRIVER])
            ->orderBy('name')
            ->get();

        // Get recent backups for tab
        $backups = BackupLog::orderBy('ran_at', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('admin/settings', compact(
            'generalSettings',
            'bookingSettings',
            'emailSettings',
            'languageSettings',
            'voucherSettings',
            'timezones',
            'currencies',
            'dateFormats',
            'languages',
            'pickupPoints',
            'users',
            'backups'
        ));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(UpdateGeneralSettingsRequest $request): RedirectResponse
    {
        Setting::setMany([
            'company_name' => $request->validated('company_name'),
            'company_email' => $request->validated('company_email'),
            'company_phone' => $request->validated('company_phone'),
            'company_address' => $request->validated('company_address'),
            'timezone' => $request->validated('timezone'),
            'currency' => $request->validated('currency'),
            'date_format' => $request->validated('date_format'),
        ]);

        return redirect()
            ->route('admin.settings')
            ->with('success', __('general.settings_saved'))
            ->withFragment('tab_general');
    }

    /**
     * Update booking settings.
     */
    public function updateBooking(UpdateBookingSettingsRequest $request): RedirectResponse
    {
        Setting::setMany([
            'cutoff_hours' => $request->validated('cutoff_hours'),
            'overbooking_expiry_hours' => $request->validated('overbooking_expiry_hours'),
            'free_cancellation_hours' => $request->validated('free_cancellation_hours'),
            'late_cancellation_penalty' => $request->validated('late_cancellation_penalty'),
            'overbooking_enabled' => $request->boolean('overbooking_enabled'),
        ]);

        return redirect()
            ->route('admin.settings')
            ->with('success', __('general.settings_saved'))
            ->withFragment('tab_booking');
    }

    /**
     * Update email settings.
     */
    public function updateEmail(UpdateEmailSettingsRequest $request): RedirectResponse
    {
        Setting::setMany([
            'smtp_host' => $request->validated('smtp_host'),
            'smtp_port' => $request->validated('smtp_port'),
            'smtp_username' => $request->validated('smtp_username'),
            'from_name' => $request->validated('from_name'),
            'from_email' => $request->validated('from_email'),
            'admin_email' => $request->validated('admin_email'),
        ]);

        // Only update password if provided
        if ($request->filled('smtp_password')) {
            Setting::set('smtp_password', $request->validated('smtp_password'));
        }

        return redirect()
            ->route('admin.settings')
            ->with('success', __('general.settings_saved'))
            ->withFragment('tab_email');
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(\Illuminate\Http\Request $request): RedirectResponse
    {
        $notifications = [
            'booking_confirmed' => [
                'admin' => $request->boolean('booking_confirmed_admin'),
                'partner' => $request->boolean('booking_confirmed_partner'),
            ],
            'overbooking_requested' => [
                'admin' => $request->boolean('overbooking_requested_admin'),
                'partner' => $request->boolean('overbooking_requested_partner'),
            ],
            'overbooking_resolved' => [
                'admin' => $request->boolean('overbooking_resolved_admin'),
                'partner' => $request->boolean('overbooking_resolved_partner'),
            ],
            'booking_cancelled' => [
                'admin' => $request->boolean('booking_cancelled_admin'),
                'partner' => $request->boolean('booking_cancelled_partner'),
            ],
            'booking_modified' => [
                'admin' => $request->boolean('booking_modified_admin'),
                'partner' => $request->boolean('booking_modified_partner'),
            ],
            'tour_cancelled' => [
                'admin' => $request->boolean('tour_cancelled_admin'),
                'partner' => $request->boolean('tour_cancelled_partner'),
            ],
        ];

        Setting::set('notifications', $notifications);

        return redirect()
            ->route('admin.settings')
            ->with('success', __('general.settings_saved'))
            ->withFragment('tab_email');
    }

    /**
     * Update language settings.
     */
    public function updateLanguage(UpdateLanguageSettingsRequest $request): RedirectResponse
    {
        Setting::setMany([
            'default_language' => $request->validated('default_language'),
            'partner_language' => $request->validated('partner_language'),
        ]);

        return redirect()
            ->route('admin.settings')
            ->with('success', __('general.settings_saved'))
            ->withFragment('tab_language');
    }

    /**
     * Send a test email.
     */
    public function sendTestEmail(): RedirectResponse
    {
        try {
            // Apply latest SMTP settings from DB and purge cached mailer
            $this->applySmtpSettings();

            $adminEmail = Setting::get('admin_email', config('mail.admin_email'));

            Mail::raw('This is a test email from MagShip B2B Booking.', function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                    ->subject('MagShip - Test Email');
            });

            return redirect()
                ->route('admin.settings')
                ->with('success', "Test email sent to {$adminEmail}")
                ->withFragment('tab_email');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings')
                ->with('error', 'Failed to send test email: '.$e->getMessage())
                ->withFragment('tab_email');
        }
    }

    /**
     * Update voucher settings.
     */
    public function updateVoucher(\Illuminate\Http\Request $request): RedirectResponse
    {
        $request->validate([
            'voucher_header' => 'nullable|string|max:500',
            'voucher_notes' => 'nullable|string|max:2000',
            'voucher_footer' => 'nullable|string|max:500',
        ]);

        Setting::setMany([
            'voucher_header' => $request->input('voucher_header', ''),
            'voucher_notes' => $request->input('voucher_notes', ''),
            'voucher_footer' => $request->input('voucher_footer', ''),
        ]);

        return redirect()
            ->route('admin.settings')
            ->with('success', __('general.settings_saved'))
            ->withFragment('tab_voucher');
    }

    /**
     * Apply SMTP settings from database to mail config and purge cached mailer.
     */
    private function applySmtpSettings(): void
    {
        $emailSettings = Setting::getGroup('email');

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

        // Purge the cached SMTP mailer so it picks up the new config
        Mail::purge('smtp');
    }

    /**
     * Create a database backup manually.
     */
    public function createBackup(): RedirectResponse
    {
        try {
            Artisan::call('db:backup', ['--compress' => true]);

            return redirect()
                ->route('admin.settings')
                ->with('success', 'Backup created successfully.')
                ->withFragment('tab_backup');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings')
                ->with('error', 'Failed to create backup: '.$e->getMessage())
                ->withFragment('tab_backup');
        }
    }
}
