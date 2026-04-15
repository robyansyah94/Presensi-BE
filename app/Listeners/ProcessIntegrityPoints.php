<?php

namespace App\Listeners;

use App\Events\PresensiRecorded;
use App\Services\RuleEngineService;

/**
 * ShouldQueue DIHAPUS → listener berjalan synchronous (tidak butuh queue worker).
 * Jika di masa depan ingin pakai queue, tambahkan kembali implements ShouldQueue
 * dan jalankan: php artisan queue:work
 */
class ProcessIntegrityPoints
{
    public function __construct(
        private readonly RuleEngineService $engine
    ) {}

    public function handle(PresensiRecorded $event): void
    {
        $this->engine->evaluate($event->presensi, $event->karyawan);
    }
}
