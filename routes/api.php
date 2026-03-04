<?php

use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->post('/presensi/scan', [PresensiController::class, 'scan']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {

    $user = $request->user()->load('karyawan.jabatan');

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'nip' => optional($user->karyawan)->nip,
        'no_hp' => optional($user->karyawan)->no_hp,
        'alamat' => optional($user->karyawan)->alamat,
        'tanggal_bergabung' => optional(optional($user->karyawan)->created_at)->format('Y-m-d'),
        'foto' => optional($user->karyawan)->foto,
        'jabatan' => optional(optional($user->karyawan)->jabatan)->nama_jabatan,
    ]);
});
