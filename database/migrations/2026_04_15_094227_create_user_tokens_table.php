<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('flexibility_items')->cascadeOnDelete();
            $table->enum('status', ['AVAILABLE', 'USED', 'EXPIRED'])->default('AVAILABLE');
            $table->foreignId('used_at_attendance_id')
                ->nullable()
                ->constrained('presensi')
                ->nullOnDelete();                                                  // Relasi saat token dipakai
            $table->timestamp('expires_at')->nullable();                           // Token bisa punya masa berlaku
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tokens');
    }
};
