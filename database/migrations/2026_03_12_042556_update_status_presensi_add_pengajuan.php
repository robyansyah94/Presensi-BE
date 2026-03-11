<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ubah enum dengan ALTER TABLE
        DB::statement("ALTER TABLE presensi MODIFY COLUMN status ENUM('hadir','terlambat','alpa','izin','sakit','cuti') NOT NULL DEFAULT 'hadir'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE presensi MODIFY COLUMN status ENUM('hadir','terlambat','alpa') NOT NULL DEFAULT 'hadir'");
    }
};