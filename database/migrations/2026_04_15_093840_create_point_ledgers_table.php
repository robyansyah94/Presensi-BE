<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('transaction_type', ['EARN', 'SPEND', 'PENALTY']);        // Jenis transaksi
            $table->unsignedInteger('amount');                                     // Selalu positif, arah ditentukan type
            $table->integer('current_balance');                                    // Saldo SETELAH transaksi (audit trail)
            $table->text('description')->nullable();                               // Cth: "Datang tepat waktu 12/08/2024"
            $table->string('reference_type', 50)->nullable();                      // 'presensi' atau 'token_redemption'
            $table->unsignedBigInteger('reference_id')->nullable();                // FK dinamis ke tabel referensi
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_ledgers');
    }
};