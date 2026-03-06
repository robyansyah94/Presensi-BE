<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalShift;
use App\Models\Karyawan;
use App\Models\LokasiKantor;
use App\Models\Presensi;
use App\Models\QrPresensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function scan(Request $request)
    {
        // VALIDASI INPUT
        // Sekarang wajib kirim latitude & longitude dari frontend
        $request->validate([
            'qr_token'  => 'required|string',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // STEP A: Validasi QR Token 
        // Cek apakah QR yang di-scan valid, aktif, dan belum expired
        $qr = QrPresensi::where('qr_token', $request->qr_token)
            ->where('is_active', true)
            ->where('expired_at', '>=', now())
            ->first();

        if (!$qr) {
            return response()->json([
                'message' => 'QR tidak valid atau sudah kadaluarsa. Minta admin refresh QR.',
            ], 400);
        }

        // STEP B: Ambil Data Karyawan 
        $user     = $request->user(); // dari token Sanctum
        $karyawan = Karyawan::where('users_id', $user->id)->first();

        if (!$karyawan) {
            return response()->json([
                'message' => 'Data karyawan tidak ditemukan.',
            ], 404);
        }

        // STEP C: Validasi GPS / Lokasi 
        $lokasi = LokasiKantor::first(); // 1 kantor saja

        if (!$lokasi) {
            return response()->json([
                'message' => 'Lokasi kantor belum dikonfigurasi. Hubungi admin.',
            ], 500);
        }

        // Hitung jarak antara posisi karyawan dengan posisi kantor
        // menggunakan rumus Haversine (akurat untuk koordinat GPS)
        $jarak = $this->hitungJarak(
            $request->latitude,
            $request->longitude,
            $lokasi->latitude,
            $lokasi->longitude
        );

        // Tolak kalau di luar radius yang ditentukan admin
        if ($jarak > $lokasi->radius_meter) {
            return response()->json([
                'message'       => "Anda berada di luar area kantor. Jarak Anda: {$jarak}m, batas maksimum: {$lokasi->radius_meter}m.",
                'jarak'         => $jarak,
                'radius_kantor' => $lokasi->radius_meter,
            ], 403);
        }

        // sTEP D: Cari Shift Aktif Karyawan Hari Ini
        // SEBELUM (kode lama): 'shift_id' => 2  ← hardcode, salah!
        // SESUDAH: ambil dari tabel jadwal_shift berdasarkan tanggal hari ini
        $today  = now()->toDateString();
        $jadwal = JadwalShift::where('karyawan_id', $karyawan->id)
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->with('shift')
            ->first();

        if (!$jadwal) {
            return response()->json([
                'message' => 'Anda tidak memiliki jadwal shift hari ini.',
            ], 403);
        }

        $shift = $jadwal->shift;

        // Tentukan Status (Hadir / Terlambat)
        // Bandingkan jam sekarang dengan jam_masuk shift + toleransi
        $jamMasukShift  = now()->setTimeFromTimeString($shift->jam_masuk);
        $batasTerlambat = $jamMasukShift->copy()->addMinutes($shift->toleransi_telat);
        $status         = now()->gt($batasTerlambat) ? 'terlambat' : 'hadir';

        // STEP F: Catat Presensi
        $presensi = Presensi::where('karyawan_id', $karyawan->id)
            ->where('tanggal', $today)
            ->first();

        // Belum ada presensi hari ini → CHECK-IN
        if (!$presensi) {
            Presensi::create([
                'karyawan_id'       => $karyawan->id,
                'shift_id'          => $shift->id, // ← sekarang dinamis!
                'tanggal'           => $today,
                'jam_masuk'         => now()->toTimeString(),
                'status'            => $status,
                'latitude'          => $request->latitude,
                'longitude'         => $request->longitude,
                'jarak_dari_kantor' => $jarak,
                'qr_token'          => $qr->qr_token,
            ]);

            $label = $status === 'terlambat' ? '⚠️ Terlambat' : '✅ Tepat Waktu';

            return response()->json([
                'message' => "Presensi Masuk Berhasil! ({$label})",
                'status'  => $status,
                'jarak'   => $jarak,
            ]);
        }

        // Sudah check-in, belum check-out → CHECK-OUT
        if (!$presensi->jam_pulang) {
            $presensi->update([
                'jam_pulang'        => now()->toTimeString(),
                'latitude'          => $request->latitude,
                'longitude'         => $request->longitude,
                'jarak_dari_kantor' => $jarak,
            ]);

            return response()->json([
                'message' => 'Presensi Pulang Berhasil! ✅',
                'jarak'   => $jarak,
            ]);
        }

        // Sudah check-in & check-out → tolak
        return response()->json([
            'message' => 'Anda sudah melakukan presensi masuk dan pulang hari ini.',
        ], 400);
    }
    
    // Menghitung jarak (meter) antara dua koordinat GPS dengan akurat
    private function hitungJarak(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371000; // radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($R * $c, 2); // hasil dalam meter, 2 angka desimal
    }
}
