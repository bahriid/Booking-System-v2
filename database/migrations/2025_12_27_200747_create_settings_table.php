<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index(); // general, booking, email, language
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->timestamps();
        });

        // Insert default settings
        $this->insertDefaults();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }

    /**
     * Insert default settings values.
     */
    private function insertDefaults(): void
    {
        $now = now();
        $settings = [
            // General Settings
            ['group' => 'general', 'key' => 'company_name', 'value' => 'MagShip B2B Booking', 'type' => 'string'],
            ['group' => 'general', 'key' => 'company_email', 'value' => 'info@magship.test', 'type' => 'string'],
            ['group' => 'general', 'key' => 'company_phone', 'value' => '+39 089 123 456', 'type' => 'string'],
            ['group' => 'general', 'key' => 'company_address', 'value' => "Via Marina Grande 12\n84011 Amalfi (SA)\nItaly", 'type' => 'string'],
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Europe/Rome', 'type' => 'string'],
            ['group' => 'general', 'key' => 'currency', 'value' => 'EUR', 'type' => 'string'],
            ['group' => 'general', 'key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string'],

            // Booking Rules
            ['group' => 'booking', 'key' => 'cutoff_hours', 'value' => '24', 'type' => 'integer'],
            ['group' => 'booking', 'key' => 'overbooking_expiry_hours', 'value' => '2', 'type' => 'integer'],
            ['group' => 'booking', 'key' => 'free_cancellation_hours', 'value' => '48', 'type' => 'integer'],
            ['group' => 'booking', 'key' => 'late_cancellation_penalty', 'value' => '100', 'type' => 'integer'],
            ['group' => 'booking', 'key' => 'overbooking_enabled', 'value' => '1', 'type' => 'boolean'],

            // Email Settings
            ['group' => 'email', 'key' => 'smtp_host', 'value' => 'smtp.mailtrap.io', 'type' => 'string'],
            ['group' => 'email', 'key' => 'smtp_port', 'value' => '587', 'type' => 'integer'],
            ['group' => 'email', 'key' => 'smtp_username', 'value' => '', 'type' => 'string'],
            ['group' => 'email', 'key' => 'smtp_password', 'value' => '', 'type' => 'string'],
            ['group' => 'email', 'key' => 'from_name', 'value' => 'MagShip Notifications', 'type' => 'string'],
            ['group' => 'email', 'key' => 'from_email', 'value' => 'noreply@magship.test', 'type' => 'string'],
            ['group' => 'email', 'key' => 'admin_email', 'value' => 'admin@magship.test', 'type' => 'string'],

            // Notification Toggles (stored as JSON)
            ['group' => 'email', 'key' => 'notifications', 'value' => json_encode([
                'booking_confirmed' => ['admin' => true, 'partner' => true],
                'overbooking_requested' => ['admin' => true, 'partner' => true],
                'overbooking_resolved' => ['admin' => true, 'partner' => true],
                'booking_cancelled' => ['admin' => true, 'partner' => true],
                'booking_modified' => ['admin' => true, 'partner' => true],
                'tour_cancelled' => ['admin' => true, 'partner' => true],
            ]), 'type' => 'json'],

            // Language Settings
            ['group' => 'language', 'key' => 'default_language', 'value' => 'en', 'type' => 'string'],
            ['group' => 'language', 'key' => 'partner_language', 'value' => 'default', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = $now;
            $setting['updated_at'] = $now;
            \Illuminate\Support\Facades\DB::table('settings')->insert($setting);
        }
    }
};
