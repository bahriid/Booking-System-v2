<?php

declare(strict_types=1);

use App\Enums\PaxType;
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
        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pickup_point_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('pax_type')->default(PaxType::ADULT->value);
            $table->string('phone')->nullable();
            $table->text('allergies')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('price', 10, 2)->default(0)->comment('Price for this passenger');
            $table->timestamps();

            $table->index('pax_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_passengers');
    }
};
