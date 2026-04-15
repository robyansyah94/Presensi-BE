<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan status 'hadir_token' untuk presensi yang memakai Token Kelonggaran
        DB::statement("ALTER TABLE presensi MODIFY COLUMN status ENUM(
            'hadir',
            'terlambat',
            'alpa',
            'izin',
            'sakit',
            'cuti',
            'hadir_token'
        ) NOT NULL DEFAULT 'hadir'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE presensi MODIFY COLUMN status ENUM(
            'hadir',
            'terlambat',
            'alpa',
            'izin',
            'sakit',
            'cuti'
        ) NOT NULL DEFAULT 'hadir'");
    }
};
