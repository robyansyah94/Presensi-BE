<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login.login');
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password salah');
        }

        if ($user->role !== 'admin') {
            return back()->with('error', 'Bukan admin');
        }

        Auth::login($user);

        return redirect('/admin/dashboard');
    }

    public function dashboard()
    {
        return view('admin.dashboard');

    }
    
    public function karyawan()
    {
        return view('admin.karyawan.karyawan');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
}
