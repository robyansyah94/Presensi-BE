<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $fillable = [
        'users_id',
        'jabatan_id',
        'nip',
        'nama',
        'no_hp',
        'alamat',
        'foto',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function jadwalShift()
    {
        return $this->hasMany(JadwalShift::class);
    }
}
