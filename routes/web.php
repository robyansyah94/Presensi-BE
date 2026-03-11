<?php

use App\Http\Controllers\Admin\AssessmentCategoryController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\JabatanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\QrPresensiController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\JadwalShiftController;
use App\Http\Controllers\Admin\LokasiKantorController;
use App\Http\Controllers\Admin\UsersController;

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

    // Users
    Route::resource('/users', UsersController::class);

    // jabatn
    Route::resource('/jabatan', JabatanController::class);

    // shift
    Route::resource('/shift', ShiftController::class);

    // jadwal shift
    Route::prefix('jadwal-shift')->name('jadwal-shift.')->group(function () {
        Route::get('/', [JadwalShiftController::class, 'index'])->name('index');
        Route::post('/store', [JadwalShiftController::class, 'store'])->name('store');
        Route::get('/preview', [JadwalShiftController::class, 'preview'])->name('preview');
    });

    //attendance
    Route::get('/admin/attendance/history', [AttendanceController::class, 'history'])
        ->name('attendance.history');

    // Lokasi Kantor
    Route::resource('/lokasi-kantor', LokasiKantorController::class)
        ->except(['show']);

    // Export attendance
    Route::get('/admin/attendance/export', [AttendanceController::class, 'export'])
        ->name('attendance.export');

    // Export attendance bulanan
    Route::get('/admin/attendance/export/monthly', [AttendanceController::class, 'exportMonthly'])
        ->name('attendance.export.monthly');


    // -----IEU PENILAIAN-----------------------------------

    // Assessment - Kategori
    Route::prefix('assessment')->name('admin.assessment.')->group(function () {
        // Resource otomatis generate: index, create, store, show, edit, update, destroy
        Route::resource('categories', AssessmentCategoryController::class)
            ->except(['show']); // tidak pakai halaman show/detail

        //route custom untuk toggle aktif/nonaktif
        Route::patch('categories/{category}/toggle', [AssessmentCategoryController::class, 'toggleActive'])
            ->name('categories.toggle');
    });
});
