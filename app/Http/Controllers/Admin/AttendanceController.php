<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\AttendanceExport;
use App\Exports\AttendanceMonthlyExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function history(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        try {
            Carbon::parse($tanggal);
        } catch (\Exception $e) {
            $tanggal = Carbon::today()->toDateString();
        }

        // Presensi hari ini — diurutkan A-Z by nama
        $presensi = Presensi::with([
            'karyawan.user',
            'karyawan.jabatan',
            'shift',
        ])
            ->where('tanggal', $tanggal)
            ->get()
            ->sortBy(fn($p) => strtolower($p->karyawan->user->name ?? ''))
            ->values();

        // Semua karyawan aktif — diurutkan A-Z by nama via JOIN
        $semuaKaryawan = Karyawan::with(['user', 'jabatan', 'jadwalShift.shift'])
            ->where('status', 'aktif')
            ->join('users', 'users.id', '=', 'karyawan.users_id')
            ->orderByRaw('LOWER(users.name) ASC')
            ->select('karyawan.*')
            ->get();

        $totalKaryawan = $semuaKaryawan->count();

        // Karyawan alpa — urutan ikut $semuaKaryawan yang sudah A-Z
        $hadirIds     = $presensi->pluck('karyawan_id')->toArray();
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

    public function export(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        try {
            Carbon::parse($tanggal);
        } catch (\Exception $e) {
            $tanggal = Carbon::today()->toDateString();
        }

        $namaFile = 'Attendance_' . $tanggal . '.xlsx';

        return Excel::download(new AttendanceExport($tanggal), $namaFile);
    }

    public function exportMonthly(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::today()->format('Y-m'));

        try {
            Carbon::createFromFormat('Y-m', $bulan);
        } catch (\Exception $e) {
            $bulan = Carbon::today()->format('Y-m');
        }

        $label    = Carbon::createFromFormat('Y-m', $bulan)->locale('id')->isoFormat('MMMM_YYYY');
        $namaFile = "Attendance_{$label}.xlsx";

        return Excel::download(new AttendanceMonthlyExport($bulan), $namaFile);
    }
}
