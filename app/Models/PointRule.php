<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    protected $fillable = [
        'rule_name',
        'target_role',
        'condition_type',
        'condition_operator',
        'condition_value',
        'condition_value_max',
        'point_modifier',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'point_modifier' => 'integer',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('target_role', $role)->orWhere('target_role', 'all');
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isEarn(): bool
    {
        return $this->point_modifier > 0;
    }

    public function isPenalty(): bool
    {
        return $this->point_modifier < 0;
    }

    public function getFormattedModifierAttribute(): string
    {
        return ($this->point_modifier > 0 ? '+' : '') . $this->point_modifier . ' Poin';
    }

    public function getConditionLabelAttribute(): string
    {
        $type = $this->condition_type === 'jam_masuk' ? 'Jam Kedatangan' : 'Menit Terlambat';
        $op   = $this->condition_operator;
        $val  = $this->condition_value;
        $max  = $this->condition_value_max;

        if ($op === 'BETWEEN') {
            return "{$type} ANTARA {$val} DAN {$max}";
        }

        return "{$type} {$op} {$val}";
    }
}
