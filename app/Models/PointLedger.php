<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointLedger extends Model
{
    public $timestamps = false;   // Hanya pakai created_at (manual)

    protected $fillable = [
        'user_id',
        'transaction_type',
        'amount',
        'current_balance',
        'description',
        'reference_type',
        'reference_id',
        'created_at',
    ];

    protected $casts = [
        'amount'          => 'integer',
        'current_balance' => 'integer',
        'created_at'      => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isEarn(): bool
    {
        return $this->transaction_type === 'EARN';
    }

    public function isPenalty(): bool
    {
        return $this->transaction_type === 'PENALTY';
    }

    public function isSpend(): bool
    {
        return $this->transaction_type === 'SPEND';
    }

    /**
     * Nilai delta (positif atau negatif) untuk ditampilkan di riwayat mutasi.
     */
    public function getDeltaAttribute(): int
    {
        return in_array($this->transaction_type, ['EARN'])
            ? +$this->amount
            : -$this->amount;
    }

    public function getIconAttribute(): string
    {
        return match ($this->transaction_type) {
            'EARN'    => '⬆️',
            'PENALTY' => '⬇️',
            'SPEND'   => '🛍️',
            default   => '•',
        };
    }
}