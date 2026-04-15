<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Seeder lama kamu (jika ada, tetap di sini)
            // UserSeeder::class,

            // ── Fitur Gamifikasi ──────────────────────────
            PointRulesSeeder::class,
            FlexibilityItemsSeeder::class,
        ]);
    }
}
