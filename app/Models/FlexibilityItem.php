<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlexibilityItem extends Model
{
    protected $fillable = [
        'item_name',
        'description',
        'icon',
        'token_type',
        'tolerance_minutes',
        'point_cost',
        'stock_limit',
        'is_active',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'point_cost'        => 'integer',
        'stock_limit'       => 'integer',
        'tolerance_minutes' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function userTokens()
    {
        return $this->hasMany(UserToken::class, 'item_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getTokenTypeLabelAttribute(): string
    {
        return match ($this->token_type) {
            'late_tolerance' => 'Toleransi Keterlambatan',
            'wfh'            => 'Work From Home',
            'excuse'         => 'Izin Tanpa Surat',
            default          => $this->token_type,
        };
    }

    /**
     * Cek berapa token yg sudah dibeli user ini bulan ini (untuk stock_limit).
     */
    public function countPurchasedThisMonth(int $userId): int
    {
        return $this->userTokens()
            ->where('user_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function isStockAvailableFor(int $userId): bool
    {
        if (is_null($this->stock_limit)) {
            return true;
        }

        return $this->countPurchasedThisMonth($userId) < $this->stock_limit;
    }
}