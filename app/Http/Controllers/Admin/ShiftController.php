<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::orderBy('id')->get();
        return view('admin.shift.index', compact('shifts'));
    }

    public function create()
    {
        return view('admin.shift.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_shift' => 'required|string|max:100',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'toleransi_telat' => 'required|integer'
        ]);

        Shift::create([
            'nama_shift' => $request->nama_shift,
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
            'toleransi_telat' => $request->toleransi_telat
        ]);

        return redirect()->route('shift.index')
            ->with('success', 'Data shift berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $shift = Shift::findOrFail($id);
        return view('admin.shift.edit', compact('shift'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_shift' => 'required|string|max:100',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'toleransi_telat' => 'required|integer'
        ]);

        $shift = Shift::findOrFail($id);
        
        $shift->update([
            'nama_shift' => $request->nama_shift,
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
            'toleransi_telat' => $request->toleransi_telat
        ]);

        return redirect()->route('shift.index')
            ->with('success', 'Data shift berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return redirect()->route('shift.index')
            ->with('success', 'Data shift berhasil dihapus.');
    }
}
