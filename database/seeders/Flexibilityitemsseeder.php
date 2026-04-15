<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FlexibilityItemsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('flexibility_items')->insert([

            // ── late_tolerance: Token bebas terlambat ──────────────────────
            [
                'item_name'          => 'Token Bebas Terlambat 15 Menit',
                'description'        => 'Gunakan token ini untuk memaafkan keterlambatan hingga 15 menit. Status absensi otomatis berubah menjadi Hadir Tepat Waktu.',
                'icon'               => '⏰',
                'token_type'         => 'late_tolerance',
                'tolerance_minutes'  => 15,
                'point_cost'         => 10,
                'stock_limit'        => 2,   // maks 2x beli per bulan
                'is_active'          => true,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'item_name'          => 'Token Bebas Terlambat 30 Menit',
                'description'        => 'Gunakan token ini untuk memaafkan keterlambatan hingga 30 menit. Status absensi otomatis berubah menjadi Hadir Tepat Waktu.',
                'icon'               => '⌚',
                'token_type'         => 'late_tolerance',
                'tolerance_minutes'  => 30,
                'point_cost'         => 20,
                'stock_limit'        => 1,   // maks 1x beli per bulan
                'is_active'          => true,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],

            // ── excuse: Token izin tanpa surat ─────────────────────────────
            [
                'item_name'          => 'Token Izin Tanpa Surat',
                'description'        => 'Izin 1 hari tanpa perlu melampirkan surat keterangan. Hanya berlaku 1x per bulan.',
                'icon'               => '📋',
                'token_type'         => 'excuse',
                'tolerance_minutes'  => null,
                'point_cost'         => 30,
                'stock_limit'        => 1,
                'is_active'          => true,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],

            // ── wfh: Token Work From Home ───────────────────────────────────
            [
                'item_name'          => 'Token WFH 1 Hari',
                'description'        => 'Bekerja dari rumah selama 1 hari penuh. Absensi tetap tercatat Hadir.',
                'icon'               => '🏠',
                'token_type'         => 'wfh',
                'tolerance_minutes'  => null,
                'point_cost'         => 50,
                'stock_limit'        => 2,
                'is_active'          => true,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'item_name'          => 'Token WFH 3 Hari',
                'description'        => 'Bekerja dari rumah selama 3 hari berturut-turut. Absensi tetap tercatat Hadir.',
                'icon'               => '💻',
                'token_type'         => 'wfh',
                'tolerance_minutes'  => null,
                'point_cost'         => 120,
                'stock_limit'        => 1,
                'is_active'          => true,
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
        ]);
    }
}