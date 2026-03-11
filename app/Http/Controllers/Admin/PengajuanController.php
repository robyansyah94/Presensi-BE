<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalShift;
use App\Models\Pengajuan;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    // GET /admin/pengajuan
    // List semua pengajuan dengan filter status.
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $pengajuan = Pengajuan::with(['karyawan.user', 'karyawan.jabatan', 'disetujuiOleh'])
            ->when($status !== 'semua', fn($q) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->get();

        $counts = [
            'pending'   => Pengajuan::where('status', 'pending')->count(),
            'disetujui' => Pengajuan::where('status', 'disetujui')->count(),
            'ditolak'   => Pengajuan::where('status', 'ditolak')->count(),
        ];

        return view('admin.pengajuan.index', compact('pengajuan', 'status', 'counts'));
    }

    
    //   POST /admin/pengajuan/{id}/approve
    //   Setujui pengajuan → otomatis insert presensi per hari.
    public function approve(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('karyawan')->findOrFail($id);

        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($pengajuan) {
            // Update status pengajuan
            $pengajuan->update([
                'status'        => 'disetujui',
                'disetujui_oleh' => Auth::id(),
            ]);

            // Insert presensi per hari dalam range tanggal
            $current = Carbon::parse($pengajuan->tanggal_mulai);
            $end     = Carbon::parse($pengajuan->tanggal_selesai);

            while ($current->lte($end)) {
                $tanggal = $current->toDateString();

                // Ambil shift karyawan di tanggal tersebut
                $jadwal = JadwalShift::where('karyawan_id', $pengajuan->karyawan_id)
                    ->where('tanggal_mulai', '<=', $tanggal)
                    ->where('tanggal_selesai', '>=', $tanggal)
                    ->first();

                // Cek apakah sudah ada presensi di tanggal tersebut
                $existing = Presensi::where('karyawan_id', $pengajuan->karyawan_id)
                    ->where('tanggal', $tanggal)
                    ->first();

                if ($existing) {
                    // Sudah ada presensi → update statusnya saja
                    $existing->update(['status' => $pengajuan->jenis]);
                } else {
                    // Belum ada → buat baris presensi baru
                    Presensi::create([
                        'karyawan_id' => $pengajuan->karyawan_id,
                        'shift_id'    => $jadwal?->shift_id,
                        'tanggal'     => $tanggal,
                        'jam_masuk'   => null,
                        'jam_pulang'  => null,
                        'status'      => $pengajuan->jenis, // izin / sakit / cuti
                        'latitude'    => null,
                        'longitude'   => null,
                    ]);
                }

                $current->addDay();
            }
        });

        return back()->with('success', 'Pengajuan berhasil disetujui dan presensi telah diperbarui.');
    }

    //   POST /admin/pengajuan/{id}/reject
    //   Tolak pengajuan.
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_tolak' => 'nullable|string|max:255',
        ]);

        $pengajuan = Pengajuan::findOrFail($id);

        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $pengajuan->update([
            'status'        => 'ditolak',
            'disetujui_oleh' => Auth::id(),
            'keterangan'    => $pengajuan->keterangan
                ? $pengajuan->keterangan . "\n[Ditolak: " . ($request->alasan_tolak ?? '-') . "]"
                : "[Ditolak: " . ($request->alasan_tolak ?? '-') . "]",
        ]);

        return back()->with('success', 'Pengajuan telah ditolak.');
    }
}
