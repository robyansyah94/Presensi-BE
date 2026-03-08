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
            <div class="flex items-center justify-between mb-4">
                <button id="btn-prev" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition text-default-600">
                    <i class="material-symbols-rounded" style="font-size:20px;">chevron_left</i>
                </button>
                <span id="cal-header" class="text-sm font-semibold text-default-800"></span>
                <button id="btn-next" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition text-default-600">
                    <i class="material-symbols-rounded" style="font-size:20px;">chevron_right</i>
                </button>
            </div>

            <div style="display:grid; grid-template-columns:repeat(7,1fr); margin-bottom:4px;">
                @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $hari)
                <div class="text-center text-xs font-medium text-default-400 py-1">{{ $hari }}</div>
                @endforeach
            </div>

            <div id="cal-grid" style="display:grid; grid-template-columns:repeat(7,1fr); row-gap:2px;"></div>

            <div class="mt-4 pt-3 border-t border-default-200">
                <a id="btn-export"
                    href="{{ route('attendance.export', ['tanggal' => $tanggal]) }}"
                    style="display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:9px 0; border-radius:8px; background:#16a34a; color:#fff; font-size:13px; font-weight:600; text-decoration:none; transition:background .15s;"
                    onmouseover="this.style.background='#15803d'"
                    onmouseout="this.style.background='#16a34a'">
                    <i class="material-symbols-rounded" style="font-size:18px;">download</i>
                    Export Excel
                </a>
                <p class="text-xs text-default-400 text-center mt-2">
                    Data tanggal: <span class="font-medium text-default-600">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ── KOLOM KANAN: TABEL ───────────────────────────────────────── --}}
    <div style="flex:1; min-width:0;">
        <div class="card">

            <div class="px-4 py-3 border-b border-default-200 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-4 text-sm">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
                        <span class="text-default-600">Hadir: <strong>{{ $presensi->where('status','hadir')->count() }}</strong></span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 inline-block"></span>
                        <span class="text-default-600">Terlambat: <strong>{{ $presensi->where('status','terlambat')->count() }}</strong></span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span>
                        <span class="text-default-600">Alpa: <strong>{{ $totalKaryawan - $presensi->whereIn('status',['hadir','terlambat'])->count() }}</strong></span>
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
                            <th class="px-4 py-3 text-left">Waktu Masuk</th>
                            <th class="px-4 py-3 text-left">Waktu Keluar</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">

                        @foreach($presensi as $p)
                        <tr class="hover:bg-gray-50 text-sm">
                            <td class="px-4 py-3 font-semibold text-default-800">{{ $p->karyawan->user->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-default-600">{{ $p->karyawan->nip ?? '-' }}</td>
                            <td class="px-4 py-3 text-default-600">{{ $p->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($p->shift)
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $p->shift->nama_shift }}</span>
                                @else <span class="text-default-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-default-600">{{ $p->jam_masuk ? \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') : '-' }}</td>
                            <td class="px-4 py-3 text-default-600">{{ $p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') : '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($p->status === 'hadir')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>Hadir
                                </span>
                                @elseif($p->status === 'terlambat')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 inline-block"></span>Terlambat
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>Tidak Hadir
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onclick="showDetail(this)"
                                    class="btn rounded-full border border-info text-info hover:bg-info hover:text-white text-xs"
                                    data-nama="{{ $p->karyawan->user->name ?? '-' }}"
                                    data-nip="{{ $p->karyawan->nip ?? '-' }}"
                                    data-jabatan="{{ $p->karyawan->jabatan->nama_jabatan ?? '-' }}"
                                    data-shift="{{ $p->shift->nama_shift ?? '-' }}"
                                    data-shift-masuk="{{ $p->shift ? \Carbon\Carbon::parse($p->shift->jam_masuk)->format('H:i') : '-' }}"
                                    data-shift-pulang="{{ $p->shift ? \Carbon\Carbon::parse($p->shift->jam_pulang)->format('H:i') : '-' }}"
                                    data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}"
                                    data-jam-masuk="{{ $p->jam_masuk ? \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') : '-' }}"
                                    data-jam-pulang="{{ $p->jam_pulang ? \Carbon\Carbon::parse($p->jam_pulang)->format('H:i') : '-' }}"
                                    data-status="{{ $p->status ?? 'tidak_hadir' }}"
                                    data-latitude="{{ $p->latitude ?? '' }}"
                                    data-longitude="{{ $p->longitude ?? '' }}"
                                    data-jarak="{{ $p->jarak_dari_kantor ?? '' }}">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        @foreach($karyawanAlpa as $k)
                        @php
                        $jadwal = $k->jadwalShift->first(fn($j) =>
                        $j->tanggal_mulai <= $tanggal && $j->tanggal_selesai >= $tanggal
                            );
                            @endphp
                            <tr class="hover:bg-gray-50 text-sm">
                                <td class="px-4 py-3 font-semibold text-default-800">{{ $k->user->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-default-600">{{ $k->nip ?? '-' }}</td>
                                <td class="px-4 py-3 text-default-600">{{ $k->jabatan->nama_jabatan ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($jadwal?->shift)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $jadwal->shift->nama_shift }}</span>
                                    @else <span class="text-default-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-default-400">-</td>
                                <td class="px-4 py-3 text-default-400">-</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>Alpa
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="showDetail(this)"
                                        class="btn rounded-full border border-info text-info hover:bg-info hover:text-white text-xs"
                                        data-nama="{{ $k->user->name ?? '-' }}"
                                        data-nip="{{ $k->nip ?? '-' }}"
                                        data-jabatan="{{ $k->jabatan->nama_jabatan ?? '-' }}"
                                        data-shift="{{ $jadwal?->shift->nama_shift ?? '-' }}"
                                        data-shift-masuk="{{ $jadwal?->shift ? \Carbon\Carbon::parse($jadwal->shift->jam_masuk)->format('H:i') : '-' }}"
                                        data-shift-pulang="{{ $jadwal?->shift ? \Carbon\Carbon::parse($jadwal->shift->jam_pulang)->format('H:i') : '-' }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}"
                                        data-jam-masuk="-"
                                        data-jam-pulang="-"
                                        data-status="alpa"
                                        data-latitude=""
                                        data-longitude=""
                                        data-jarak="">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @endforeach

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


{{-- ── MODAL DETAIL PRESENSI ───────────────────────────────────────────────── --}}
<div id="modal-detail"
    style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:16px;">

    {{-- Backdrop --}}
    <div onclick="closeDetail()"
        style="position:absolute; inset:0; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);"></div>

    {{-- Panel --}}
    <div style="position:relative; background:#fff; border-radius:16px; width:100%; max-width:460px; max-height:88vh; overflow-y:auto; box-shadow:0 25px 60px rgba(0,0,0,0.25); animation:modalIn .2s ease;">

        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px 14px; border-bottom:1px solid #f1f5f9; position:sticky; top:0; background:#fff; z-index:2; border-radius:16px 16px 0 0;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="material-symbols-rounded" style="font-size:21px; color:#3b82f6;">badge</i>
                </div>
                <div>
                    <div style="font-size:15px; font-weight:700; color:#0f172a; line-height:1.2;">Detail Presensi</div>
                    <div id="modal-subtitle" style="font-size:12px; color:#94a3b8; margin-top:2px;"></div>
                </div>
            </div>
            <button onclick="closeDetail()"
                style="width:32px; height:32px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:16px; flex-shrink:0; transition:background .15s;"
                onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">✕</button>
        </div>

        {{-- Body --}}
        <div style="padding:16px 20px 22px; display:flex; flex-direction:column; gap:12px;">

            {{-- Karyawan --}}
            <div>
                <div style="font-size:10px; font-weight:700; color:#3b82f6; letter-spacing:.8px; text-transform:uppercase; margin-bottom:6px; display:flex; align-items:center; gap:5px;">
                    <i class="material-symbols-rounded" style="font-size:13px;">person</i> Karyawan
                </div>
                <div style="background:#f8fafc; border-radius:10px; overflow:hidden;">
                    <div class="mrow"><span class="mlabel">Nama</span><span id="m-nama" class="mval"></span></div>
                    <div class="mrow"><span class="mlabel">NIP</span><span id="m-nip" class="mval"></span></div>
                    <div class="mrow"><span class="mlabel">Jabatan</span><span id="m-jabatan" class="mval"></span></div>
                    <div class="mrow mrow-last"><span class="mlabel">Shift</span><span id="m-shift" class="mval"></span></div>
                </div>
            </div>

            {{-- Presensi --}}
            <div>
                <div style="font-size:10px; font-weight:700; color:#16a34a; letter-spacing:.8px; text-transform:uppercase; margin-bottom:6px; display:flex; align-items:center; gap:5px;">
                    <i class="material-symbols-rounded" style="font-size:13px;">task_alt</i> Presensi
                </div>
                <div style="background:#f0fdf4; border-radius:10px; overflow:hidden;">
                    <div class="mrow"><span class="mlabel">Tanggal</span><span id="m-tanggal" class="mval"></span></div>
                    <div class="mrow"><span class="mlabel">Waktu Masuk</span><span id="m-masuk" class="mval"></span></div>
                    <div class="mrow"><span class="mlabel">Waktu Keluar</span><span id="m-pulang" class="mval"></span></div>
                    <div class="mrow mrow-last">
                        <span class="mlabel">Status</span>
                        <span id="m-status"></span>
                    </div>
                </div>
            </div>

            {{-- Lokasi --}}
            <div>
                <div style="font-size:10px; font-weight:700; color:#f59e0b; letter-spacing:.8px; text-transform:uppercase; margin-bottom:6px; display:flex; align-items:center; gap:5px;">
                    <i class="material-symbols-rounded" style="font-size:13px;">location_on</i> Lokasi Presensi
                </div>
                <div style="background:#fffbeb; border-radius:10px; overflow:hidden;">
                    <div class="mrow mrow-last"><span class="mlabel">Jarak dari Kantor</span><span id="m-jarak" class="mval"></span></div>
                    <div id="m-map" style="margin:0 12px 12px;"></div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    .cal-day:hover {
        background: rgba(79, 70, 229, .1);
        color: #4f46e5;
    }

    .mrow {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 14px;
        border-bottom: 1px solid rgba(0, 0, 0, .05);
    }

    .mrow-last {
        border-bottom: none;
    }

    .mlabel {
        font-size: 13px;
        font-weight: 600;
        color: #94a3b8;
    }

    .mval {
        font-size: 13px;
        font-weight: 500;
        color: #1e293b;
        text-align: right;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: translateY(14px) scale(.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>

<script>
    // ── KALENDER ──────────────────────────────────────────────────────────────
    const SELECTED_DATE = '{{ $tanggal }}';
    const BASE_URL = '{{ route("attendance.history") }}';
    const BULAN_ID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    let curYear, curMonth;

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
        document.getElementById('cal-header').textContent = BULAN_ID[curMonth] + ' ' + curYear;
        const grid = document.getElementById('cal-grid');
        grid.innerHTML = '';
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const selected = new Date(SELECTED_DATE + 'T00:00:00');
        const firstDay = new Date(curYear, curMonth, 1).getDay();
        const total = new Date(curYear, curMonth + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) grid.insertAdjacentHTML('beforeend', '<div></div>');

        for (let d = 1; d <= total; d++) {
            const t = new Date(curYear, curMonth, d);
            t.setHours(0, 0, 0, 0);
            const iso = `${curYear}-${String(curMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const base = 'display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:12px;margin:0 auto;transition:background .15s,color .15s;';
            let cls = '',
                style;

            if (t.getTime() === selected.getTime())
                style = base + 'background:#4f46e5;color:#fff;font-weight:700;box-shadow:0 1px 4px rgba(79,70,229,.4);';
            else if (t.getTime() === today.getTime())
                style = base + 'background:#e5e7eb;color:#1f2937;font-weight:600;';
            else {
                cls = 'cal-day';
                style = base + 'color:#4b5563;';
            }

            grid.insertAdjacentHTML('beforeend',
                `<div class="${cls}" data-date="${iso}" style="${style}">${d}</div>`);
        }

        grid.querySelectorAll('[data-date]').forEach(el =>
            el.addEventListener('click', function() {
                window.location.href = BASE_URL + '?tanggal=' + this.dataset.date;
            })
        );
    }

    // ── MODAL DETAIL ─────────────────────────────────────────────────────────
    function showDetail(btn) {
        const d = btn.dataset;

        document.getElementById('modal-subtitle').textContent = d.tanggal || '';
        document.getElementById('m-nama').textContent = d.nama || '-';
        document.getElementById('m-nip').textContent = d.nip || '-';
        document.getElementById('m-jabatan').textContent = d.jabatan || '-';
        document.getElementById('m-shift').textContent = d.shift || '-';
        document.getElementById('m-tanggal').textContent = d.tanggal || '-';
        document.getElementById('m-masuk').textContent = d.jamMasuk || '-';
        document.getElementById('m-pulang').textContent = d.jamPulang || '-';

        // Badge status
        const map = {
            hadir: {
                label: 'Hadir',
                bg: '#dcfce7',
                color: '#16a34a',
                dot: '#22c55e'
            },
            terlambat: {
                label: 'Terlambat',
                bg: '#fef9c3',
                color: '#b45309',
                dot: '#eab308'
            },
            alpa: {
                label: 'Alpa',
                bg: '#fee2e2',
                color: '#dc2626',
                dot: '#f87171'
            },
            tidak_hadir: {
                label: 'Tidak Hadir',
                bg: '#fee2e2',
                color: '#dc2626',
                dot: '#f87171'
            },
        };
        const s = map[d.status] || map.tidak_hadir;
        document.getElementById('m-status').innerHTML =
            `<span style="display:inline-flex;align-items:center;gap:5px;padding:3px 12px;border-radius:999px;font-size:12px;font-weight:600;background:${s.bg};color:${s.color};">
                <span style="width:7px;height:7px;border-radius:50%;background:${s.dot};display:inline-block;flex-shrink:0;"></span>
                ${s.label}
             </span>`;

        // Jarak
        document.getElementById('m-jarak').textContent =
            (d.jarak && d.jarak !== '') ? parseFloat(d.jarak).toFixed(0) + ' m dari kantor' : '-';

        // Map
        const mapEl = document.getElementById('m-map');
        const lat = d.latitude,
            lng = d.longitude;
        const hasGps = lat && lng && lat !== '' && lng !== '';

        if (hasGps) {
            const bbox = `${parseFloat(lng)-.002},${parseFloat(lat)-.002},${parseFloat(lng)+.002},${parseFloat(lat)+.002}`;
            mapEl.innerHTML =
                `<div style="border-radius:8px;overflow:hidden;border:1px solid #fde68a;">
                    <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${lat},${lng}"
                        style="width:100%;height:190px;border:none;display:block;" loading="lazy"></iframe>
                 </div>
                 <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank"
                    style="display:block;text-align:center;padding:6px 0 0;font-size:11px;color:#4f46e5;text-decoration:none;">
                    ↗ Buka di Google Maps
                 </a>`;
        } else {
            mapEl.innerHTML =
                `<div style="padding:16px 0 4px;text-align:center;color:#94a3b8;">
                    <div style="font-size:26px;margin-bottom:4px;">📍</div>
                    <div style="font-size:12px;">Tidak ada data lokasi</div>
                 </div>`;
        }

        const modal = document.getElementById('modal-detail');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeDetail() {
        document.getElementById('modal-detail').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeDetail();
    });
</script>
@endpush