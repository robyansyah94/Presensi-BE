<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\PointLedger;
use App\Models\PointRule;
use App\Models\Presensi;
use Carbon\Carbon;

class RuleEngineService
{
    public function __construct(
        private readonly PointLedgerService $ledger
    ) {}

    /**
     * Evaluasi semua rule aktif untuk satu presensi check-in.
     * Dipanggil oleh Listener setelah event PresensiRecorded.
     */
    public function evaluate(Presensi $presensi, Karyawan $karyawan): void
    {
        // Guard: hanya proses check-in (jam_pulang belum ada)
        if (!$presensi->jam_masuk || $presensi->jam_pulang) return;

        // Guard: token sudah menangani, skip Rule Engine untuk poin
        if ($presensi->status === 'hadir_token') return;

        // Guard: jangan double-proses rule yang sama untuk 1 presensi
        $alreadyProcessed = PointLedger::where('reference_type', 'presensi')
            ->where('reference_id', $presensi->id)
            ->exists();

        if ($alreadyProcessed) return;

        // ── Load relasi yang dibutuhkan (hindari null / lazy load gagal) ──────
        // Pastikan shift ter-load (untuk jam_masuk shift)
        if (!$presensi->relationLoaded('shift')) {
            $presensi->load('shift');
        }

        // Pastikan relasi user ter-load untuk ambil role
        if (!$karyawan->relationLoaded('user')) {
            $karyawan->load('user');
        }

        // Guard: shift tidak ada (seharusnya tidak terjadi, tapi aman)
        if (!$presensi->shift) {
            \Log::warning("RuleEngine: shift null untuk presensi #{$presensi->id}");
            return;
        }

        // ── Hitung parameter kondisi ──────────────────────────────────────────
        $jamMasuk      = Carbon::createFromTimeString($presensi->jam_masuk);
        $jamMasukShift = Carbon::createFromTimeString($presensi->shift->jam_masuk);

        // Menit terlambat: 0 jika datang tepat/lebih awal
        $menitTerlambat = $jamMasuk->gt($jamMasukShift)
            ? (int) $jamMasukShift->diffInMinutes($jamMasuk)
            : 0;

        // ── Ambil role karyawan (fallback ke 'karyawan' jika relasi null) ─────
        $role = optional($karyawan->user)->role ?? 'karyawan';

        // ── Ambil semua rule aktif untuk role ini ─────────────────────────────
        $rules = PointRule::active()->forRole($role)->get();

        if ($rules->isEmpty()) {
            \Log::info("RuleEngine: tidak ada rule aktif untuk role '{$role}'");
            return;
        }

        // ── Evaluasi setiap rule ──────────────────────────────────────────────
        foreach ($rules as $rule) {
            if (!$this->matches($rule, $jamMasuk, $menitTerlambat)) {
                continue;
            }

            $type        = $rule->point_modifier > 0 ? 'EARN' : 'PENALTY';
            $absAmount   = abs($rule->point_modifier);
            $tanggal     = Carbon::parse($presensi->tanggal)->format('d/m/Y');
            $description = "[{$rule->rule_name}] pada {$tanggal}";

            $result = $this->ledger->record(
                userId: $karyawan->users_id,
                type: $type,
                amount: $absAmount,
                description: $description,
                refType: 'presensi',
                refId: $presensi->id,
            );

            \Log::info("RuleEngine: rule '{$rule->rule_name}' cocok → {$type} {$absAmount} poin untuk user #{$karyawan->users_id}. Ledger ID: " . ($result?->id ?? 'GAGAL'));
        }
    }

    // ── Evaluator per Rule ────────────────────────────────────────────────────

    private function matches(PointRule $rule, Carbon $jamMasuk, int $menitTerlambat): bool
    {
        return match ($rule->condition_type) {
            'jam_masuk'       => $this->matchTime($rule, $jamMasuk),
            'menit_terlambat' => $this->matchMinutes($rule, $menitTerlambat),
            default           => false,
        };
    }

    private function matchTime(PointRule $rule, Carbon $jamMasuk): bool
    {
        $val = Carbon::createFromTimeString($rule->condition_value);

        return match ($rule->condition_operator) {
            '<'       => $jamMasuk->lt($val),
            '>'       => $jamMasuk->gt($val),
            'BETWEEN' => $jamMasuk->between(
                $val,
                Carbon::createFromTimeString($rule->condition_value_max)
            ),
            default   => false,
        };
    }

    private function matchMinutes(PointRule $rule, int $menitTerlambat): bool
    {
        $val = (int) $rule->condition_value;

        return match ($rule->condition_operator) {
            '<'       => $menitTerlambat > 0 && $menitTerlambat < $val,
            '>'       => $menitTerlambat > $val,
            'BETWEEN' => $menitTerlambat >= $val
                && $menitTerlambat <= (int) $rule->condition_value_max,
            default   => false,
        };
    }
}
