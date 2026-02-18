<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();
        $settings = [
            ['group' => 'voucher', 'key' => 'voucher_header', 'value' => '', 'type' => 'string'],
            ['group' => 'voucher', 'key' => 'voucher_notes', 'value' => '', 'type' => 'string'],
            ['group' => 'voucher', 'key' => 'voucher_footer', 'value' => '', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            // Only insert if not exists
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();
            if (!$exists) {
                $setting['created_at'] = $now;
                $setting['updated_at'] = $now;
                DB::table('settings')->insert($setting);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'voucher_header',
            'voucher_notes',
            'voucher_footer',
        ])->delete();
    }
};
