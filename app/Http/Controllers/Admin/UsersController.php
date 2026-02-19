<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class UsersController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->role; // ambil parameter ?role=

        $query = User::with('karyawan.jabatan');

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->latest()->get();

        return view('admin.users.index', compact('users', 'role'));
    }

    public function create()
    {
        $jabatan = Jabatan::all();
        return view('admin.users.create', compact('jabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
            'role'       => 'required|in:admin,karyawan',
            'nip'        => 'nullable|string|max:50',
            'jabatan_id' => 'nullable|exists:jabatan,id',
            'no_hp'      => 'nullable|string|max:15',
            'alamat'     => 'nullable|string',
            'foto'       => 'nullable|image|max:2048',
            'status'     => 'nullable|in:aktif,nonaktif',
        ]);

        DB::transaction(function () use ($request) {

            // 1️⃣ Buat user dulu
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
                'role'     => $request->role,
            ]);

            // 2️⃣ Kalau role karyawan baru buat detail karyawan
            if ($request->role === 'karyawan') {

                $fotoPath = null;

                if ($request->hasFile('foto')) {
                    $fotoPath = $request->file('foto')->store('karyawan', 'public');
                }

                Karyawan::create([
                    'users_id'   => $user->id,
                    'jabatan_id' => $request->jabatan_id,
                    'nip'        => $request->nip,
                    'no_hp'      => $request->no_hp,
                    'alamat'     => $request->alamat,
                    'foto'       => $fotoPath,
                    'status'     => $request->status ?? 'aktif',
                ]);
            }
        });
        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $jabatan = Jabatan::all();
        $user->load('karyawan');

        return view('admin.users.edit', compact('user', 'jabatan'));
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'nip' => 'nullable',
            'alamat' => 'nullable',
            'jabatan_id' => 'nullable',
            'no_hp' => 'nullable',
            'foto' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        DB::transaction(function () use ($request, $user) {

            // 1️⃣ Update user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // 2️⃣ Kalau dia punya karyawan
            if ($user->karyawan) {

                $karyawan = $user->karyawan;

                $fotoPath = $karyawan->foto;

                if ($request->hasFile('foto')) {

                    // hapus foto lama
                    if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                        Storage::disk('public')->delete($fotoPath);
                    }

                    $fotoPath = $request->file('foto')->store('karyawan', 'public');
                }

                $karyawan->update([
                    'jabatan_id' => $request->jabatan_id,
                    'nip' => $request->nip,
                    'no_hp' => $request->no_hp,
                    'alamat' => $request->alamat,
                    'foto' => $fotoPath
                ]);
            }
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }


    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {

            // 1️⃣ Kalau punya data karyawan
            if ($user->karyawan) {

                $karyawan = $user->karyawan;

                if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
                    Storage::disk('public')->delete($karyawan->foto);
                }
            }

            // 2️⃣ Hapus user (karyawan akan ikut terhapus karena cascade)
            $user->delete();
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
