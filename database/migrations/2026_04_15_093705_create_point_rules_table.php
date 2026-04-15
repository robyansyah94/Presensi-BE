<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');                                          // Cth: "Datang Pagi Banget"
            $table->string('target_role')->default('karyawan');                   // karyawan / admin / all
            $table->enum('condition_type', ['jam_masuk', 'menit_terlambat']);     // Jenis kondisi yang dievaluasi
            $table->enum('condition_operator', ['<', '>', 'BETWEEN']);            // Operator perbandingan
            $table->string('condition_value', 20);                                // Cth: "06:30:00" atau "15"
            $table->string('condition_value_max', 20)->nullable();                // Hanya untuk BETWEEN
            $table->integer('point_modifier');                                    // +5 atau -3
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_rules');
    }
};
