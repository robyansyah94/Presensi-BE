{{-- resources/views/admin/integrity/analytics/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Analitik Integritas')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📊 Analitik Integritas</h1>
            <p class="text-sm text-gray-500 mt-1">Leaderboard & statistik poin karyawan</p>
        </div>

        {{-- Filter Bulan --}}
        <form method="GET" class="flex items-center gap-2">
            <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="tahun" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach(range(now()->year - 2, now()->year) as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                Filter
            </button>
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-xs text-green-600 font-semibold uppercase mb-1">Total Poin Diperoleh</p>
            <p class="text-2xl font-bold text-green-700">+{{ number_format($earnTotal) }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-xs text-red-600 font-semibold uppercase mb-1">Total Poin Dipotong</p>
            <p class="text-2xl font-bold text-red-700">-{{ number_format($penaltyTotal) }}</p>
        </div>
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
            <p class="text-xs text-indigo-600 font-semibold uppercase mb-1">Token Ditukarkan</p>
            <p class="text-2xl font-bold text-indigo-700">{{ $tokenRedeemed }}</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
            <p class="text-xs text-purple-600 font-semibold uppercase mb-1">Token Terpakai</p>
            <p class="text-2xl font-bold text-purple-700">{{ $tokenUsed }}</p>
        </div>
    </div>

    {{-- Leaderboard --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">🏆 Leaderboard Poin Integritas</h2>
            <p class="text-xs text-gray-400">Diurutkan berdasarkan saldo poin terkini</p>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($leaderboard as $i => $entry)
            <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">

                {{-- Rank --}}
                <div class="w-8 text-center font-bold
                    {{ $i === 0 ? 'text-yellow-500 text-lg' :
                       ($i === 1 ? 'text-gray-400 text-lg' :
                       ($i === 2 ? 'text-orange-400 text-lg' : 'text-gray-300 text-sm')) }}">
                    {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#'.($i+1))) }}
                </div>

                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-600 flex-shrink-0">
                    {{ strtoupper(substr($entry['user']->name, 0, 1)) }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 truncate">{{ $entry['user']->name }}</p>
                    <p class="text-xs text-gray-400">
                        {{ optional(optional($entry['user']->karyawan)->jabatan)->nama_jabatan ?? 'Karyawan' }}
                        · {{ $entry['level'] }}
                    </p>
                </div>

                {{-- Saldo --}}
                <div class="text-right flex-shrink-0">
                    <p class="font-bold text-indigo-700 text-lg">{{ number_format($entry['balance']) }}</p>
                    <p class="text-xs text-gray-400">poin</p>
                </div>

                {{-- Progress Bar (relatif terhadap poin tertinggi) --}}
                @php $max = $leaderboard->first()['balance'] ?: 1; @endphp
                <div class="w-24 bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full bg-indigo-400 transition-all"
                         style="width: {{ min(100, ($entry['balance'] / $max) * 100) }}%"></div>
                </div>
            </div>
            @endforeach

            @if($leaderboard->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <div class="text-4xl mb-2">📊</div>
                <p>Belum ada data poin karyawan.</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
