<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiKantor;
use Illuminate\Http\Request;

class LokasiKantorController extends Controller
{
    public function index()
    {
        $lokasiKantors = LokasiKantor::latest()->get();
        return view('admin.lokasi-kantor.index', compact('lokasiKantors'));
    }

    public function create()
    {
        return view('admin.lokasi-kantor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kantor'  => 'required|string|max:255',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:1',
        ]);

        LokasiKantor::create($request->only([
            'nama_kantor',
            'latitude',
            'longitude',
            'radius_meter'
        ]));

        return redirect()->route('lokasi-kantor.index')
            ->with('success', 'Lokasi kantor berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $lokasiKantor = LokasiKantor::findOrFail($id);
        return view('admin.lokasi-kantor.edit', compact('lokasiKantor'));
    }

    public function update(Request $request, string $id)
    {
        $lokasiKantor = LokasiKantor::findOrFail($id);

        $request->validate([
            'nama_kantor'  => 'required|string|max:255',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:1',
        ]);

        $lokasiKantor->update($request->only([
            'nama_kantor',
            'latitude',
            'longitude',
            'radius_meter'
        ]));

        return redirect()->route('lokasi-kantor.index')
            ->with('success', 'Lokasi kantor berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $lokasiKantor = LokasiKantor::findOrFail($id);
        $lokasiKantor->delete();

        return redirect()->route('lokasi-kantor.index')
            ->with('success', 'Lokasi kantor berhasil dihapus.');
    }
}