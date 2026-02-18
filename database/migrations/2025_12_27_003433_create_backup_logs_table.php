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
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->boolean('success')->default(false);
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->comment('Size in bytes');
            $table->text('notes')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('ran_at')->useCurrent();

            $table->index('success');
            $table->index('ran_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
