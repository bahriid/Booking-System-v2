<?php

declare(strict_types=1);

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 30)->unique()->comment('Format: TOURCODE-NN-YYYYMMDD');
            $table->foreignId('partner_id')->constrained()->restrictOnDelete();
            $table->foreignId('tour_departure_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default(BookingStatus::CONFIRMED->value);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->string('payment_status')->default(PaymentStatus::UNPAID->value);
            $table->timestamp('suspended_until')->nullable()->comment('Overbooking expiry time');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('booking_code');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['partner_id', 'status']);
            $table->index(['tour_departure_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
