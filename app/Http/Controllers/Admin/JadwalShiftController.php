<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\JadwalShift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalShiftController extends Controller
{
    public function index(Request $request)
    {
        $karyawans = Karyawan::with('user')->get();
        $shifts = Shift::all();

        return view('admin.jadwal_shift.index', compact('karyawans', 'shifts'));
    }

    // Menampilkan jadwal shift berdasarkan tanggal mulai yang dipilih
    public function preview(Request $request)
    {
        $karyawans = [];
        $dates = [];

        if ($request->tanggal_mulai && $request->tanggal_selesai) {

            $startDate = Carbon::parse($request->tanggal_mulai);
            $endDate = Carbon::parse($request->tanggal_selesai);

            $dates = [];

            while ($startDate <= $endDate) {
                $dates[] = $startDate->copy();
                $startDate->addDay();
            }

            $karyawans = Karyawan::with(['user'])->get()->map(function ($karyawan) use ($dates) {

                $schedule = [];

                foreach ($dates as $date) {

                    $jadwal = JadwalShift::where('karyawan_id', $karyawan->id)
                        ->where('tanggal_mulai', '<=', $date)
                        ->where('tanggal_selesai', '>=', $date)
                        ->with('shift')
                        ->first();

                    $schedule[$date->format('Y-m-d')] = $jadwal;
                }

                $karyawan->weekly_schedule = $schedule;

                return $karyawan;
            });
        }

        return view('admin.jadwal_shift.preview', compact('karyawans', 'dates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'shift' => 'required|array'
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);

        // cek apakah ada Sabtu / Minggu di periode
        $period = \Carbon\CarbonPeriod::create($tanggalMulai, $tanggalSelesai);

        foreach ($period as $tanggal) {

            if ($tanggal->isWeekend()) {

                return back()->with(
                    'error',
                    'Tidak bisa membuat jadwal pada hari Sabtu dan Minggu.'
                );
            }
        }

        // simpan jadwal shift
        foreach ($request->shift as $karyawan_id => $shift_id) {

            JadwalShift::updateOrCreate(
                [
                    'karyawan_id' => $karyawan_id,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai
                ],
                [
                    'shift_id' => $shift_id
                ]
            );
        }
        return back()->with('success', 'Jadwal shift berhasil disimpan.');
    }
}
