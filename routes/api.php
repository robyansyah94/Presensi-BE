<?php

use App\Http\Controllers\Api\QrTokenController;
use App\Http\Controllers\Api\PresensiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/presensi/scan', [PresensiController::class, 'scan']);

Route::middleware('auth:sanctum')->post('/scan-qr', [PresensiController::class, 'scan']);