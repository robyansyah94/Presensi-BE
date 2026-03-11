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
        Schema::create('assessment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // cth: "Disiplin", "Kerja Sama"
            $table->text('description')->nullable();       // deskripsi indikator
            $table->boolean('is_active')->default(true);   // bisa dinonaktifkan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_categories');
    }
};
