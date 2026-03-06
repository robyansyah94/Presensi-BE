<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrPresensi;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrPresensiController extends Controller
{
    public function index()
    {
        return view('admin.qr-presensi');
    }

    public function generate()
    {
        try {
            // DELETE semua record lama → tabel selalu bersih
            QrPresensi::query()->delete();

            $token = Str::random(32); // 32 karakter lebih aman dari 10

            QrPresensi::create([
                'qr_token'   => $token,
                'expired_at' => now()->addSeconds(10), // tetap 10 detik (dinamis)
                'is_active'  => true,
            ]);

            $qrCode = QrCode::format('svg')
                ->size(300)
                ->generate($token);

            return response($qrCode)
                ->header('Content-Type', 'image/svg+xml');
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
