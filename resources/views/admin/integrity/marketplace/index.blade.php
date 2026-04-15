{{-- resources/views/admin/integrity/marketplace/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Flexibility Marketplace')

@section('content')
<div class="p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🛍️ Flexibility Marketplace</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola item yang bisa ditukar karyawan menggunakan poin integritas</p>
        </div>
        <a href="{{ route('admin.integrity.marketplace.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Item
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- Grid Card Item --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
        <div class="bg-white rounded-xl shadow p-5 flex flex-col gap-3 {{ $item->is_active ? '' : 'opacity-60' }}">

            {{-- Header Card --}}
            <div class="flex items-start justify-between">
                <div class="text-4xl">{{ $item->icon }}</div>
                <div class="flex flex-col items-end gap-1">
                    @if($item->is_active)
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold">Aktif</span>
                    @else
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-semibold">Nonaktif</span>
                    @endif
                    <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full">{{ $item->token_type_label }}</span>
                </div>
            </div>

            {{-- Nama & Deskripsi --}}
            <div>
                <h3 class="font-bold text-gray-800">{{ $item->item_name }}</h3>
                @if($item->description)
                    <p class="text-xs text-gray-500 mt-1">{{ $item->description }}</p>
                @endif
                @if($item->tolerance_minutes)
                    <p class="text-xs text-orange-600 mt-1 font-medium">⏱️ Toleransi {{ $item->tolerance_minutes }} menit</p>
                @endif
            </div>

            {{-- Harga --}}
            <div class="flex items-center gap-2 mt-auto pt-3 border-t border-gray-100">
                <div class="flex-1">
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($item->point_cost) }}
                        <span class="text-sm font-normal text-gray-400">poin</span>
                    </p>
                    @if($item->stock_limit)
                        <p class="text-xs text-gray-400">Maks {{ $item->stock_limit }}x/bulan per orang</p>
                    @else
                        <p class="text-xs text-gray-400">Unlimited per bulan</p>
                    @endif
                </div>
                <div class="flex flex-col gap-1">
                    <a href="{{ route('admin.integrity.marketplace.edit', $item) }}"
                       class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-medium px-3 py-1.5 rounded-lg transition text-center">
                        Edit
                    </a>
                    <form action="{{ route('admin.integrity.marketplace.destroy', $item) }}" method="POST"
                          onsubmit="return confirm('Hapus item ini?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-full text-xs bg-red-50 hover:bg-red-100 text-red-600 font-medium px-3 py-1.5 rounded-lg transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 py-16 text-center text-gray-400">
            <div class="text-5xl mb-3">🛍️</div>
            <p>Marketplace masih kosong. Tambahkan item pertama!</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
