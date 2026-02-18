<?php

declare(strict_types=1);

use App\Enums\PaxType;
use App\Enums\Season;
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
        Schema::create('partner_price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->string('season')->default(Season::MID->value);
            $table->string('pax_type')->default(PaxType::ADULT->value);
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['partner_id', 'tour_id', 'season', 'pax_type'], 'partner_tour_season_pax_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_price_lists');
    }
};
