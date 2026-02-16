<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::with(['user', 'jabatan'])->latest()->get();
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
            'alamat' => 'required',
            'jabatan_id' => 'required',
            'no_hp' => 'required',
            'foto' => 'image|mimes:jpg,png,jpeg'
        ]);

        try {
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
                $fotoPath = $request->file('foto')->store('karyawan', 'public');
            }

            // 3. Buat data karyawan
            Karyawan::create([
                'users_id' => $user->id,
                'alamat' => $request->alamat,
                'jabatan_id' => $request->jabatan_id,
                'nip' => $request->nip,
                'no_hp' => $request->no_hp,
                'foto' => $fotoPath
            ]);

            return redirect()
                ->route('karyawan.index')
                ->with('success', 'Berhasil tambah karyawan');
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', 'Gagal tambah karyawan');
        }
    }

    public function edit($id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);
        $jabatan = Jabatan::all();

        return view('admin.karyawan.edit_karyawan', compact('karyawan', 'jabatan'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $user = User::findOrFail($karyawan->users_id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'nip' => 'required',
            'alamat' => 'required',
            'jabatan_id' => 'required',
            'no_hp' => 'required',
            'foto' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        try {
            // 1️⃣ Update user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // 2️⃣ Upload foto baru (jika ada)
            $fotoPath = $karyawan->foto;

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('karyawan', 'public');
            }

            // 3️⃣ Update karyawan
            $karyawan->update([
                'jabatan_id' => $request->jabatan_id,
                'nip' => $request->nip,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'foto' => $fotoPath
            ]);

            return redirect()
                ->route('karyawan.index')
                ->with('success', 'Berhasil update karyawan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update karyawan');
        }
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        try {

            DB::transaction(function () use ($karyawan) {

                // 1️⃣ Hapus file foto jika ada
                if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
                    Storage::disk('public')->delete($karyawan->foto);
                }

                // 2️⃣ Hapus user login
                User::where('id', $karyawan->users_id)->delete();

                // 3️⃣ Hapus data karyawan
                $karyawan->delete();
            });

            return redirect()
                ->route('karyawan.index')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {

            return back()->with('error', 'Gagal menghapus data');
        }
    }
}
