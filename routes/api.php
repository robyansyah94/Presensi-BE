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
        'foto' => optional($user->karyawan)->foto,
        'jabatan' => optional(optional($user->karyawan)->jabatan)->nama_jabatan,
    ]);
});