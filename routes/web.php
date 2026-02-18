<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BackupLogController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmailLogController;
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use App\Http\Controllers\Admin\TourDepartureController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PickupPointController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Driver\DashboardController as DriverDashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Partner\BookingController as PartnerBookingController;
use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cache Clear Route (TEMPORARY - Remove after use)
|--------------------------------------------------------------------------
*/
Route::get('/clear-cache-xyz123', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return 'Cache cleared successfully! Remove this route now.';
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->intended(match (auth()->user()->role->value) {
            'admin' => route('admin.dashboard'),
            'partner' => route('partner.dashboard'),
            'driver' => route('driver.dashboard'),
        });
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Password Reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Profile Routes (All Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('password');
    Route::put('/change-password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Language Switch Route
|--------------------------------------------------------------------------
*/
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Tours (Resource Controller)
        Route::resource('tours', AdminTourController::class);

        // Calendar & Departures
        Route::get('/calendar', [TourDepartureController::class, 'index'])->name('calendar');
        Route::get('/departures/events', [TourDepartureController::class, 'events'])->name('departures.events');
        Route::post('/departures/bulk', [TourDepartureController::class, 'bulkCreate'])->name('departures.bulk');
        Route::post('/departures/bulk-close', [TourDepartureController::class, 'bulkClose'])->name('departures.bulk-close');
        Route::post('/departures/{departure}/cancel', [TourDepartureController::class, 'cancel'])->name('departures.cancel');
        Route::resource('departures', TourDepartureController::class)->except(['index', 'create']);

        // Bookings
        Route::get('/bookings/export', [AdminBookingController::class, 'export'])->name('bookings.export');
        Route::get('/bookings/export-pdf', [AdminBookingController::class, 'exportPdf'])->name('bookings.export-pdf');
        Route::resource('bookings', AdminBookingController::class);
        Route::get('/tours/{tour}/departures', [AdminBookingController::class, 'getDepartures'])->name('tours.departures');
        Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::post('/bookings/{booking}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');

        // Partners (Resource Controller + Price List)
        Route::resource('partners', AdminPartnerController::class);
        Route::put('/partners/{partner}/prices', [AdminPartnerController::class, 'updatePrices'])->name('partners.prices.update');

        // Accounting
        Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
        Route::post('/accounting/payment', [AccountingController::class, 'storePayment'])->name('accounting.payment');
        Route::post('/accounting/credit', [AccountingController::class, 'storeCredit'])->name('accounting.credit');
        Route::post('/accounting/bulk-mark-paid', [AccountingController::class, 'bulkMarkPaid'])->name('accounting.bulk-mark-paid');
        Route::get('/accounting/export', [AccountingController::class, 'export'])->name('accounting.export');
        Route::get('/accounting/export-balances', [AccountingController::class, 'exportBalances'])->name('accounting.export-balances');

        // Reports
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general');
        Route::put('/settings/booking', [SettingsController::class, 'updateBooking'])->name('settings.booking');
        Route::put('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email');
        Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
        Route::put('/settings/language', [SettingsController::class, 'updateLanguage'])->name('settings.language');
        Route::put('/settings/voucher', [SettingsController::class, 'updateVoucher'])->name('settings.voucher');
        Route::post('/settings/test-email', [SettingsController::class, 'sendTestEmail'])->name('settings.test-email');
        Route::post('/settings/backup', [SettingsController::class, 'createBackup'])->name('settings.backup');

        // Users (Admin/Driver)
        Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
        Route::patch('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::patch('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');

        // Pickup Points
        Route::resource('pickup-points', PickupPointController::class)->except(['show']);
        Route::patch('/pickup-points/{pickup_point}/toggle-active', [PickupPointController::class, 'toggleActive'])->name('pickup-points.toggle-active');

        // Email Logs
        Route::get('/email-logs', [EmailLogController::class, 'index'])->name('email-logs.index');
        Route::get('/email-logs/{emailLog}', [EmailLogController::class, 'show'])->name('email-logs.show');

        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');

        // Backup Logs
        Route::get('/backup-logs', [BackupLogController::class, 'index'])->name('backup-logs.index');
        Route::post('/backup-logs/run', [BackupLogController::class, 'runBackup'])->name('backup-logs.run');
        Route::get('/backup-logs/{backupLog}/download', [BackupLogController::class, 'download'])->name('backup-logs.download');

        // PDF Downloads
        Route::get('/bookings/{booking}/voucher', [PdfController::class, 'bookingVoucher'])->name('bookings.voucher');
        Route::get('/bookings/{booking}/voucher/preview', [PdfController::class, 'bookingVoucherStream'])->name('bookings.voucher.preview');
        Route::get('/departures/{departure}/manifest', [PdfController::class, 'tourManifest'])->name('departures.manifest');
        Route::get('/departures/{departure}/manifest/preview', [PdfController::class, 'tourManifestStream'])->name('departures.manifest.preview');
    });

/*
|--------------------------------------------------------------------------
| Partner Routes
|--------------------------------------------------------------------------
*/
Route::prefix('partner')
    ->name('partner.')
    ->middleware(['auth', 'role:partner'])
    ->group(function () {
        // Dashboard
        Route::get('/', [PartnerDashboardController::class, 'index'])->name('dashboard');

        // Bookings
        Route::get('/bookings', [PartnerBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/create', [PartnerBookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [PartnerBookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{booking}', [PartnerBookingController::class, 'show'])->name('bookings.show');
        Route::get('/bookings/{booking}/edit', [PartnerBookingController::class, 'edit'])->name('bookings.edit');
        Route::put('/bookings/{booking}', [PartnerBookingController::class, 'update'])->name('bookings.update');
        Route::post('/bookings/{booking}/cancel', [PartnerBookingController::class, 'cancel'])->name('bookings.cancel');

        // AJAX endpoints for booking wizard
        Route::get('/tours/{tour}/departures', [PartnerBookingController::class, 'getDepartures'])->name('tours.departures');

        // PDF Downloads
        Route::get('/bookings/{booking}/voucher', [PdfController::class, 'bookingVoucher'])->name('bookings.voucher');
        Route::get('/bookings/{booking}/voucher/preview', [PdfController::class, 'bookingVoucherStream'])->name('bookings.voucher.preview');
    });

/*
|--------------------------------------------------------------------------
| Driver Routes
|--------------------------------------------------------------------------
*/
Route::prefix('driver')
    ->name('driver.')
    ->middleware(['auth', 'role:driver'])
    ->group(function () {
        // Dashboard with shifts
        Route::get('/', [DriverDashboardController::class, 'index'])->name('dashboard');

        // Manifest modal content (AJAX)
        Route::get('/departures/{departure}/manifest', [DriverDashboardController::class, 'manifest'])
            ->name('departures.manifest');

        // PDF Downloads
        Route::get('/departures/{departure}/manifest/pdf', [PdfController::class, 'tourManifest'])->name('departures.manifest.pdf');
        Route::get('/departures/{departure}/manifest/preview', [PdfController::class, 'tourManifestStream'])->name('departures.manifest.preview');
    });
