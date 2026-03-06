<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrPresensi extends Model
{
    use HasFactory;

    protected $table = 'qr_presensi';

    // Hanya kolom yang BENAR-BENAR ada di tabel database
    // (sesuai migration 2026_02_04_050841_qr_presensi.php)
    protected $fillable = [
        'qr_token',
        'expired_at',
        'is_active',
    ];

    public $timestamps = true;

    protected $casts = [
        'expired_at' => 'datetime',
        'is_active'  => 'boolean',
    ];
}
