<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flexibility_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');                                           // Cth: "Token Bebas Terlambat 30 Menit"
            $table->text('description')->nullable();
            $table->string('icon', 10)->default('🎫');                            // Emoji icon untuk UI
            $table->enum('token_type', ['late_tolerance', 'wfh', 'excuse']);      // Tipe kelonggaran
            $table->unsignedInteger('tolerance_minutes')->nullable();              // Khusus late_tolerance: maks menit telat
            $table->unsignedInteger('point_cost');                                 // Harga dalam poin
            $table->unsignedInteger('stock_limit')->nullable();                    // Batas beli per bulan (null = unlimited)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flexibility_items');
    }
};
