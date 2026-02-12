<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::with(['user','jabatan'])->latest()->get();
        return view('admin.karyawan.karyawan', compact('karyawan'));
    }

    public function create()
    {
        $jabatan = Jabatan::all();
        return view('admin.karyawan.create_karyawan', compact('jabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'nip' => 'required',
            'jabatan_id' => 'required',
            'no_hp' => 'required',
            'foto' => 'image|mimes:jpg,png,jpeg'
        ]);

        // 1. Buat user login
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('12345678'),
            'role' => 'karyawan'
        ]);

        // 2. Upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('karyawan','public');
        }

        // 3. Buat data karyawan
        Karyawan::create([
            'users_id' => $user->id,
            'jabatan_id' => $request->jabatan_id,
            'nip' => $request->nip,
            'no_hp' => $request->no_hp,
            'foto' => $fotoPath
        ]);

        return redirect()->route('karyawan.index')->with('success','Berhasil tambah karyawan');
    }
}

