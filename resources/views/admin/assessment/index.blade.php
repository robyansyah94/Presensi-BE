@extends('admin.layouts.app')

@section('title', 'Penilaian Sikap')

@section('content')

@if(session('success'))
<div class="bg-success/25 text-success text-sm rounded-md p-4 mb-4" role="alert">
    <span class="font-bold">Success!</span> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-danger/25 text-danger text-sm rounded-md p-4 mb-4" role="alert">
    <span class="font-bold">Danger!</span> {{ session('error') }}
</div>
@endif
<script>
    setTimeout(() => document.querySelectorAll('[role="alert"]').forEach(a => a.style.display = 'none'), 3000);
</script>

{{-- Page Title + Filter Periode --}}
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">PENILAIAN SIKAP KARYAWAN</h4>
    <div class="flex gap-2">
        @foreach(['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan'] as $key => $label)
        <a href="{{ route('admin.assessment.index', ['period' => $key]) }}"
            class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ $period === $key ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

{{-- Progress Bar --}}
<div class="card p-4 mb-5">
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm text-gray-600">Progress — <span class="font-medium text-gray-800">{{ $periodLabel }}</span></span>
        <span class="text-sm font-bold text-blue-600">{{ $totalDinilai }} / {{ $totalKaryawan }}</span>
    </div>
    @php $pct = $totalKaryawan > 0 ? round($totalDinilai / $totalKaryawan * 100) : 0; @endphp
    <div class="w-full bg-gray-100 rounded-full h-2">
        <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{$pct}}%"></div>
    </div>
    <p class="text-xs text-gray-400 mt-1">
        {{ $pct === 100 ? '🎉 Semua karyawan sudah dinilai!' : $totalDinilai . ' dari ' . $totalKaryawan . ' karyawan sudah dinilai' }}
    </p>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr class="text-sm">
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Karyawan</th>
                    <th class="px-4 py-3 text-left">Jabatan</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Nilai Rata-rata</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($karyawanList as $i => $karyawan)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500 text-sm">{{ $i + 1 }}</td>

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center
                                text-blue-600 font-bold text-sm shrink-0">
                                {{ strtoupper(substr(optional($karyawan->user)->name ?? '?', 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-800 text-sm">
                                {{ optional($karyawan->user)->name ?? '-' }}
                            </span>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ optional($karyawan->jabatan)->nama_jabatan ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if($karyawan->sudah_dinilai)
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">
                            ✓ Sudah Dinilai
                        </span>
                        @else
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full font-medium">
                            ● Belum Dinilai
                        </span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if($karyawan->sudah_dinilai && $karyawan->assessment)
                        @php $avg = $karyawan->assessment->average_score; @endphp
                        <div class="flex gap-0.5 justify-center">
                            @for($s = 1; $s <= 5; $s++)
                                <span class="{{ $s <= round($avg) ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                @endfor
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $avg }}/5</p>
                        @else
                        <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            @if($karyawan->sudah_dinilai && $karyawan->assessment)
                            <a href="{{ route('admin.assessment.edit', $karyawan->assessment->id) }}"
                                class="btn rounded-full border border-warning text-warning hover:bg-warning hover:text-white">
                                Edit
                            </a>
                            <a href="{{ route('admin.assessment.report', $karyawan->id) }}"
                                class="btn rounded-full border border-primary text-primary hover:bg-primary hover:text-white">
                                Rapor
                            </a>
                            @else
                            <a href="{{ route('admin.assessment.create', [$karyawan->id, 'period' => $period]) }}"
                                class="btn rounded-full border border-success text-success hover:bg-success hover:text-white">
                                Nilai
                            </a>
                            <a href="{{ route('admin.assessment.report', $karyawan->id) }}"
                                class="btn rounded-full border border-gray-300 text-gray-500 hover:bg-gray-100">
                                Riwayat
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-400">
                        Belum ada karyawan aktif.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection