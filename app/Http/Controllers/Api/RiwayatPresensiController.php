<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatPresensiController extends Controller
{
    /**
     * GET /api/presensi/riwayat?bulan=2026-03
     * Mengembalikan semua presensi karyawan login pada bulan tertentu.
     */
    public function index(Request $request)
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        // Default: bulan ini
        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));

        try {
            $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format bulan tidak valid. Gunakan format YYYY-MM.'], 422);
        }

        $presensiList = Presensi::with('shift')
            ->where('karyawan_id', $karyawan->id)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($p) {
                // Hitung total jam kerja
                // Paksa parse sebagai time-only agar tidak terpengaruh komponen tanggal
                $totalHours = null;
                if ($p->jam_masuk && $p->jam_pulang) {
                    // Gabungkan dengan tanggal yang sama agar diff akurat
                    $tglStr = Carbon::parse($p->tanggal)->toDateString();
                    $masuk  = Carbon::parse($tglStr . ' ' . Carbon::parse($p->jam_masuk)->format('H:i:s'));
                    $pulang = Carbon::parse($tglStr . ' ' . Carbon::parse($p->jam_pulang)->format('H:i:s'));
                    // Jika pulang < masuk (shift lewat tengah malam), tambah 1 hari
                    if ($pulang->lte($masuk)) $pulang->addDay();
                    $diffMenit  = $masuk->diffInMinutes($pulang);
                    $totalHours = sprintf('%02d:%02d', intdiv($diffMenit, 60), $diffMenit % 60);
                }

                $tgl = Carbon::parse($p->tanggal);

                return [
                    'id'          => $p->id,
                    'tanggal'     => $p->tanggal,                        // YYYY-MM-DD
                    'hari'        => $tgl->translatedFormat('l'),         // Senin, Selasa, dst
                    'tanggal_num' => $tgl->format('d'),                   // 23
                    'jam_masuk'   => $p->jam_masuk
                        ? Carbon::parse($p->jam_masuk)->format('H:i')
                        : null,
                    'jam_pulang'  => $p->jam_pulang
                        ? Carbon::parse($p->jam_pulang)->format('H:i')
                        : null,
                    'total_jam'   => $totalHours,                        // "08:05" atau null
                    'status'      => $p->status,
                    'latitude'    => $p->latitude,
                    'longitude'   => $p->longitude,
                    'jarak'       => $p->jarak_dari_kantor
                        ? round($p->jarak_dari_kantor) . ' m dari kantor'
                        : null,
                    'shift'       => $p->shift ? [
                        'nama'       => $p->shift->nama_shift,
                        'jam_masuk'  => Carbon::parse($p->shift->jam_masuk)->format('H:i'),
                        'jam_pulang' => Carbon::parse($p->shift->jam_pulang)->format('H:i'),
                    ] : null,
                ];
            });

        // Ringkasan bulan
        $summary = [
            'total_hadir'    => $presensiList->whereIn('status', ['hadir', 'terlambat'])->count(),
            'total_terlambat' => $presensiList->where('status', 'terlambat')->count(),
            'total_alpa'     => 0, // opsional, bisa dihitung dari jadwal
        ];

        return response()->json([
            'bulan'    => $bulan,
            'summary'  => $summary,
            'data'     => $presensiList->values(),
        ]);
    }
}