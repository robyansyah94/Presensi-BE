{{-- resources/views/admin/integrity/rules/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Point Rules - Rule Engine')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">⚙️ Rule Engine</h1>
            <p class="text-sm text-gray-500 mt-1">Aturan otomatis pemberian & pengurangan poin integritas</p>
        </div>
        <a href="{{ route('admin.integrity.rules.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Rule
        </a>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-2">
        <span>✅</span> {{ session('success') }}
    </div>
    @endif

    {{-- Penjelasan singkat --}}
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
        <strong>Cara kerja:</strong> Setiap kali karyawan berhasil check-in, sistem secara otomatis mengevaluasi
        semua rule aktif di bawah ini dan menambah/mengurangi poin sesuai kondisi yang cocok.
    </div>

    {{-- Tabel Rules --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Rule</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Kondisi</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Poin</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Target</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rules as $rule)
                <tr class="hover:bg-gray-50 transition {{ $rule->is_active ? '' : 'opacity-50' }}">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $rule->rule_name }}</td>

                    {{-- Kondisi dalam format statement builder --}}
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                            JIKA
                            <span class="text-indigo-600">
                                {{ $rule->condition_type === 'jam_masuk' ? 'Jam Kedatangan' : 'Menit Terlambat' }}
                            </span>
                            <span class="text-orange-500 font-bold">{{ $rule->condition_operator }}</span>
                            <span class="text-green-700">{{ $rule->condition_value }}</span>
                            @if($rule->condition_operator === 'BETWEEN')
                            <span class="text-gray-500">DAN</span>
                            <span class="text-green-700">{{ $rule->condition_value_max }}</span>
                            @endif
                        </span>
                    </td>

                    {{-- Poin modifier --}}
                    <td class="px-4 py-3 text-center">
                        @if($rule->point_modifier > 0)
                            <span class="inline-block bg-green-100 text-green-700 font-bold px-3 py-1 rounded-full text-xs">
                                +{{ $rule->point_modifier }} Poin
                            </span>
                        @else
                            <span class="inline-block bg-red-100 text-red-700 font-bold px-3 py-1 rounded-full text-xs">
                                {{ $rule->point_modifier }} Poin
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-center text-gray-500 capitalize">{{ $rule->target_role }}</td>

                    {{-- Toggle Status --}}
                    <td class="px-4 py-3 text-center">
                        <form action="{{ route('admin.integrity.rules.toggle', $rule) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="text-xs px-3 py-1 rounded-full font-semibold transition
                                    {{ $rule->is_active
                                        ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                {{ $rule->is_active ? '● Aktif' : '○ Nonaktif' }}
                            </button>
                        </form>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.integrity.rules.edit', $rule) }}"
                               class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Edit</a>
                            <form action="{{ route('admin.integrity.rules.destroy', $rule) }}" method="POST"
                                  onsubmit="return confirm('Hapus rule ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">⚙️</div>
                        <div>Belum ada rule. Tambahkan rule pertama Anda!</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
