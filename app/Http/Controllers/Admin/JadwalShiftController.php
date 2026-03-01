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