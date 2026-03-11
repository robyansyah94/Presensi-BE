@extends('admin.layouts.app')

@section('title', 'Rapor Penilaian - ' . optional($karyawan->user)->name)

@section('content')

<style>
    .star-fill  { color: #f59e0b; }
    .star-empty { color: #e5e7eb; }
    .riwayat-item { transition: background 0.15s; }
    .riwayat-item:hover { background: #f9fafb; }
</style>

{{-- ── Header ───────────────────────────────────────────────── --}}
<div class="flex items-center justify-between flex-wrap gap-2 mb-6">
    <div>
        <h4 class="text-default-900 text-lg font-bold">Rapor Penilaian Sikap</h4>
    </div>
    <a href="{{ route('admin.assessment.index') }}"
        class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl
            hover:bg-gray-50 transition text-sm font-medium shadow-sm">
        ← Kembali
    </a>
</div>

@php
    $allAvg = $assessments->count() > 0
        ? round($assessments->map(fn($a) => $a->details->avg('score'))->avg(), 1)
        : 0;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ════════════════════════════════════
         KOLOM KIRI — Profil + Grafik
    ════════════════════════════════════ --}}
    <div class="flex flex-col gap-5">

        {{-- Card Profil --}}
        <div class="card overflow-hidden">
            {{-- Banner tipis --}}
            <div class="h-16 bg-gradient-to-r from-blue-500 to-blue-600"></div>

            <div class="px-5 pb-5">
                {{-- Foto / Avatar --}}
                <div class="-mt-9 mb-3 flex justify-center">
                    @if($karyawan->foto)
                        <img src="{{ asset('storage/' . $karyawan->foto) }}"
                            class="w-18 h-18 rounded-2xl object-cover border-4 border-white shadow-md"
                            style="width:72px;height:72px">
                    @else
                        <div class="w-18 h-18 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600
                            flex items-center justify-center text-white font-black text-2xl
                            border-4 border-white shadow-md"
                            style="width:72px;height:72px">
                            {{ strtoupper(substr(optional($karyawan->user)->name ?? '?', 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="text-center mb-4">
                    <p class="font-bold text-gray-900 text-base leading-tight">
                        {{ optional($karyawan->user)->name }}
                    </p>
                    <p class="text-sm text-gray-400 mt-0.5">
                        {{ optional($karyawan->jabatan)->nama_jabatan ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-300 mt-0.5">NIP: {{ $karyawan->nip }}</p>

                    {{-- Stars --}}
                    <div class="flex gap-0.5 justify-center mt-3">
                        @for($s = 1; $s <= 5; $s++)
                            <span class="text-lg {{ $s <= round($allAvg) ? 'star-fill' : 'star-empty' }}">★</span>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $allAvg }}/5 Overall</p>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-blue-50 rounded-xl py-3 text-center">
                        <p class="text-xl font-black text-blue-600">{{ $assessments->count() }}</p>
                        <p class="text-[10px] text-blue-400 font-semibold uppercase tracking-wide mt-0.5">
                            Penilaian
                        </p>
                    </div>
                    <div class="bg-amber-50 rounded-xl py-3 text-center">
                        <p class="text-xl font-black text-amber-500">{{ $allAvg }}</p>
                        <p class="text-[10px] text-amber-400 font-semibold uppercase tracking-wide mt-0.5">
                            Rata-rata
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Grafik Radar --}}
        <div class="card p-5">
            <p class="text-sm font-bold text-gray-700 mb-1">Grafik Performa</p>
            <p class="text-xs text-gray-400 mb-4">Rata-rata semua periode</p>

            @if($radarData->isNotEmpty() && $radarData->sum('average') > 0)
                <canvas id="radarChart"></canvas>
            @else
                <div class="flex flex-col items-center justify-center h-40 text-gray-300">
                    <span class="text-4xl mb-2">📊</span>
                    <p class="text-sm">Belum ada data</p>
                </div>
            @endif
        </div>

    </div>

    {{-- ════════════════════════════════════
         KOLOM KANAN — Riwayat Penilaian
    ════════════════════════════════════ --}}
    <div class="lg:col-span-2">
        <div class="card overflow-hidden h-full">

            {{-- Header card --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h4 class="font-bold text-gray-800">Riwayat Penilaian</h4>
                <span class="text-xs text-gray-400 bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
                    {{ $assessments->count() }} periode
                </span>
            </div>

            {{-- List --}}
            @forelse($assessments as $i => $assessment)
            @php
                $avg        = $assessment->details->avg('score') ?? 0;
                $avgRounded = round($avg, 1);
                $pct        = ($avg / 5) * 100;
                $scoreLabel = match(true) {
                    $avg >= 4.5 => ['Istimewa', 'bg-green-100 text-green-700'],
                    $avg >= 3.5 => ['Sangat Baik', 'bg-blue-100 text-blue-700'],
                    $avg >= 2.5 => ['Baik', 'bg-yellow-100 text-yellow-700'],
                    $avg >= 1.5 => ['Cukup', 'bg-orange-100 text-orange-700'],
                    default     => ['Kurang', 'bg-red-100 text-red-700'],
                };
            @endphp

            <div class="px-6 py-5 border-b border-gray-50 last:border-0 riwayat-item">

                {{-- Baris atas: periode + skor + hapus --}}
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-3">
                        {{-- Nomor --}}
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center
                            text-blue-600 font-black text-xs shrink-0">
                            {{ $i + 1 }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-bold text-gray-800 text-sm">{{ $assessment->period_label }}</p>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $scoreLabel[1] }}">
                                    {{ $scoreLabel[0] }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $assessment->assessment_date->format('d M Y') }} ·
                                <span class="text-gray-500">
                                    {{ optional($assessment->evaluator)->name ?? '-' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        {{-- Score --}}
                        <div class="text-right">
                            <div class="flex gap-0.5 justify-end">
                                @for($s = 1; $s <= 5; $s++)
                                    <span class="text-sm {{ $s <= round($avg) ? 'star-fill' : 'star-empty' }}">★</span>
                                @endfor
                            </div>
                            <span class="text-xs font-bold text-gray-600">
                                {{ $avgRounded }}<span class="text-gray-300 font-normal">/5</span>
                            </span>
                        </div>

                        {{-- Hapus --}}
                        <form action="{{ route('admin.assessment.destroy', $assessment) }}" method="POST"
                            id="delete-assessment-{{ $assessment->id }}">
                            @csrf @method('DELETE')
                            <button type="button"
                                onclick="confirmDeleteAssessment('{{ $assessment->id }}')"
                                class="w-8 h-8 rounded-xl border border-red-100 text-red-400
                                    hover:bg-red-50 flex items-center justify-center text-xs font-bold transition">
                                ✕
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="h-full rounded-full transition-all"
                            style="width:{{ $pct }}%; background: linear-gradient(90deg, #60a5fa, #3b82f6)">
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-blue-600 w-8 text-right">{{ round($pct) }}%</span>
                </div>

                {{-- Detail kategori --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($assessment->details as $detail)
                    <div class="bg-gray-50 rounded-xl px-3 py-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide truncate mb-1.5">
                            {{ optional($detail->category)->name ?? '-' }}
                        </p>
                        <div class="flex items-center gap-1.5">
                            <div class="flex gap-0.5">
                                @for($s = 1; $s <= 5; $s++)
                                    <span class="text-xs {{ $s <= $detail->score ? 'star-fill' : 'star-empty' }}">★</span>
                                @endfor
                            </div>
                            <span class="text-[10px] font-bold text-gray-400">{{ $detail->score }}/5</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Catatan --}}
                @if($assessment->general_notes)
                <div class="mt-3 flex gap-2.5 bg-blue-50 rounded-xl px-4 py-3">
                    <span class="text-base shrink-0 mt-0.5">💬</span>
                    <div>
                        <p class="text-[10px] font-bold text-blue-400 uppercase tracking-wide mb-0.5">
                            Catatan Evaluator
                        </p>
                        <p class="text-xs text-blue-700 leading-relaxed">{{ $assessment->general_notes }}</p>
                    </div>
                </div>
                @endif

            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <span class="text-5xl mb-3">📋</span>
                <p class="text-sm font-semibold text-gray-400">Belum ada riwayat penilaian</p>
                <a href="{{ route('admin.assessment.create', $karyawan->id) }}"
                    class="text-blue-500 text-sm hover:underline mt-2 font-medium">
                    + Buat penilaian pertama
                </a>
            </div>
            @endforelse

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('radarChart');
    if (!ctx) return;

    const radarData = @json($radarData);

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: radarData.map(d => d.category),
            datasets: [{
                label: 'Nilai',
                data: radarData.map(d => d.average),
                backgroundColor: 'rgba(59,130,246,0.10)',
                borderColor: 'rgba(59,130,246,0.75)',
                borderWidth: 2,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    min: 0, max: 5,
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 },
                        backdropColor: 'transparent',
                        color: '#9ca3af',
                    },
                    pointLabels: {
                        font: { size: 10, weight: '600' },
                        color: '#374151',
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    angleLines: { color: 'rgba(0,0,0,0.05)' },
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleFont: { size: 11 },
                    bodyFont: { size: 12, weight: 'bold' },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ` ${ctx.raw}/5` }
                }
            }
        }
    });
});

function confirmDeleteAssessment(id) {
    Swal.fire({
        title: "Hapus penilaian ini?",
        text: "Data akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('delete-assessment-' + id).submit();
        }
    });
}
</script>
@endpush