{{-- resources/views/admin/integrity/rules/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Rule Baru')

@section('content')
<div class="p-6 max-w-2xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.integrity.rules.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Rule Engine</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">⚙️ Tambah Rule Baru</h1>
    </div>

    {{-- Live Preview --}}
    <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-xl">
        <p class="text-xs font-semibold text-indigo-500 uppercase mb-2">Preview Statement</p>
        <div id="rule-preview" class="font-mono text-sm text-gray-700 min-h-5">—</div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form action="{{ route('admin.integrity.rules.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nama Rule --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Rule</label>
                <input type="text" name="rule_name" value="{{ old('rule_name') }}"
                    placeholder="Cth: Datang Lebih Awal, Alpa Penalty"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                @error('rule_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Target Role --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Berlaku Untuk</label>
                <select name="target_role"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                    <option value="karyawan" {{ old('target_role','karyawan') === 'karyawan' ? 'selected':'' }}>Karyawan</option>
                    <option value="admin"    {{ old('target_role') === 'admin'    ? 'selected':'' }}>Admin</option>
                    <option value="all"      {{ old('target_role') === 'all'      ? 'selected':'' }}>Semua</option>
                </select>
            </div>

            {{-- Jenis Kondisi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kondisi</label>
                <select name="condition_type" id="condition_type"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                    <option value="menit_terlambat"  {{ old('condition_type') === 'menit_terlambat'  ? 'selected':'' }}>Menit Terlambat (dari jam shift)</option>
                    <option value="menit_lebih_awal" {{ old('condition_type') === 'menit_lebih_awal' ? 'selected':'' }}>Menit Lebih Awal (sebelum jam shift)</option>
                    <option value="status_presensi"  {{ old('condition_type') === 'status_presensi'  ? 'selected':'' }}>Status Presensi (hadir/terlambat/alpa)</option>
                    <option value="jam_masuk"        {{ old('condition_type') === 'jam_masuk'        ? 'selected':'' }}>Jam Kedatangan Absolut</option>
                </select>

                {{-- Keterangan kontekstual --}}
                <p id="hint-menit_terlambat"  class="hint text-xs text-gray-400 mt-1">Dihitung dari jam masuk shift. Contoh: > 30 artinya lebih dari 30 menit terlambat.</p>
                <p id="hint-menit_lebih_awal" class="hint text-xs text-gray-400 mt-1 hidden">Relatif ke jam shift masing-masing. Contoh: BETWEEN 10 DAN 60 artinya datang 10–60 menit sebelum shift.</p>
                <p id="hint-status_presensi"  class="hint text-xs text-gray-400 mt-1 hidden">Isi nilai kondisi dengan: <strong>hadir</strong>, <strong>terlambat</strong>, <strong>alpa</strong>, atau <strong>hadir_token</strong>. Operator diabaikan.</p>
                <p id="hint-jam_masuk"        class="hint text-xs text-gray-400 mt-1 hidden">Jam absolut (tidak memperhitungkan shift). Gunakan format HH:MM:SS.</p>
            </div>

            {{-- Operator (sembunyikan jika status_presensi) --}}
            <div id="operator-group">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Operator</label>
                <select name="condition_operator" id="condition_operator"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                    <option value="<"       {{ old('condition_operator') === '<'       ? 'selected':'' }}>Kurang dari (&lt;)</option>
                    <option value=">"       {{ old('condition_operator') === '>'       ? 'selected':'' }}>Lebih dari (&gt;)</option>
                    <option value="BETWEEN" {{ old('condition_operator') === 'BETWEEN' ? 'selected':'' }}>Di Antara (BETWEEN)</option>
                </select>
            </div>

            {{-- Nilai kondisi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1" id="label-value">Nilai</label>
                <input type="text" name="condition_value" id="condition_value"
                    value="{{ old('condition_value') }}"
                    placeholder="Cth: 15 atau alpa"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                @error('condition_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nilai Max (BETWEEN) --}}
            <div id="between-group" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nilai Maksimum</label>
                <input type="text" name="condition_value_max" id="condition_value_max"
                    value="{{ old('condition_value_max') }}" placeholder="Cth: 60"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
            </div>

            {{-- Poin Modifier --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Poin Modifier</label>
                <p class="text-xs text-gray-400 mb-1">Positif = tambah poin, negatif = kurangi poin.</p>
                <input type="number" name="point_modifier" id="point_modifier"
                    value="{{ old('point_modifier') }}" placeholder="Cth: 5 atau -10"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                @error('point_modifier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Status aktif --}}
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active','1') == '1' ? 'checked':'' }}
                    class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                <label for="is_active" class="text-sm font-semibold text-gray-700">Aktifkan Rule Langsung</label>
            </div>
            
            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    Simpan Rule
                </button>
                <a href="{{ route('admin.integrity.rules.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const condType  = document.getElementById('condition_type');
const condOp    = document.getElementById('condition_operator');
const condVal   = document.getElementById('condition_value');
const condMax   = document.getElementById('condition_value_max');
const poinEl    = document.getElementById('point_modifier');
const preview   = document.getElementById('rule-preview');
const betweenGr = document.getElementById('between-group');
const opGroup   = document.getElementById('operator-group');
const labelVal  = document.getElementById('label-value');

const hints = document.querySelectorAll('.hint');

function updateUI() {
    const type      = condType.value;
    const op        = condOp.value;
    const val       = condVal.value || '...';
    const max       = condMax.value || '...';
    const poin      = poinEl.value;
    const isStatus  = type === 'status_presensi';
    const isBetween = op === 'BETWEEN' && !isStatus;

    // Sembunyikan/tampilkan operator
    opGroup.classList.toggle('hidden', isStatus);

    // Sembunyikan/tampilkan nilai max
    betweenGr.classList.toggle('hidden', !isBetween);

    // Update hint
    hints.forEach(h => h.classList.add('hidden'));
    document.getElementById('hint-' + type)?.classList.remove('hidden');

    // Update label nilai
    labelVal.textContent = isStatus ? 'Status (hadir / terlambat / alpa / hadir_token)' : 'Nilai';

    // Build preview
    const typeLabels = {
        jam_masuk:        'Jam Kedatangan',
        menit_terlambat:  'Menit Terlambat',
        menit_lebih_awal: 'Menit Lebih Awal',
        status_presensi:  'Status',
    };
    const tLabel = typeLabels[type] ?? type;
    const pNum   = parseInt(poin);
    const pLabel = isNaN(pNum) ? '...' : (pNum > 0 ? '+' : '') + pNum + ' Poin';
    const pColor = !isNaN(pNum) ? (pNum > 0 ? 'color:green' : 'color:red') : '';

    let stmt;
    if (isStatus) {
        stmt = `JIKA <b style="color:#7c3aed">${tLabel}</b> = <b style="color:#16a34a">${val}</b> MAKA POIN <b style="${pColor}">${pLabel}</b>`;
    } else if (isBetween) {
        stmt = `JIKA <b style="color:#4f46e5">${tLabel}</b> <b style="color:#ea580c">BETWEEN</b> <b style="color:#16a34a">${val}</b> DAN <b style="color:#16a34a">${max}</b> MAKA POIN <b style="${pColor}">${pLabel}</b>`;
    } else {
        stmt = `JIKA <b style="color:#4f46e5">${tLabel}</b> <b style="color:#ea580c">${op}</b> <b style="color:#16a34a">${val}</b> MAKA POIN <b style="${pColor}">${pLabel}</b>`;
    }
    preview.innerHTML = stmt;
}

[condType, condOp, condVal, condMax, poinEl].forEach(el => {
    el.addEventListener('input', updateUI);
    el.addEventListener('change', updateUI);
});
updateUI();
</script>
@endsection