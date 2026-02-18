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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 50)->comment('booking_confirmed, overbooking_requested, etc.');
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('subject');
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('success')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->useCurrent();

            $table->index('event_type');
            $table->index('to_email');
            $table->index('booking_id');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
