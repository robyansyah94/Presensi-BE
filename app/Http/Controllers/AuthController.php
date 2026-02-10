<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Cek login
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        $user = auth()->user();

        // Hanya karyawan boleh login ke Vue
        if ($user->role !== 'karyawan') {
            return response()->json([
                'message' => 'Akses hanya untuk karyawan'
            ], 403);
        }

        // Buat token Sanctum
        $token = $user->createToken('karyawan-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token
        ]);
    }
}
