{{-- resources/views/admin/integrity/rules/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Rule Baru')

@section('content')
<div class="p-6 max-w-2xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.integrity.rules.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            ← Kembali ke Rule Engine
        </a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">⚙️ Tambah Rule Baru</h1>
    </div>

    {{-- Rule Builder Preview --}}
    <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-xl">
        <p class="text-xs font-semibold text-indigo-500 uppercase mb-2">Preview Statement</p>
        <div id="rule-preview" class="font-mono text-sm text-gray-700">
            JIKA <span class="text-indigo-600" id="prev-type">...</span>
            <span class="text-orange-500 font-bold" id="prev-op">...</span>
            <span class="text-green-700" id="prev-val">...</span>
            <span id="prev-between" class="hidden"> DAN <span class="text-green-700" id="prev-val-max">...</span></span>
            MAKA POIN <span id="prev-poin" class="font-bold">...</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form action="{{ route('admin.integrity.rules.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nama Rule --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Rule</label>
                <input type="text" name="rule_name" value="{{ old('rule_name') }}" placeholder="Cth: Datang Pagi Banget"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                @error('rule_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Target Role --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Berlaku Untuk</label>
                <select name="target_role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                    <option value="karyawan" {{ old('target_role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    <option value="admin"    {{ old('target_role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                    <option value="all"      {{ old('target_role') === 'all'      ? 'selected' : '' }}>Semua</option>
                </select>
            </div>

            {{-- Kondisi Builder --}}
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kondisi</label>
                    <select name="condition_type" id="condition_type"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                        <option value="jam_masuk"       {{ old('condition_type') === 'jam_masuk'      ? 'selected' : '' }}>Jam Kedatangan</option>
                        <option value="menit_terlambat" {{ old('condition_type') === 'menit_terlambat'? 'selected' : '' }}>Menit Terlambat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Operator</label>
                    <select name="condition_operator" id="condition_operator"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                        <option value="<"       {{ old('condition_operator') === '<'       ? 'selected' : '' }}>Kurang dari (&lt;)</option>
                        <option value=">"       {{ old('condition_operator') === '>'       ? 'selected' : '' }}>Lebih dari (&gt;)</option>
                        <option value="BETWEEN" {{ old('condition_operator') === 'BETWEEN' ? 'selected' : '' }}>Di Antara (BETWEEN)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nilai</label>
                    <input type="text" name="condition_value" id="condition_value"
                        value="{{ old('condition_value') }}" placeholder="Cth: 06:30 atau 15"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                </div>
            </div>

            {{-- Nilai Max (hanya untuk BETWEEN) --}}
            <div id="between-group" class="{{ old('condition_operator') === 'BETWEEN' ? '' : 'hidden' }}">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nilai Maksimum (untuk BETWEEN)</label>
                <input type="text" name="condition_value_max" id="condition_value_max"
                    value="{{ old('condition_value_max') }}" placeholder="Cth: 09:00 atau 30"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
            </div>

            {{-- Poin Modifier --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Poin Modifier</label>
                <p class="text-xs text-gray-400 mb-1">Gunakan angka positif untuk MENAMBAH poin, negatif untuk MENGURANGI.</p>
                <input type="number" name="point_modifier" id="point_modifier"
                    value="{{ old('point_modifier') }}" placeholder="Cth: 5 atau -3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                @error('point_modifier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', '1') == '1' ? 'checked' : '' }}
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
// ── Live preview builder ──────────────────────────────────────────────────────
const fields = {
    type:    document.getElementById('condition_type'),
    op:      document.getElementById('condition_operator'),
    val:     document.getElementById('condition_value'),
    valMax:  document.getElementById('condition_value_max'),
    poin:    document.getElementById('point_modifier'),
    between: document.getElementById('between-group'),
};

const prevType   = document.getElementById('prev-type');
const prevOp     = document.getElementById('prev-op');
const prevVal    = document.getElementById('prev-val');
const prevBetween= document.getElementById('prev-between');
const prevValMax = document.getElementById('prev-val-max');
const prevPoin   = document.getElementById('prev-poin');

function updatePreview() {
    const typeLabel = fields.type.value === 'jam_masuk' ? 'Jam Kedatangan' : 'Menit Terlambat';
    const opLabel   = fields.op.value;
    const val       = fields.val.value || '...';
    const valMax    = fields.valMax.value || '...';
    const poin      = fields.poin.value;
    const isBetween = opLabel === 'BETWEEN';

    prevType.textContent   = typeLabel;
    prevOp.textContent     = opLabel;
    prevVal.textContent    = val;
    prevValMax.textContent = valMax;
    prevBetween.classList.toggle('hidden', !isBetween);
    fields.between.classList.toggle('hidden', !isBetween);

    if (poin) {
        const num = parseInt(poin);
        prevPoin.textContent  = (num > 0 ? '+' : '') + num;
        prevPoin.className    = num > 0 ? 'font-bold text-green-600' : 'font-bold text-red-600';
    } else {
        prevPoin.textContent  = '...';
        prevPoin.className    = 'font-bold';
    }
}

[fields.type, fields.op, fields.val, fields.valMax, fields.poin].forEach(el => {
    el.addEventListener('input', updatePreview);
    el.addEventListener('change', updatePreview);
});

updatePreview();
</script>
@endsection
