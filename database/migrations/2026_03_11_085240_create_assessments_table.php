<?php

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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();   // siapa yang menilai (admin)
            $table->foreignId('evaluatee_id')->constrained('karyawan')->cascadeOnDelete(); // siapa yang dinilai (karyawan)
            $table->date('assessment_date');
            $table->enum('period', ['harian', 'mingguan', 'bulanan'])->default('bulanan');
            $table->string('period_label')->nullable(); // cth: "Maret 2026", "Minggu 1 Mar 2026"
            $table->text('general_notes')->nullable();  // catatan umum/kesimpulan
            $table->timestamps();

            // satu karyawan hanya bisa dinilai sekali per periode per label
            $table->unique(['evaluatee_id', 'period', 'period_label'], 'unique_assessment_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
