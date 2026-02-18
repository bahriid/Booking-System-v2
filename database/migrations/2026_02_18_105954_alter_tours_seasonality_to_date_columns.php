<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $year = now()->year;

        // Step 1: Add temporary date columns
        Schema::table('tours', function (Blueprint $table) {
            $table->date('seasonality_start_date')->nullable()->after('seasonality_start');
            $table->date('seasonality_end_date')->nullable()->after('seasonality_end');
        });

        // Step 2: Convert month integers to dates
        DB::table('tours')->get()->each(function ($tour) use ($year) {
            DB::table('tours')->where('id', $tour->id)->update([
                'seasonality_start_date' => sprintf('%d-%02d-01', $year, $tour->seasonality_start),
                'seasonality_end_date' => sprintf('%d-%02d-01', $year, $tour->seasonality_end),
            ]);
        });

        // Step 3: Drop old columns and rename new ones
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['seasonality_start', 'seasonality_end']);
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->renameColumn('seasonality_start_date', 'seasonality_start');
            $table->renameColumn('seasonality_end_date', 'seasonality_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add temporary integer columns
        Schema::table('tours', function (Blueprint $table) {
            $table->unsignedTinyInteger('seasonality_start_month')->default(4)->after('seasonality_start');
            $table->unsignedTinyInteger('seasonality_end_month')->default(10)->after('seasonality_end');
        });

        // Step 2: Extract month from dates
        DB::table('tours')->get()->each(function ($tour) {
            DB::table('tours')->where('id', $tour->id)->update([
                'seasonality_start_month' => date('n', strtotime($tour->seasonality_start)),
                'seasonality_end_month' => date('n', strtotime($tour->seasonality_end)),
            ]);
        });

        // Step 3: Drop date columns and rename integer ones
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['seasonality_start', 'seasonality_end']);
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->renameColumn('seasonality_start_month', 'seasonality_start');
            $table->renameColumn('seasonality_end_month', 'seasonality_end');
        });
    }
};
