<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointLedger;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;

class IntegrityAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->integer('bulan', now()->month);
        $tahun = $request->integer('tahun', now()->year);

        // ── Leaderboard: saldo poin tertinggi ─────────────────────────────────
        // Ambil current_balance terbaru per user via subquery
        $leaderboard = User::where('role', 'karyawan')
            ->with('karyawan.jabatan')
            ->get()
            ->map(function ($user) {
                $balance = PointLedger::where('user_id', $user->id)
                    ->latest('id')
                    ->value('current_balance') ?? 0;

                return [
                    'user'    => $user,
                    'balance' => $balance,
                    'level'   => $this->getLevel($balance),
                ];
            })
            ->sortByDesc('balance')
            ->values();

        // ── Statistik transaksi bulan ini ─────────────────────────────────────
        $earnTotal   = PointLedger::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('transaction_type', 'EARN')
            ->sum('amount');

        $penaltyTotal = PointLedger::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('transaction_type', 'PENALTY')
            ->sum('amount');

        $tokenRedeemed = UserToken::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->count();

        $tokenUsed = UserToken::whereMonth('updated_at', $bulan)
            ->whereYear('updated_at', $tahun)
            ->where('status', 'USED')
            ->count();

        return view('admin.integrity.analytics.index', compact(
            'leaderboard',
            'earnTotal',
            'penaltyTotal',
            'tokenRedeemed',
            'tokenUsed',
            'bulan',
            'tahun',
        ));
    }

    private function getLevel(int $balance): string
    {
        return match (true) {
            $balance >= 500 => 'Disiplin Elite 🏆',
            $balance >= 200 => 'Sangat Disiplin ⭐',
            $balance >= 100 => 'Disiplin 🔵',
            $balance >= 50  => 'Cukup Disiplin 🟢',
            $balance >= 0   => 'Pemula 🟡',
            default         => 'Perlu Perbaikan 🔴',
        };
    }
}
