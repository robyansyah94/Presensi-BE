<?php

namespace App\Http\Controllers\Api;

use App\Events\PresensiRecorded;
use App\Http\Controllers\Controller;
use App\Models\JadwalShift;
use App\Models\Karyawan;
use App\Models\LokasiKantor;
use App\Models\Presensi;
use App\Models\QrPresensi;
use App\Services\AlpaPointService;
use App\Services\TokenInterceptorService;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function __construct(
        private readonly TokenInterceptorService $tokenInterceptor,
        private readonly AlpaPointService $alpaPointService
    ) {}

    public function scan(Request $request)
    {
        // Panggil di awal method scan (ketika ada karyawan scan, maka sistem akan cek semua jadwal hari ini, jika ada yang jam_pulang-nya sudah lewat tapi belum ada record presensi, maka akan otomatis diinsert sebagai alpa)
        $this->checkAndInsertAlpa();

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

            $jamMasukShift    = now()->setTimeFromTimeString($shift->jam_masuk);
            $batasAwalCheckin = $jamMasukShift->copy()->subMinutes(60);
            $batasAkhirCheckin = $jamMasukShift->copy()->addMinutes(60);
            $sekarang          = now();

            if ($sekarang->lt($batasAwalCheckin)) {
                return response()->json([
                    'message' => "Presensi masuk belum bisa dilakukan. Anda bisa check-in mulai jam {$batasAwalCheckin->format('H:i')} (60 menit sebelum shift).",
                ], 403);
            }

            if ($sekarang->gt($batasAkhirCheckin)) {
                return response()->json([
                    'message' => "Waktu check-in sudah berakhir. Batas check-in untuk shift ini adalah jam {$batasAkhirCheckin->format('H:i')}.",
                ], 403);
            }

            // ── Hitung status & menit terlambat ──────────────────────────────
            $batasTerlambat = $jamMasukShift->copy()->addMinutes($shift->toleransi_telat);
            $isTerlambat    = $sekarang->gt($batasTerlambat);
            $statusAwal     = $isTerlambat ? 'terlambat' : 'hadir';

            // Hitung menit terlambat dari jam mulai shift (bukan dari batas toleransi)
            $menitTerlambat = $isTerlambat
                ? (int) $jamMasukShift->diffInMinutes($sekarang)
                : 0;

            // ── TOKEN INTERCEPTOR ─────────────────────────────────────────────
            // Cek apakah user punya token kelonggaran yang bisa menutup keterlambatan ini
            $intercept   = $this->tokenInterceptor->intercept($karyawan, $statusAwal, $menitTerlambat);
            $finalStatus = $intercept['status'];
            $tokenUsed   = $intercept['token_used'];

            // ── Simpan Presensi ───────────────────────────────────────────────
            $presensiRecord = Presensi::create([
                'karyawan_id'       => $karyawan->id,
                'shift_id'          => $shift->id,
                'tanggal'           => $today,
                'jam_masuk'         => $sekarang->toTimeString(),
                'status'            => $finalStatus,
                'latitude'          => $request->latitude,
                'longitude'         => $request->longitude,
                'jarak_dari_kantor' => $jarak,
                'qr_token'          => $qr->qr_token,
            ]);

            // ── Tandai Token sebagai USED (setelah presensi tersimpan) ────────
            if ($tokenUsed) {
                $tokenUsed->update([
                    'status'                => 'USED',
                    'used_at_attendance_id' => $presensiRecord->id,
                ]);
            }

            // ── Fire Event → Rule Engine → Ledger ────────────────────────────
            // Load relasi shift agar RuleEngineService tidak N+1
            $presensiRecord->load('shift');
            event(new PresensiRecorded($presensiRecord, $karyawan));

            // ── Response ──────────────────────────────────────────────────────
            $label = match ($finalStatus) {
                'hadir'       => '✅ Tepat Waktu',
                'terlambat'   => '⚠️ Terlambat',
                'hadir_token' => '🎫 Token Digunakan',
                default       => $finalStatus,
            };

            $extra = $tokenUsed
                ? ['token_used' => $tokenUsed->item->item_name]
                : [];

            return response()->json(array_merge([
                'message' => "Presensi Masuk Berhasil! ({$label})",
                'status'  => $finalStatus,
                'jarak'   => $jarak,
            ], $extra));
        }

        // ── STEP G: Logika CHECK-OUT ──────────────────────────────────────────
        if (!$presensi->jam_pulang) {

            $jamPulangShift    = now()->setTimeFromTimeString($shift->jam_pulang);
            $batasAwalCheckout = $jamPulangShift->copy()->subMinutes(30);
            $sekarang          = now();

            if ($sekarang->lt($batasAwalCheckout)) {
                return response()->json([
                    'message' => "Presensi pulang belum bisa dilakukan. Anda bisa check-out mulai jam {$batasAwalCheckout->format('H:i')} (30 menit sebelum jam pulang {$jamPulangShift->format('H:i')}).",
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

    private function checkAndInsertAlpa(): void
    {
        $today   = now()->toDateString();
        $sekarang = now();

        // Ambil semua jadwal shift yang jam_pulang-nya sudah lewat hari ini
        $jadwalHariIni = JadwalShift::where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->with('shift')
            ->get();

        foreach ($jadwalHariIni as $jadwal) {
            $jamPulangShift = $sekarang->copy()->setTimeFromTimeString($jadwal->shift->jam_pulang);

            // Skip jika jam pulang shift belum lewat
            if ($sekarang->lt($jamPulangShift)) continue;

            // Skip jika sudah ada record presensi
            $sudahAbsen = Presensi::where('karyawan_id', $jadwal->karyawan_id)
                ->where('tanggal', $today)
                ->exists();

            if ($sudahAbsen) continue;

            // Insert alpa
            $presensiAlpa = Presensi::create([
                'karyawan_id'       => $jadwal->karyawan_id,
                'shift_id'          => $jadwal->shift_id,
                'tanggal'           => $today,
                'jam_masuk'         => null,
                'jam_pulang'        => null,
                'status'            => 'alpa',
                'latitude'          => null,
                'longitude'         => null,
                'jarak_dari_kantor' => null,
                'qr_token'          => null,
            ]);

            $this->alpaPointService->process($presensiAlpa);
        }
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
