<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrPresensi extends Model
{
    protected $fillable = [
        'qr_token',
        'expired_at',
        'is_active'
    ];
}
