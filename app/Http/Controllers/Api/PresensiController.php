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
        // ── VALIDASI INPUT ────────────────────────────────────────────────────
        $request->validate([
            'qr_token'  => 'required|string',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // ── STEP A: Validasi QR Token ─────────────────────────────────────────
        $qr = QrPresensi::where('qr_token', $request->qr_token)
            ->where('is_active', true)
            ->where('expired_at', '>=', now())
            ->first();

        if (!$qr) {
            return response()->json([
                'message' => 'QR tidak valid atau sudah kadaluarsa. Minta admin refresh QR.',
            ], 400);
        }

        // ── STEP B: Ambil Data Karyawan ───────────────────────────────────────
        $user     = $request->user();
        $karyawan = Karyawan::where('users_id', $user->id)->first();

        if (!$karyawan) {
            return response()->json([
                'message' => 'Data karyawan tidak ditemukan.',
            ], 404);
        }

        // ── STEP C: Validasi GPS ──────────────────────────────────────────────
        $lokasi = LokasiKantor::first();

        if (!$lokasi) {
            return response()->json([
                'message' => 'Lokasi kantor belum dikonfigurasi. Hubungi admin.',
            ], 500);
        }

        $jarak = $this->hitungJarak(
            $request->latitude,
            $request->longitude,
            $lokasi->latitude,
            $lokasi->longitude
        );

        if ($jarak > $lokasi->radius_meter) {
            return response()->json([
                'message'       => "Anda berada di luar area kantor. Jarak Anda: {$jarak}m, batas maksimum: {$lokasi->radius_meter}m.",
                'jarak'         => $jarak,
                'radius_kantor' => $lokasi->radius_meter,
            ], 403);
        }

        // ── STEP D: Cari Shift Aktif ──────────────────────────────────────────
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

        // ── STEP E: Cek Presensi Hari Ini ─────────────────────────────────────
        $presensi = Presensi::where('karyawan_id', $karyawan->id)
            ->where('tanggal', $today)
            ->first();

        // ── STEP F: Logika CHECK-IN ───────────────────────────────────────────
        if (!$presensi) {

            // Waktu jam masuk shift (hari ini)
            $jamMasukShift = now()->setTimeFromTimeString($shift->jam_masuk);

            // Batas paling awal boleh check-in: 60 menit sebelum jam masuk
            $batasAwalCheckin = $jamMasukShift->copy()->subMinutes(60);

            // Batas paling akhir boleh check-in: 60 menit setelah jam masuk
            // (toleransi_telat hanya untuk menentukan status, bukan batas check-in)
            $batasAkhirCheckin = $jamMasukShift->copy()->addMinutes(60);

            $sekarang = now();

            // Terlalu awal
            if ($sekarang->lt($batasAwalCheckin)) {
                $mulaiStr = $batasAwalCheckin->format('H:i');
                return response()->json([
                    'message' => "Presensi masuk belum bisa dilakukan. Anda bisa check-in mulai jam {$mulaiStr} (60 menit sebelum shift).",
                ], 403);
            }

            // Terlalu telat (lebih dari 60 menit setelah jam masuk)
            if ($sekarang->gt($batasAkhirCheckin)) {
                $batasStr    = $batasAkhirCheckin->format('H:i');
                $jamMasukStr = $jamMasukShift->format('H:i');
                return response()->json([
                    'message' => "Waktu check-in sudah berakhir. Batas check-in untuk shift ini adalah jam {$batasStr} (60 menit setelah jam masuk {$jamMasukStr}).",
                ], 403);
            }

            // Tentukan status: hadir atau terlambat
            $batasTerlambat = $jamMasukShift->copy()->addMinutes($shift->toleransi_telat);
            $status         = $sekarang->gt($batasTerlambat) ? 'terlambat' : 'hadir';

            Presensi::create([
                'karyawan_id'       => $karyawan->id,
                'shift_id'          => $shift->id,
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

        // ── STEP G: Logika CHECK-OUT ──────────────────────────────────────────
        if (!$presensi->jam_pulang) {

            // Batas paling awal boleh check-out: 30 menit sebelum jam pulang shift
            $jamPulangShift    = now()->setTimeFromTimeString($shift->jam_pulang);
            $batasAwalCheckout = $jamPulangShift->copy()->subMinutes(30);

            $sekarang = now();

            if ($sekarang->lt($batasAwalCheckout)) {
                $mulaiStr    = $batasAwalCheckout->format('H:i');
                $jamPulangStr = $jamPulangShift->format('H:i');
                return response()->json([
                    'message' => "Presensi pulang belum bisa dilakukan. Anda bisa check-out mulai jam {$mulaiStr} (30 menit sebelum jam pulang {$jamPulangStr}).",
                ], 403);
            }

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

        // ── STEP H: Sudah check-in & check-out ───────────────────────────────
        return response()->json([
            'message' => 'Anda sudah melakukan presensi masuk dan pulang hari ini.',
        ], 400);
    }

    // ── Haversine formula ─────────────────────────────────────────────────────
    private function hitungJarak(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($R * $c, 2);
    }
}
