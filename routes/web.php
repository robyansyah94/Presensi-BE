<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\QrPresensiController;
use App\Http\Controllers\Admin\KaryawanController;


Route::get('/', function () {
    return redirect()->route('admin.login');
});


Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
});


Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    // QR Presensi
    Route::get('/qr-presensi', [QrPresensiController::class, 'index'])->name('admin.qr.index');
    Route::get('/generate-qr', [QrPresensiController::class, 'generate'])->name('admin.qr.generate');
    // Karyawan
    Route::resource('/karyawan', KaryawanController::class);
});
