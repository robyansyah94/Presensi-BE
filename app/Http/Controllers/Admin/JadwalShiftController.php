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

        if ($request->start_date) {

            $startDate = \Carbon\Carbon::parse($request->start_date);

            if (!$startDate->isMonday()) {
                return back()->with('error', 'Tanggal harus hari Senin!');
            }

            $dates = collect(range(0, 4))->map(function ($i) use ($startDate) {
                return $startDate->copy()->addDays($i);
            });

            $karyawans = \App\Models\Karyawan::with(['user', 'jadwalShift.shift'])
                ->get()
                ->map(function ($karyawan) use ($dates) {

                    $schedule = [];

                    foreach ($dates as $date) {
                        $jadwal = $karyawan->jadwalShift
                            ->where('tanggal', $date->format('Y-m-d'))
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
            'start_date' => 'required|date',
            'shift' => 'required|array'
        ]);

        $startDate = Carbon::parse($request->start_date);

        // Pastikan hari Senin
        if (!$startDate->isMonday()) {
            return back()->with('error', 'Tanggal harus hari Senin!');
        }

        foreach ($request->shift as $karyawan_id => $shift_id) {

            for ($i = 0; $i < 5; $i++) {

                $tanggal = $startDate->copy()->addDays($i);

                JadwalShift::updateOrCreate(
                    [
                        'karyawan_id' => $karyawan_id,
                        'tanggal' => $tanggal
                    ],
                    [
                        'shift_id' => $shift_id
                    ]
                );
            }
        }

        return back()->with('success', 'Jadwal minggu berhasil disimpan.');
    }
}
