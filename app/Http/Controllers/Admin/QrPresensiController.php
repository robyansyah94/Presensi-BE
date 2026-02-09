<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrPresensiController extends Controller
{
    public function index()
    {
        return view('admin.qr-presensi');
    }

    public function generate()
    {
        $token = md5(now()->timestamp . rand());

        Cache::put('qr_token', $token, 10);

        return QrCode::size(300)->generate($token);
    }
}
