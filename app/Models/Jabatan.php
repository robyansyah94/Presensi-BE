<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $fillable = [
        'nama_jabatan',
        'keterangan'
    ];

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }
}
