@extends('admin.layouts.app')

@section('title', 'Rapor Penilaian - ' . optional($karyawan->user)->name)

@section('content')

{{-- Page Title --}}
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">RAPOR PENILAIAN SIKAP</h4>
    <a href="{{ route('admin.assessment.index') }}"
        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition text-sm">
        Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Kolom Kiri: Profil + Radar Chart --}}
    <div class="flex flex-col gap-5">

        {{-- Profil --}}
        <div class="card p-5 text-center">
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center
                text-blue-600 font-bold text-2xl mx-auto mb-3">
                {{ strtoupper(substr(optional($karyawan->user)->name ?? '?', 0, 1)) }}
            </div>
            <p class="font-bold text-gray-800">{{ optional($karyawan->user)->name }}</p>
            <p class="text-sm text-gray-500">{{ optional($karyawan->jabatan)->nama_jabatan ?? '-' }}</p>
            <p class="text-xs text-gray-400 mt-1">NIP: {{ $karyawan->nip }}</p>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-400">Total Penilaian</p>
                <p class="text-2xl font-bold text-blue-600">{{ $assessments->count() }}</p>
            </div>
        </div>

        {{-- Radar Chart --}}
        <div class="card p-5">
            <h5 class="font-semibold text-gray-700 text-sm mb-4">Grafik Performa (Rata-rata)</h5>
            @if($radarData->isNotEmpty() && $radarData->sum('average') > 0)
                <canvas id="radarChart" height="250"></canvas>
            @else
                <div class="flex items-center justify-center h-40 text-gray-300 text-sm">
                    Belum ada data penilaian
                </div>
            @endif
        </div>

    </div>

    {{-- Kolom Kanan: Riwayat Penilaian --}}
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Riwayat Penilaian</h4>
            </div>

            @forelse($assessments as $assessment)
            <div class="px-6 py-5 border-b border-gray-100 last:border-0">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $assessment->period_label }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $assessment->assessment_date->format('d M Y') }} ·
                            Dinilai oleh: {{ optional($assessment->evaluator)->name }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Rata-rata bintang --}}
                        <div class="flex flex-col items-end">
                            <div class="flex gap-0.5">
                                @for($s = 1; $s <= 5; $s++)
                                    <span class="{{ $s <= round($assessment->average_score) ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                @endfor
                            </div>
                            <span class="text-xs text-gray-400 mt-0.5">{{ $assessment->average_score }}/5</span>
                        </div>
                        {{-- Hapus --}}
                        <form action="{{ route('admin.assessment.destroy', $assessment) }}" method="POST"
                            id="delete-assessment-{{ $assessment->id }}">
                            @csrf @method('DELETE')
                            <button type="button"
                                onclick="confirmDeleteAssessment('{{ $assessment->id }}')"
                                class="btn rounded-full border border-danger text-danger hover:bg-danger hover:text-white text-xs">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Detail nilai per kategori --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3">
                    @foreach($assessment->details as $detail)
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-500 truncate">{{ optional($detail->category)->name }}</p>
                        <div class="flex gap-0.5 mt-1">
                            @for($s = 1; $s <= 5; $s++)
                                <span class="text-sm {{ $s <= $detail->score ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Catatan --}}
                @if($assessment->general_notes)
                <div class="bg-blue-50 rounded-lg px-4 py-3 text-sm text-blue-700">
                    <span class="font-medium">Catatan:</span> {{ $assessment->general_notes }}
                </div>
                @endif

            </div>
            @empty
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                Belum ada riwayat penilaian untuk karyawan ini.
                <a href="{{ route('admin.assessment.create', $karyawan->id) }}"
                    class="text-blue-600 hover:underline block mt-1">
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
// Radar Chart
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('radarChart');
    if (!ctx) return;

    const radarData = @json($radarData);
    const labels    = radarData.map(d => d.category);
    const values    = radarData.map(d => d.average);

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Rata-rata Nilai',
                data: values,
                backgroundColor: 'rgba(59, 130, 246, 0.15)',
                borderColor: 'rgba(59, 130, 246, 0.8)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    min: 0,
                    max: 5,
                    ticks: { stepSize: 1, font: { size: 10 } },
                    pointLabels: { font: { size: 11 } },
                }
            },
            plugins: { legend: { display: false } }
        }
    });
});

// Konfirmasi hapus
function confirmDeleteAssessment(id) {
    Swal.fire({
        title: "Hapus penilaian ini?",
        text: "Data penilaian akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-assessment-' + id).submit();
        }
    });
}
</script>
@endpush