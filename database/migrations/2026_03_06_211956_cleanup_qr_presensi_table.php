<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus semua record lama yang sudah tidak aktif (sampah akumulasi)
        DB::table('qr_presensi')->where('is_active', false)->delete();

        // Tambah index agar query QR aktif lebih cepat
        Schema::table('qr_presensi', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('qr_presensi', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
    }
};
