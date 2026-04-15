<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\UserToken;

class TokenInterceptorService
{
    /**
     * Cek apakah user punya token yang bisa mengintervensi status presensi.
     * Dipanggil SEBELUM Presensi::create() di PresensiController.
     *
     * @return array{status: string, token_used: UserToken|null}
     */
    public function intercept(Karyawan $karyawan, string $status, int $menitTerlambat): array
    {
        // Hanya intercept jika status terlambat
        if ($status !== 'terlambat') {
            return ['status' => $status, 'token_used' => null];
        }

        // Cari token late_tolerance yang:
        // 1. AVAILABLE
        // 2. tolerance_minutes >= menit terlambat user
        // 3. Diurutkan: token terkecil toleransinya yang dipakai dulu (efisiensi)
        $token = UserToken::where('user_id', $karyawan->users_id)
            ->available()
            ->whereHas('item', function ($q) use ($menitTerlambat) {
                $q->where('token_type', 'late_tolerance')
                  ->where('tolerance_minutes', '>=', $menitTerlambat)
                  ->where('is_active', true);
            })
            ->with('item')
            ->join('flexibility_items', 'flexibility_items.id', '=', 'user_tokens.item_id')
            ->orderBy('flexibility_items.tolerance_minutes')   // Pakai token terkecil yg cukup
            ->select('user_tokens.*')
            ->first();

        if (!$token) {
            return ['status' => $status, 'token_used' => null];
        }

        // Token ditemukan → ubah status ke hadir_token
        // Token belum di-mark USED di sini; dilakukan setelah Presensi tersimpan
        return ['status' => 'hadir_token', 'token_used' => $token];
    }
}