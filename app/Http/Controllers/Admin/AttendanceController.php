<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function history(Request $request)
    {
        // Default: hari ini
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        // Validasi format tanggal supaya tidak error
        try {
            Carbon::parse($tanggal);
        } catch (\Exception $e) {
            $tanggal = Carbon::today()->toDateString();
        }

        // ── Ambil data presensi di tanggal ini ──────────────────────────
        // Eager load semua relasi yang dibutuhkan tabel
        $presensi = Presensi::with([
            'karyawan.user',
            'karyawan.jabatan',
            'shift',
        ])
            ->where('tanggal', $tanggal)
            ->get();

        // ── Semua karyawan aktif ─────────────────────────────────────────
        $semuaKaryawan = Karyawan::with(['user', 'jabatan', 'jadwalShift.shift'])
            ->where('status', 'aktif')
            ->get();

        $totalKaryawan = $semuaKaryawan->count();

        // ── Karyawan yang ALPA (tidak ada di tabel presensi hari ini) ────
        $hadirIds = $presensi->pluck('karyawan_id')->toArray();

        $karyawanAlpa = $semuaKaryawan->filter(
            fn($k) => !in_array($k->id, $hadirIds)
        );

        return view('admin.attendance.history', compact(
            'presensi',
            'karyawanAlpa',
            'tanggal',
            'totalKaryawan',
        ));
    }

    //export
    public function export(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        // Validasi format tanggal
        try {
            Carbon::parse($tanggal);
        } catch (\Exception $e) {
            $tanggal = Carbon::today()->toDateString();
        }

        $namaFile = 'attendance_' . $tanggal . '.xlsx';

        return Excel::download(new AttendanceExport($tanggal), $namaFile);
    }
}
