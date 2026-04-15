<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PointRulesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('point_rules')->insert([

            // ── EARN: Reward datang lebih awal ──────────────────────────────
            [
                'rule_name'           => 'Datang Pagi Banget',
                'target_role'         => 'all',
                'condition_type'      => 'jam_masuk',
                'condition_operator'  => '<',
                'condition_value'     => '06:30:00',
                'condition_value_max' => null,
                'point_modifier'      => 5,
                'is_active'           => true,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'rule_name'           => 'Datang Tepat Waktu',
                'target_role'         => 'all',
                'condition_type'      => 'jam_masuk',
                'condition_operator'  => 'BETWEEN',
                'condition_value'     => '06:30:00',
                'condition_value_max' => '07:00:00',
                'point_modifier'      => 3,
                'is_active'           => true,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'rule_name'           => 'Masuk Sebelum Jam 8',
                'target_role'         => 'karyawan',
                'condition_type'      => 'jam_masuk',
                'condition_operator'  => '<',
                'condition_value'     => '08:00:00',
                'condition_value_max' => null,
                'point_modifier'      => 2,
                'is_active'           => true,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],

            // ── PENALTY: Denda terlambat ────────────────────────────────────
            [
                'rule_name'           => 'Telat Ringan (1-15 menit)',
                'target_role'         => 'all',
                'condition_type'      => 'menit_terlambat',
                'condition_operator'  => 'BETWEEN',
                'condition_value'     => '1',
                'condition_value_max' => '15',
                'point_modifier'      => -2,
                'is_active'           => true,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'rule_name'           => 'Telat Sedang (16-30 menit)',
                'target_role'         => 'all',
                'condition_type'      => 'menit_terlambat',
                'condition_operator'  => 'BETWEEN',
                'condition_value'     => '16',
                'condition_value_max' => '30',
                'point_modifier'      => -4,
                'is_active'           => true,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'rule_name'           => 'Telat Parah (>30 menit)',
                'target_role'         => 'all',
                'condition_type'      => 'menit_terlambat',
                'condition_operator'  => '>',
                'condition_value'     => '30',
                'condition_value_max' => null,
                'point_modifier'      => -7,
                'is_active'           => true,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
        ]);
    }
}
