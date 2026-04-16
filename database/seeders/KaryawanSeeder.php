<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('karyawan')->insert([
            [
                'users_id' => 1, // pastikan sesuai dengan user karyawan
                'jabatan_id' => 3, // sesuaikan dengan data jabatan
                'nip' => '1234',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Contoh Alamat',
                'foto' => null,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}