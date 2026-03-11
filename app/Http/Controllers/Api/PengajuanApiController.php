<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanApiController extends Controller
{
    /**
     * GET /api/pengajuan
     * List semua pengajuan milik karyawan yang login.
     */
    public function index(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $pengajuan = Pengajuan::where('karyawan_id', $karyawan->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => $this->formatPengajuan($p));

        return response()->json(['data' => $pengajuan]);
    }

    /**
     * POST /api/pengajuan
     * Karyawan submit pengajuan baru.
     */
    public function store(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $request->validate([
            'jenis'           => 'required|in:izin,sakit,cuti',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'nullable|string|max:500',
            'bukti'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('pengajuan', 'public');
        }

        $pengajuan = Pengajuan::create([
            'karyawan_id'     => $karyawan->id,
            'jenis'           => $request->jenis,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan'      => $request->keterangan,
            'bukti'           => $buktiPath,
            'status'          => 'pending',
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil dikirim dan menunggu persetujuan admin.',
            'data'    => $this->formatPengajuan($pengajuan),
        ], 201);
    }

    // ── Format response ───────────────────────────────────────────────────────
    private function formatPengajuan(Pengajuan $p): array
    {
        return [
            'id'              => $p->id,
            'jenis'           => $p->jenis,
            'tanggal_mulai'   => $p->tanggal_mulai->format('Y-m-d'),
            'tanggal_selesai' => $p->tanggal_selesai->format('Y-m-d'),
            'keterangan'      => $p->keterangan,
            'bukti_url'       => $p->bukti_url,
            'status'          => $p->status,
            'created_at'      => $p->created_at->format('Y-m-d H:i'),
        ];
    }
}
