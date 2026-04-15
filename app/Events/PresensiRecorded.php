<?php

namespace App\Events;

use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PresensiRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Presensi $presensi,
        public readonly Karyawan $karyawan,
    ) {}
}
