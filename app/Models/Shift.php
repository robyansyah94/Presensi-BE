<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'nama_shift',
        'jam_masuk',
        'jam_pulang',
        'toleransi_telat'
    ];

    public function jadwalShift()
    {
        return $this->hasMany(JadwalShift::class);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }
}
