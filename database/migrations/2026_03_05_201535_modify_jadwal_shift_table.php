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
        Schema::table('jadwal_shift', function (Blueprint $table) {

            // hapus index lama jika ada
            $table->dropUnique(['karyawan_id', 'tanggal']);

            // tambah kolom baru
            $table->date('tanggal_mulai')->after('shift_id');
            $table->date('tanggal_selesai')->after('tanggal_mulai');
        });

        Schema::table('jadwal_shift', function (Blueprint $table) {

            // hapus kolom lama
            $table->dropColumn('tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_shift', function (Blueprint $table) {

            $table->date('tanggal')->nullable();

            $table->dropColumn([
                'tanggal_mulai',
                'tanggal_selesai'
            ]);
        });
    }
};
