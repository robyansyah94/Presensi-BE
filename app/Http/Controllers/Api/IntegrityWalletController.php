<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PointLedgerService;
use Illuminate\Http\Request;

/**
 * IntegrityWalletController
 * Controller untuk mengelola "dompet" poin disiplin pengguna.
 */
class IntegrityWalletController extends Controller
{
    public function __construct(
        private readonly PointLedgerService $ledger
    ) {}

    /**
     * GET /api/wallet
     * Mengembalikan saldo + info level user.
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $balance = $this->ledger->getBalance($user->id);

        return response()->json([
            'balance' => $balance,
            'level'   => $this->getLevel($balance),
            'user'    => [
                'name'  => $user->name,
                'foto'  => optional($user->karyawan)->foto,
            ],
        ]);
    }

    /**
     * GET /api/wallet/history
     * Riwayat mutasi poin (paginated).
     */
    public function history(Request $request)
    {
        $user    = $request->user();
        $history = $this->ledger->getHistory($user->id, perPage: 15);

        $history->getCollection()->transform(function ($item) {
            return [
                'id'               => $item->id,
                'type'             => $item->transaction_type,
                'icon'             => $item->icon,
                'amount'           => $item->amount,
                'delta'            => $item->delta,
                'current_balance'  => $item->current_balance,
                'description'      => $item->description,
                'created_at'       => $item->created_at->format('d M Y, H:i'),
            ];
        });

        return response()->json($history);
    }

    // ── Level System ──────────────────────────────────────────────────────────

    private function getLevel(int $balance): array
    {
        return match (true) {
            $balance >= 500 => ['name' => 'Disiplin Elite 🏆',    'color' => '#FFD700', 'min' => 500, 'next' => null],
            $balance >= 200 => ['name' => 'Sangat Disiplin ⭐',   'color' => '#C0C0C0', 'min' => 200, 'next' => 500],
            $balance >= 100 => ['name' => 'Disiplin 🔵',          'color' => '#4F8EF7', 'min' => 100, 'next' => 200],
            $balance >= 50  => ['name' => 'Cukup Disiplin 🟢',    'color' => '#4CAF50', 'min' => 50,  'next' => 100],
            $balance >= 0   => ['name' => 'Pemula 🟡',            'color' => '#FFC107', 'min' => 0,   'next' => 50],
            default         => ['name' => 'Perlu Perbaikan 🔴',   'color' => '#F44336', 'min' => null, 'next' => 0],
        };
    }
}
