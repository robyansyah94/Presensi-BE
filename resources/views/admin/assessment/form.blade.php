@extends('admin.layouts.app')

@section('title', isset($assessment) ? 'Edit Penilaian' : 'Input Penilaian')

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

{{-- Page Title --}}
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">
        {{ isset($assessment) ? 'EDIT PENILAIAN' : 'INPUT PENILAIAN' }}
    </h4>
</div>

{{-- Profil Karyawan --}}
<div class="card p-4 mb-5">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center
            text-blue-600 font-bold text-lg shrink-0">
            {{ strtoupper(substr(optional($karyawan->user)->name ?? '?', 0, 1)) }}
        </div>
        <div>
            <p class="font-semibold text-gray-800">{{ optional($karyawan->user)->name }}</p>
            <p class="text-sm text-gray-500">
                {{ optional($karyawan->jabatan)->nama_jabatan ?? '-' }} · NIP: {{ $karyawan->nip }}
            </p>
            <p class="text-xs text-blue-600 mt-0.5">
                Periode: {{ $periodLabel ?? $assessment->period_label }}
                ({{ ucfirst($period ?? $assessment->period) }})
            </p>
        </div>
    </div>
</div>

{{-- Form --}}
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Indikator Penilaian</h4>
    </div>
    <div class="p-6">
        <form action="{{ isset($assessment)
                ? route('admin.assessment.update', $assessment)
                : route('admin.assessment.store') }}"
            method="POST">
            @csrf
            @if(isset($assessment)) @method('PUT') @endif

            <input type="hidden" name="evaluatee_id" value="{{ $karyawan->id }}">
            <input type="hidden" name="period" value="{{ $period ?? $assessment->period }}">
            <input type="hidden" name="period_label" value="{{ $periodLabel ?? $assessment->period_label }}">

            @if($errors->any())
            <div class="bg-danger/25 text-danger text-sm rounded-md p-4 mb-5">
                {{ $errors->first() }}
            </div>
            @endif

            @forelse($categories as $cat)
            <div class="flex items-center justify-between gap-4 py-4 border-b border-gray-100 last:border-0">

                {{-- Nama Kategori --}}
                <div class="flex-1">
                    <p class="font-medium text-gray-800 text-sm">{{ $cat->name }}</p>
                    @if($cat->description)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $cat->description }}</p>
                    @endif
                </div>

                {{-- Star Rating + Label --}}
                <div class="flex flex-col items-end shrink-0">
                    <div class="flex gap-0.5">
                        @for($s = 1; $s <= 5; $s++)
                            <button type="button"
                            onclick="setRating({{ $cat->id }}, {{ $s }})"
                            onmouseover="hoverRating({{ $cat->id }}, {{ $s }})"
                            onmouseout="resetHover({{ $cat->id }})"
                            class="star-btn text-3xl leading-none transition-transform hover:scale-110 focus:outline-none"
                            data-category="{{ $cat->id }}"
                            data-value="{{ $s }}">☆</button>
                            @endfor
                            <input type="hidden" name="scores[{{ $cat->id }}]"
                                id="score_{{ $cat->id }}"
                                value="{{ $scores[$cat->id] ?? '' }}">
                    </div>
                    <span id="label_{{ $cat->id }}" class="text-xs text-gray-400 mt-0.5">
                        @if(isset($scores[$cat->id]))
                        @php $lbls = [1=>'Kurang',2=>'Cukup',3=>'Baik',4=>'Sangat Baik',5=>'Istimewa']; @endphp
                        {{ $lbls[$scores[$cat->id]] ?? '' }}
                        @else
                        Belum dinilai
                        @endif
                    </span>
                </div>

            </div>
            @empty
            <div class="text-center py-8 text-gray-400 text-sm">
                Belum ada kategori aktif.
                <a href="{{ route('admin.assessment.categories.create') }}" class="text-blue-600 hover:underline ml-1">
                    + Tambah kategori
                </a>
            </div>
            @endforelse

            @if($categories->isNotEmpty())
            {{-- Catatan --}}
            <div class="mt-5">
                <label class="text-default-800 text-sm font-medium inline-block mb-2">
                    Catatan / Feedback <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="general_notes" rows="3"
                    placeholder="Catatan evaluasi umum..."
                    class="form-input">{{ old('general_notes', $assessment->general_notes ?? '') }}</textarea>
            </div>

            {{-- Tombol --}}
            <div class="mt-5 flex gap-2">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    {{ isset($assessment) ? 'Simpan Perubahan' : 'Simpan Penilaian' }}
                </button>
                <a href="{{ route('admin.assessment.index', ['period' => $period ?? $assessment->period]) }}"
                    class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                    Batal
                </a>
            </div>
            @endif

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const scoreLabels = {
        1: 'Kurang',
        2: 'Cukup',
        3: 'Baik',
        4: 'Sangat Baik',
        5: 'Istimewa'
    };

    function getStars(catId) {
        return document.querySelectorAll(`.star-btn[data-category="${catId}"]`);
    }

    function renderStars(catId, value, isHover = false) {
        getStars(catId).forEach(btn => {
            const v = parseInt(btn.dataset.value);
            btn.textContent = v <= value ? '★' : '☆';
            btn.style.color = v <= value ? '#FBBF24' : '#D1D5DB';
        });
        if (!isHover) {
            const lbl = document.getElementById(`label_${catId}`);
            if (lbl) lbl.textContent = value > 0 ? scoreLabels[value] : 'Belum dinilai';
        }
    }

    function setRating(catId, value) {
        document.getElementById(`score_${catId}`).value = value;
        renderStars(catId, value);
    }

    function hoverRating(catId, value) {
        renderStars(catId, value, true);
    }

    function resetHover(catId) {
        const cur = parseInt(document.getElementById(`score_${catId}`).value) || 0;
        renderStars(catId, cur);
    }
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[id^="score_"]').forEach(input => {
            const catId = input.id.replace('score_', '');
            const val = parseInt(input.value) || 0;
            if (val > 0) renderStars(catId, val);
        });
    });
</script>
@endpush