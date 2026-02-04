<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $fillable = [
        'karyawan_id',
        'shift_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'latitude',
        'longitude',
        'jarak_dari_kantor',
        'qr_token'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
