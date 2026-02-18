<?php

declare(strict_types=1);

use App\Enums\Season;
use App\Enums\TourDepartureStatus;
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
        Schema::create('tour_departures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->unsignedSmallInteger('capacity')->comment('Maximum seats available');
            $table->string('status')->default(TourDepartureStatus::OPEN->value);
            $table->string('season')->default(Season::MID->value);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tour_id', 'date', 'time']);
            $table->index('date');
            $table->index('status');
            $table->index(['date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_departures');
    }
};
