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

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return redirect()->route('shift.index')
            ->with('success', 'Data shift berhasil dihapus.');
    }
}
