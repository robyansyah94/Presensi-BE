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
            $token = Str::random(10);
            $expiredAt = now()->addSeconds(10);

            QrPresensi::where('is_active', true)->update([
                'is_active' => false
            ]);

            QrPresensi::create([
                'qr_token' => $token,
                'expired_at' => $expiredAt,
                'is_active' => true
            ]);

            $qrCode = QrCode::format('svg')
                ->size(300)
                ->generate($token);

            return response($qrCode)
                ->header('Content-Type', 'image/svg+xml');
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }
}
