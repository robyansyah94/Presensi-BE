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
     * Entry point utama — dipanggil dari:
     * 1. Listener ProcessIntegrityPoints (setelah check-in berhasil)
     * 2. Langsung dari controller/service saat sistem mencatat ALPA
     */
    public function evaluate(Presensi $presensi, Karyawan $karyawan): void
    {
        // Guard: jangan double-proses rule yang sama untuk 1 presensi
        $alreadyProcessed = PointLedger::where('reference_type', 'presensi')
            ->where('reference_id', $presensi->id)
            ->exists();

        if ($alreadyProcessed) return;

        // Load relasi yang dibutuhkan
        if (!$presensi->relationLoaded('shift')) {
            $presensi->load('shift');
        }
        if (!$karyawan->relationLoaded('user')) {
            $karyawan->load('user');
        }

        if (!$presensi->shift) {
            \Log::warning("RuleEngine: shift null untuk presensi #{$presensi->id}");
            return;
        }

        // ── Hitung semua parameter kondisi ───────────────────────────────────
        $jamMasukShift = Carbon::createFromTimeString($presensi->shift->jam_masuk);
        $status        = $presensi->status; // hadir / terlambat / alpa / hadir_token

        // Untuk alpa: jam_masuk null, set parameter ke nilai "tidak hadir"
        $jamMasuk       = $presensi->jam_masuk
            ? Carbon::createFromTimeString($presensi->jam_masuk)
            : null;

        // Menit terlambat (dari jam mulai shift)
        $menitTerlambat = ($jamMasuk && $jamMasuk->gt($jamMasukShift))
            ? (int) $jamMasukShift->diffInMinutes($jamMasuk)
            : 0;

        // Menit lebih awal (datang sebelum jam shift)
        $menitLebihAwal = ($jamMasuk && $jamMasuk->lt($jamMasukShift))
            ? (int) $jamMasuk->diffInMinutes($jamMasukShift)
            : 0;

        // ── Ambil role & rules ────────────────────────────────────────────────
        $role  = optional($karyawan->user)->role ?? 'karyawan';
        $rules = PointRule::active()->forRole($role)->get();

        if ($rules->isEmpty()) return;

        // ── Evaluasi setiap rule ──────────────────────────────────────────────
        foreach ($rules as $rule) {
            if (!$this->matches($rule, $jamMasuk, $menitTerlambat, $menitLebihAwal, $status)) {
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

            \Log::info("RuleEngine: '{$rule->rule_name}' → {$type} {$absAmount} poin | user #{$karyawan->users_id} | ledger: " . ($result?->id ?? 'GAGAL'));
        }
    }

    // ── Dispatcher kondisi ────────────────────────────────────────────────────

    private function matches(
        PointRule $rule,
        ?Carbon $jamMasuk,
        int $menitTerlambat,
        int $menitLebihAwal,
        string $status
    ): bool {
        return match ($rule->condition_type) {
            'jam_masuk'        => $jamMasuk ? $this->matchTime($rule, $jamMasuk) : false,
            'menit_terlambat'  => $this->matchMinutes($rule, $menitTerlambat),
            'menit_lebih_awal' => $this->matchMinutes($rule, $menitLebihAwal),
            'status_presensi'  => $this->matchStatus($rule, $status),
            default            => false,
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

    private function matchMinutes(PointRule $rule, int $menit): bool
    {
        $val = (int) $rule->condition_value;

        return match ($rule->condition_operator) {
            '<'       => $menit > 0 && $menit < $val,
            '>'       => $menit > $val,
            'BETWEEN' => $menit >= $val && $menit <= (int) $rule->condition_value_max,
            default   => false,
        };
    }

    /**
     * Cocokkan berdasarkan status presensi.
     * condition_value diisi dengan status: hadir / terlambat / alpa / hadir_token
     * condition_operator diabaikan (selalu exact match).
     */
    private function matchStatus(PointRule $rule, string $status): bool
    {
        return strtolower($rule->condition_value) === strtolower($status);
    }
}
