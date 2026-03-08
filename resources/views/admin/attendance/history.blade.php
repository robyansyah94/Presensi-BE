@extends('admin.layouts.app')

@section('title', 'History Attendance')

@section('content')

{{-- Page Title --}}
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">HISTORY ATTENDANCE</h4>
    <span class="text-sm text-default-500">
        Menampilkan data presensi tanggal:
        <span id="label-tanggal" class="font-semibold text-default-800">
            {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
        </span>
    </span>
</div>

<div style="display:flex; flex-direction:row; gap:20px; align-items:flex-start;">

    {{-- ── KOLOM KIRI: KALENDER ─────────────────────────────────────── --}}
    <div style="width:288px; flex-shrink:0;">
        <div class="card p-4">
            {{-- Header Bulan --}}
            <div class="flex items-center justify-between mb-4">
                <button id="btn-prev"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition text-default-600">
                    <i class="material-symbols-rounded" style="font-size:20px;">chevron_left</i>
                </button>
                <span id="cal-header" class="text-sm font-semibold text-default-800"></span>
                <button id="btn-next"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition text-default-600">
                    <i class="material-symbols-rounded" style="font-size:20px;">chevron_right</i>
                </button>
            </div>

            {{-- Nama Hari --}}
            <div style="display:grid; grid-template-columns:repeat(7,1fr); margin-bottom:4px;">
                @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $hari)
                <div class="text-center text-xs font-medium text-default-400 py-1">{{ $hari }}</div>
                @endforeach
            </div>

            {{-- Grid Tanggal (diisi JS) --}}
            <div id="cal-grid" style="display:grid; grid-template-columns:repeat(7,1fr); row-gap:2px;"></div>
        </div>
    </div>

    <!-- Tabel history Presensi -->
    <div style="flex:1; min-width:0;">
        <div class="card">

            {{-- Info ringkas di atas tabel --}}
            <div class="px-4 py-3 border-b border-default-200 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-4 text-sm">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
                        <span class="text-default-600">Hadir:
                            <strong id="count-hadir">{{ $presensi->where('status','hadir')->count() }}</strong>
                        </span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 inline-block"></span>
                        <span class="text-default-600">Terlambat:
                            <strong id="count-terlambat">{{ $presensi->where('status','terlambat')->count() }}</strong>
                        </span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span>
                        <span class="text-default-600">Alpa:
                            <strong id="count-alpa">{{ $totalKaryawan - $presensi->whereIn('status',['hadir','terlambat'])->count() }}</strong>
                        </span>
                    </span>
                </div>
                <span class="text-xs text-default-400">Total karyawan: {{ $totalKaryawan }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100 border-b">
                        <tr class="text-sm">
                            <th class="px-4 py-3 text-left">Karyawan</th>
                            <th class="px-4 py-3 text-left">NIP</th>
                            <th class="px-4 py-3 text-left">Jabatan</th>
                            <th class="px-4 py-3 text-left">Shift</th>
                            <th class="px-4 py-3 text-left">Jam Masuk</th>
                            <th class="px-4 py-3 text-left">Jam Keluar</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y" id="tabel-body">

                        {{-- ── Karyawan yang HADIR ─────────────────── --}}
                        @foreach($presensi as $i => $p)
                        <tr class="hover:bg-gray-50 text-sm">

                            {{-- NAMA --}}
                            <td class="px-4 py-3">
                                <span class="font-semibold text-default-800">
                                    {{ $p->karyawan->user->name ?? '-' }}
                                </span>
                            </td>

                            {{-- NIP --}}
                            <td class="px-4 py-3 text-default-600">
                                {{ $p->karyawan->nip ?? '-' }}
                            </td>

                            {{-- JABATAN --}}
                            <td class="px-4 py-3 text-default-600">
                                {{ $p->karyawan->jabatan->nama_jabatan ?? '-' }}
                            </td>

                            {{-- SHIFT --}}
                            <td class="px-4 py-3">
                                @if($p->shift)
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ $p->shift->nama_shift }}
                                </span>
                                @else
                                <span class="text-default-400">-</span>
                                @endif
                            </td>

                            {{-- WAKTU MASUK --}}
                            <td class="px-4 py-3 text-default-600">
                                {{ $p->jam_masuk ? \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') : '-' }}
                            </td>

                            {{-- WAKTU KELUAR --}}
                            <td class="px-4 py-3 text-default-600">
                                {{ $p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') : '-' }}
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-3 text-center">
                                @if($p->status === 'hadir')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                    Hadir
                                </span>
                                @elseif($p->status === 'terlambat')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 inline-block"></span>
                                    Terlambat
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>
                                    Tidak Hadir
                                </span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                    class="btn rounded-full border border-info text-info hover:bg-info hover:text-white text-xs">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        {{-- ── Karyawan yang ALPA (tidak ada record presensi) ── --}}
                        @foreach($karyawanAlpa as $i => $k)
                        <tr class="hover:bg-gray-50 text-sm">

                            <td class="px-4 py-3">
                                <span class="font-semibold text-default-800">
                                    {{ $k->user->name ?? '-' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-default-600">{{ $k->nip ?? '-' }}</td>
                            <td class="px-4 py-3 text-default-600">{{ $k->jabatan->nama_jabatan ?? '-' }}</td>

                            {{-- Shift: cari dari jadwal_shift --}}
                            <td class="px-4 py-3">
                                @php
                                $jadwal = $k->jadwalShift
                                ->first(fn($j) =>
                                $j->tanggal_mulai <= $tanggal &&
                                    $j->tanggal_selesai >= $tanggal
                                    );
                                    @endphp
                                    @if($jadwal?->shift)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        {{ $jadwal->shift->nama_shift }}
                                    </span>
                                    @else
                                    <span class="text-default-400">-</span>
                                    @endif
                            </td>

                            <td class="px-4 py-3 text-default-400">-</td>
                            <td class="px-4 py-3 text-default-400">-</td>

                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>
                                    Alpa
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                    class="btn rounded-full border border-info text-info hover:bg-info hover:text-white text-xs">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        {{-- Empty state --}}
                        @if($presensi->isEmpty() && empty($karyawanAlpa))
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-default-400">
                                <i class="material-symbols-rounded text-4xl mb-2 block">event_busy</i>
                                Tidak ada data presensi pada tanggal ini.
                            </td>
                        </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<style>
    .cal-day:hover {
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
    }
</style>
<script>
    // ── DATA DARI BLADE ────────────────────────────────────────────────────────
    const SELECTED_DATE = '{{ $tanggal }}'; // format: YYYY-MM-DD
    const BASE_URL = '{{ route("attendance.history") }}';

    // ── KALENDER ───────────────────────────────────────────────────────────────
    const BULAN_ID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    let curYear, curMonth;

    // Inisialisasi dari tanggal yang sedang aktif
    (function init() {
        const d = new Date(SELECTED_DATE + 'T00:00:00');
        curYear = d.getFullYear();
        curMonth = d.getMonth();
        renderCalendar();
    })();

    document.getElementById('btn-prev').addEventListener('click', () => {
        curMonth--;
        if (curMonth < 0) {
            curMonth = 11;
            curYear--;
        }
        renderCalendar();
    });

    document.getElementById('btn-next').addEventListener('click', () => {
        curMonth++;
        if (curMonth > 11) {
            curMonth = 0;
            curYear++;
        }
        renderCalendar();
    });

    function renderCalendar() {
        document.getElementById('cal-header').textContent =
            BULAN_ID[curMonth] + ' ' + curYear;

        const grid = document.getElementById('cal-grid');
        grid.innerHTML = '';

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const selected = new Date(SELECTED_DATE + 'T00:00:00');

        // Hari pertama bulan ini (0=Min, 1=Sen, dst)
        const firstDay = new Date(curYear, curMonth, 1).getDay();
        const daysInMonth = new Date(curYear, curMonth + 1, 0).getDate();

        // Padding kosong di awal
        for (let i = 0; i < firstDay; i++) {
            grid.insertAdjacentHTML('beforeend', '<div></div>');
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const thisDate = new Date(curYear, curMonth, d);
            thisDate.setHours(0, 0, 0, 0);

            const yyyy = curYear;
            const mm = String(curMonth + 1).padStart(2, '0');
            const dd = String(d).padStart(2, '0');
            const iso = `${yyyy}-${mm}-${dd}`;

            const isToday = thisDate.getTime() === today.getTime();
            const isSelected = thisDate.getTime() === selected.getTime();

            // Base style pakai inline CSS agar tidak bergantung Tailwind JIT
            let baseStyle = 'display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:12px;margin:0 auto;transition:background .15s,color .15s;';
            let cls, style;

            if (isSelected) {
                cls = '';
                style = baseStyle + 'background:#4f46e5;color:#fff;font-weight:700;box-shadow:0 1px 4px rgba(79,70,229,.4);';
            } else if (isToday) {
                cls = '';
                style = baseStyle + 'background:#e5e7eb;color:#1f2937;font-weight:600;';
            } else {
                cls = 'cal-day'; // hover via <style> block
                style = baseStyle + 'color:#4b5563;';
            }

            grid.insertAdjacentHTML('beforeend',
                `<div class="${cls}" data-date="${iso}" style="${style}">${d}</div>`
            );
        }

        // Event klik tanggal → navigasi ke halaman dengan tanggal baru
        grid.querySelectorAll('[data-date]').forEach(el => {
            el.addEventListener('click', function() {
                const date = this.dataset.date;
                window.location.href = BASE_URL + '?tanggal=' + date;
            });
        });
    }
</script>
@endpush