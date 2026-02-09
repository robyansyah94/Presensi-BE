<?php

use App\Http\Controllers\Admin\QrPresensiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');


Route::get('/admin/qr-presensi', [QrPresensiController::class, 'index']);
Route::get('/admin/generate-qr', [QrPresensiController::class, 'generate']);

