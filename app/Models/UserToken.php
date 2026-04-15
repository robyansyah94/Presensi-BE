<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'status',
        'used_at_attendance_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(FlexibilityItem::class, 'item_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Presensi::class, 'used_at_attendance_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('status', 'AVAILABLE')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isAvailable(): bool
    {
        if ($this->status !== 'AVAILABLE') return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;

        return true;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'AVAILABLE' => '✅ Tersedia',
            'USED'      => '✔️ Sudah Dipakai',
            'EXPIRED'   => '❌ Kadaluarsa',
            default     => $this->status,
        };
    }
}
