<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatans = \App\Models\Jabatan::latest()->get();

        return view('admin.jabatan.index', compact('jabatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jabatan = \App\Models\Jabatan::findOrFail($id);

        //ulah di hapus jika masih ada karyawan yang menggunakan jabatan ini
        if ($jabatan->karyawan()->count() > 0) {
            return back()->with('error', 'Jabatan tidak bisa dihapus karena masih digunakan karyawan.');
        }

        $jabatan->delete();

        return redirect()->route('jabatan.index')
            ->with('success', 'Data jabatan berhasil dihapus.');
    }
}
