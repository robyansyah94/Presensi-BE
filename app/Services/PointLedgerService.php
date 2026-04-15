<?php

namespace App\Services;

use App\Models\PointLedger;
use Illuminate\Support\Facades\DB;

class PointLedgerService
{
    /**
     * Catat transaksi poin ke buku besar.
     * Menggunakan DB transaction + lockForUpdate untuk mencegah race condition.
     *
     * @param  int     $userId
     * @param  string  $type          EARN | SPEND | PENALTY
     * @param  int     $amount        Selalu positif
     * @param  string  $description
     * @param  string|null $refType
     * @param  int|null    $refId
     * @return PointLedger|null       null hanya jika SPEND tapi saldo tidak cukup
     */
    public function record(
        int $userId,
        string $type,
        int $amount,
        string $description,
        ?string $refType = null,
        ?int $refId = null
    ): ?PointLedger {
        return DB::transaction(function () use ($userId, $type, $amount, $description, $refType, $refId) {

            // Kunci baris terakhir untuk mencegah concurrent write
            $currentBalance = PointLedger::where('user_id', $userId)
                ->lockForUpdate()
                ->latest('id')
                ->value('current_balance') ?? 0;

            // Hitung saldo baru
            $newBalance = match ($type) {
                'EARN'    => $currentBalance + $amount,
                'SPEND',
                'PENALTY' => $currentBalance - $amount,
                default   => $currentBalance,
            };

            // Hanya SPEND yang ditolak jika saldo tidak cukup.
            // PENALTY tetap dicatat meski saldo jadi minus (denda harus tercatat).
            if ($type === 'SPEND' && $newBalance < 0) {
                return null;
            }

            return PointLedger::create([
                'user_id'          => $userId,
                'transaction_type' => $type,
                'amount'           => $amount,
                'current_balance'  => $newBalance,
                'description'      => $description,
                'reference_type'   => $refType,
                'reference_id'     => $refId,
            ]);
        });
    }

    /**
     * Ambil saldo poin user saat ini.
     */
    public function getBalance(int $userId): int
    {
        return PointLedger::where('user_id', $userId)
            ->latest('id')
            ->value('current_balance') ?? 0;
    }

    /**
     * Ambil riwayat mutasi dengan pagination.
     */
    public function getHistory(int $userId, int $perPage = 20)
    {
        return PointLedger::where('user_id', $userId)
            ->latest('id')
            ->paginate($perPage);
    }
}