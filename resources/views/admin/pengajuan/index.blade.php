@extends('admin.layouts.app')

@section('title', 'Pengajuan Karyawan')

@section('content')

<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">PENGAJUAN KARYAWAN</h4>
</div>

{{-- ── Alert ── --}}
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold flex items-center gap-2">
    <i class="material-symbols-rounded text-base">check_circle</i>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm font-semibold flex items-center gap-2">
    <i class="material-symbols-rounded text-base">error</i>
    {{ session('error') }}
</div>
@endif

{{-- ── Tab Filter ── --}}
<div class="flex gap-2 mb-5 flex-wrap">
    @foreach([
        ['pending',   'Menunggu',   'bg-amber-100 text-amber-700',  'bg-amber-500 text-white'],
        ['disetujui', 'Disetujui',  'bg-green-100 text-green-700',  'bg-green-500 text-white'],
        ['ditolak',   'Ditolak',    'bg-red-100 text-red-600',      'bg-red-500 text-white'],
        ['semua',     'Semua',      'bg-gray-100 text-gray-600',    'bg-gray-700 text-white'],
    ] as [$val, $label, $inactiveClass, $activeClass])
    <a href="{{ route('pengajuan.index', ['status' => $val]) }}"
        style="display:inline-flex; align-items:center; gap:6px; padding:7px 16px; border-radius:999px; font-size:13px; font-weight:600; text-decoration:none; transition:all .15s;"
        class="{{ $status === $val ? $activeClass : $inactiveClass }}">
        {{ $label }}
        @if($val !== 'semua')
        <span style="display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:50%; font-size:11px; font-weight:700;
            background:{{ $status === $val ? 'rgba(255,255,255,0.3)' : 'rgba(0,0,0,0.08)' }}">
            {{ $counts[$val] ?? 0 }}
        </span>
        @endif
    </a>
    @endforeach
</div>

{{-- ── Tabel ── --}}
<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr class="text-sm">
                    <th class="px-4 py-3 text-left">Karyawan</th>
                    <th class="px-4 py-3 text-left">Jenis</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Keterangan</th>
                    <th class="px-4 py-3 text-center">Bukti</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($pengajuan as $p)
                <tr class="hover:bg-gray-50 text-sm">

                    {{-- Karyawan --}}
                    <td class="px-4 py-3">
                        <div class="font-semibold text-default-800">{{ $p->karyawan->user->name ?? '-' }}</div>
                        <div class="text-xs text-default-400">{{ $p->karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                    </td>

                    {{-- Jenis --}}
                    <td class="px-4 py-3">
                        @php
                            $jenisMap = [
                                'izin'  => ['bg-blue-100 text-blue-700',   '📋'],
                                'sakit' => ['bg-red-100 text-red-600',     '🤒'],
                                'cuti'  => ['bg-purple-100 text-purple-700','🌴'],
                            ];
                            [$cls, $emoji] = $jenisMap[$p->jenis] ?? ['bg-gray-100 text-gray-600','📄'];
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $cls }}">
                            {{ $emoji }} {{ ucfirst($p->jenis) }}
                        </span>
                    </td>

                    {{-- Tanggal --}}
                    <td class="px-4 py-3 text-default-600">
                        @if($p->tanggal_mulai->eq($p->tanggal_selesai))
                            {{ $p->tanggal_mulai->translatedFormat('d M Y') }}
                        @else
                            {{ $p->tanggal_mulai->translatedFormat('d M Y') }}<br>
                            <span class="text-default-400 text-xs">s/d {{ $p->tanggal_selesai->translatedFormat('d M Y') }}</span>
                        @endif
                    </td>

                    {{-- Keterangan --}}
                    <td class="px-4 py-3 text-default-600 max-w-[200px]">
                        <span class="line-clamp-2 text-xs">{{ $p->keterangan ?? '-' }}</span>
                    </td>

                    {{-- Bukti --}}
                    <td class="px-4 py-3 text-center">
                        @if($p->bukti)
                        <a href="{{ asset('storage/' . $p->bukti) }}" target="_blank"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:underline">
                            <i class="material-symbols-rounded text-sm">attach_file</i> Lihat
                        </a>
                        @else
                        <span class="text-default-300 text-xs">-</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3 text-center">
                        @if($p->status === 'pending')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block animate-pulse"></span>Menunggu
                        </span>
                        @elseif($p->status === 'disetujui')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>Disetujui
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>Ditolak
                        </span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-3 text-center">
                        @if($p->status === 'pending')
                        <div class="flex items-center justify-center gap-2">

                            {{-- Tombol Setujui --}}
                            <form method="POST" action="{{ route('pengajuan.approve', $p->id) }}"
                                onsubmit="return confirm('Setujui pengajuan {{ ucfirst($p->jenis) }} dari {{ $p->karyawan->user->name ?? '' }}?')">
                                @csrf
                                <button type="submit"
                                    class="btn rounded-full bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1.5 flex items-center gap-1">
                                    <i class="material-symbols-rounded text-sm">check_circle</i> Setujui
                                </button>
                            </form>

                            {{-- Tombol Tolak --}}
                            <button type="button"
                                onclick="showTolakModal({{ $p->id }}, '{{ addslashes($p->karyawan->user->name ?? '') }}')"
                                class="btn rounded-full border border-red-400 text-red-500 hover:bg-red-500 hover:text-white text-xs px-3 py-1.5 flex items-center gap-1">
                                <i class="material-symbols-rounded text-sm">cancel</i> Tolak
                            </button>

                        </div>
                        @else
                        <span class="text-xs text-default-400">
                            {{ $p->disetujuiOleh?->name ?? '-' }}
                        </span>
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-14 text-center text-default-400">
                        <i class="material-symbols-rounded text-4xl mb-2 block">inbox</i>
                        Tidak ada pengajuan {{ $status !== 'semua' ? $status : '' }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Modal Tolak ── --}}
