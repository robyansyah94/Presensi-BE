{{-- resources/views/admin/integrity/marketplace/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', isset($item) ? 'Edit Item' : 'Tambah Item Marketplace')

@section('content')
<div class="p-6 max-w-xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.integrity.marketplace.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← Kembali ke Marketplace
        </a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">
            {{ isset($item) ? '✏️ Edit Item' : '🛍️ Tambah Item Baru' }}
        </h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form
            action="{{ isset($item) ? route('admin.integrity.marketplace.update', $item) : route('admin.integrity.marketplace.store') }}"
            method="POST" class="space-y-5">
            @csrf
            @if(isset($item)) @method('PUT') @endif

            {{-- Nama Item --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Item</label>
                <input type="text" name="item_name"
                    value="{{ old('item_name', $item->item_name ?? '') }}"
                    placeholder="Cth: Token Bebas Terlambat 30 Menit"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                @error('item_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Icon & Tipe --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon"
                        value="{{ old('icon', $item->icon ?? '🎫') }}"
                        maxlength="10" placeholder="🎫"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none text-2xl">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Token</label>
                    <select name="token_type" id="token_type"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                        <option value="late_tolerance" {{ old('token_type', $item->token_type ?? '') === 'late_tolerance' ? 'selected' : '' }}>
                            Toleransi Keterlambatan
                        </option>
                        <option value="wfh"    {{ old('token_type', $item->token_type ?? '') === 'wfh'    ? 'selected' : '' }}>Work From Home</option>
                        <option value="excuse" {{ old('token_type', $item->token_type ?? '') === 'excuse' ? 'selected' : '' }}>Izin Tanpa Surat</option>
                    </select>
                </div>
            </div>

            {{-- Toleransi Menit (hanya untuk late_tolerance) --}}
            <div id="tolerance-group" class="{{ old('token_type', $item->token_type ?? '') === 'late_tolerance' ? '' : 'hidden' }}">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Toleransi Menit Terlambat</label>
                <p class="text-xs text-gray-400 mb-1">Token ini akan dipakai otomatis jika karyawan terlambat ≤ X menit.</p>
                <input type="number" name="tolerance_minutes" min="1"
                    value="{{ old('tolerance_minutes', $item->tolerance_minutes ?? '') }}"
                    placeholder="Cth: 30"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                @error('tolerance_minutes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi (Opsional)</label>
                <textarea name="description" rows="2" placeholder="Deskripsi singkat tentang token ini..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">{{ old('description', $item->description ?? '') }}</textarea>
            </div>

            {{-- Harga & Stock --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Harga (Poin)</label>
                    <input type="number" name="point_cost" min="1"
                        value="{{ old('point_cost', $item->point_cost ?? '') }}" placeholder="Cth: 50"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                    @error('point_cost') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Batas Beli/Bulan</label>
                    <input type="number" name="stock_limit" min="1"
                        value="{{ old('stock_limit', $item->stock_limit ?? '') }}" placeholder="Kosongkan = unlimited"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                </div>
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                <label for="is_active" class="text-sm font-semibold text-gray-700">Item Aktif di Marketplace</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    {{ isset($item) ? 'Update Item' : 'Simpan Item' }}
                </button>
                <a href="{{ route('admin.integrity.marketplace.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const tokenType      = document.getElementById('token_type');
const toleranceGroup = document.getElementById('tolerance-group');

tokenType.addEventListener('change', () => {
    toleranceGroup.classList.toggle('hidden', tokenType.value !== 'late_tolerance');
});
</script>
@endsection
