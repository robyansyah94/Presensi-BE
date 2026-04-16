<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah dua condition_type baru:
        // - menit_lebih_awal : menit kedatangan sebelum jam shift (untuk reward rajin)
        // - status_presensi  : cocokkan berdasarkan status (hadir/terlambat/alpa)
        DB::statement("ALTER TABLE point_rules MODIFY COLUMN condition_type
            ENUM('jam_masuk', 'menit_terlambat', 'menit_lebih_awal', 'status_presensi')
            NOT NULL DEFAULT 'jam_masuk'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE point_rules MODIFY COLUMN condition_type
            ENUM('jam_masuk', 'menit_terlambat')
            NOT NULL DEFAULT 'jam_masuk'");
    }
};