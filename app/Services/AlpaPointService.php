<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Presensi;

/**
 * Service ini dipanggil dari controller/command yang mencatat status alpa.
 * Tugasnya: pastikan Rule Engine dijalankan untuk presensi yang berstatus alpa.
 *
 * CARA PAKAI — di controller/command yang menyimpan alpa:
 *
 *   // Setelah Presensi::create(['status' => 'alpa', ...]) atau update:
 *   app(AlpaPointService::class)->process($presensi);
 */
class AlpaPointService
{
    public function __construct(
        private readonly RuleEngineService $engine
    ) {}

    public function process(Presensi $presensi): void
    {
        // Hanya proses jika statusnya memang alpa
        if ($presensi->status !== 'alpa') return;

        $karyawan = $presensi->karyawan;

        if (!$karyawan) {
            \Log::warning("AlpaPointService: karyawan null untuk presensi #{$presensi->id}");
            return;
        }

        $this->engine->evaluate($presensi, $karyawan);
    }
}