<div id="modal-tolak"
    style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div onclick="closeTolakModal()"
        style="position:absolute; inset:0; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);"></div>
    <div style="position:relative; background:#fff; border-radius:16px; width:100%; max-width:420px; box-shadow:0 25px 60px rgba(0,0,0,0.2); animation:modalIn .2s ease;">
        <div style="padding:20px 24px 16px; border-bottom:1px solid #f1f5f9;">
            <div style="font-size:16px; font-weight:700; color:#0f172a;">Tolak Pengajuan</div>
            <div id="tolak-subtitle" style="font-size:13px; color:#94a3b8; margin-top:4px;"></div>
        </div>
        <form id="form-tolak" method="POST">
            @csrf
            <div style="padding:20px 24px;">
                <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px; text-transform:uppercase; letter-spacing:.5px;">
                    Alasan Penolakan <span style="color:#94a3b8; font-weight:400;">(opsional)</span>
                </label>
                <textarea name="alasan_tolak" rows="3" placeholder="Tuliskan alasan penolakan..."
                    style="width:100%; border:1.5px solid #e2e8f0; border-radius:10px; padding:10px 14px; font-size:13px; color:#1e293b; resize:none; outline:none; box-sizing:border-box; font-family:inherit;"
                    onfocus="this.style.borderColor='#ef4444'"
                    onblur="this.style.borderColor='#e2e8f0'"></textarea>
            </div>
            <div style="padding:0 24px 20px; display:flex; gap:10px;">
                <button type="button" onclick="closeTolakModal()"
                    style="flex:1; padding:10px; border-radius:10px; border:1.5px solid #e2e8f0; background:#fff; font-size:13px; font-weight:600; color:#64748b; cursor:pointer;">
                    Batal
                </button>
                <button type="submit"
                    style="flex:1; padding:10px; border-radius:10px; border:none; background:#ef4444; font-size:13px; font-weight:700; color:#fff; cursor:pointer;">
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<style>
    @keyframes modalIn {
        from { opacity:0; transform:translateY(12px) scale(.98); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
</style>
<script>
    function showTolakModal(id, nama) {
        document.getElementById('tolak-subtitle').textContent = 'Pengajuan dari ' + nama;
        document.getElementById('form-tolak').action = '/admin/pengajuan/' + id + '/reject';
        const modal = document.getElementById('modal-tolak');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeTolakModal() {
        document.getElementById('modal-tolak').style.display = 'none';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeTolakModal(); });
</script>
@endpush