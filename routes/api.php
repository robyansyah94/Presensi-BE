<?php

use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->post('/presensi/scan', [PresensiController::class, 'scan']);

Route::post('/login', [AuthController::class, 'login']);