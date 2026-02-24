<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'qr_token' => 'required'
        ]);

        // 1. Validasi QR
        $qr = \App\Models\QrPresensi::where('qr_token', $request->qr_token)
            ->where('expired_at', '>=', now())
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$qr) {
            return response()->json([
                'message' => 'QR Expired atau Tidak Valid!'
            ], 400);
        }

        // 2. Ambil user login
        $user = $request->user();

        $karyawan = \App\Models\Karyawan::where('users_id', $user->id)->first();

        if (!$karyawan) {
            return response()->json([
                'message' => 'Karyawan tidak terdaftar'
            ], 404);
        }

        // 3. Cek presensi hari ini
        $presensi = \App\Models\Presensi::where('karyawan_id', $karyawan->id)
            ->where('tanggal', now()->toDateString())
            ->first();

        if (!$presensi) {

            \App\Models\Presensi::create([
                'karyawan_id' => $karyawan->id,
                'shift_id' => 2, // sementara
                'tanggal' => now()->toDateString(),
                'jam_masuk' => now()->toTimeString(),
                'status' => 'hadir',
                'qr_token' => $qr->qr_token
            ]);

            return response()->json([
                'message' => 'Presensi Masuk Berhasil'
            ]);
        }

        // Kalau sudah ada → berarti pulang
        if (!$presensi->jam_pulang) {

            $presensi->update([
                'jam_pulang' => now()->toTimeString()
            ]);

            return response()->json([
                'message' => 'Presensi Pulang Berhasil'
            ]);
        }

        return response()->json([
            'message' => 'Anda sudah melakukan presensi masuk & pulang hari ini'
        ], 400);
    }
}
