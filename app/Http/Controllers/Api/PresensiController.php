<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\JadwalShift;
use App\Models\QrPresensi;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function scan(Request $request)
    {
        $user = auth()->user();

        // cari karyawan
        $karyawan = Karyawan::where('users_id', $user->id)->first();

        if (!$karyawan) {
            return response()->json([
                'message' => 'Data karyawan tidak ditemukan'
            ], 404);
        }

        $today = Carbon::today();

        // cari jadwal shift hari ini
        $jadwal = JadwalShift::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$jadwal) {
            return response()->json([
                'message' => 'Tidak ada jadwal shift hari ini'
            ], 400);
        }

        // QR aktif
        $qr = QrPresensi::where('is_active', true)->first();

        if (!$qr) {
            return response()->json([
                'message' => 'QR tidak aktif'
            ], 400);
        }

        // cek presensi hari ini
        $presensi = Presensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        // ===== MASUK =====
        if (!$presensi) {

            Presensi::create([
                'karyawan_id' => $karyawan->id,
                'jadwal_shift_id' => $jadwal->id,
                'qr_presensi_id' => $qr->id,
                'tanggal' => $today,
                'jam_masuk' => now(),
                'status' => 'hadir',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'jarak_dari_kantor' => $request->jarak
            ]);

            return response()->json([
                'message' => 'Presensi masuk berhasil'
            ]);
        }

        // ===== PULANG =====
        if (!$presensi->jam_pulang) {
            $presensi->update([
                'jam_pulang' => now()
            ]);

            return response()->json([
                'message' => 'Presensi pulang berhasil'
            ]);
        }

        return response()->json([
            'message' => 'Anda sudah presensi hari ini'
        ], 400);
    }
}
