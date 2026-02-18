<?php

declare(strict_types=1);

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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Tour code e.g. POSAMCL');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('seasonality_start')->default(4)->comment('Start month (1-12)');
            $table->unsignedTinyInteger('seasonality_end')->default(10)->comment('End month (1-12)');
            $table->unsignedSmallInteger('cutoff_hours')->default(24)->comment('Booking cutoff hours before departure');
            $table->unsignedSmallInteger('default_capacity')->default(50)->comment('Default seats per departure');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
