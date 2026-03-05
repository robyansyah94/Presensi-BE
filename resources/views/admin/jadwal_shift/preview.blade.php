@extends('admin.layouts.app')

@section('title', 'Preview Jadwal Shift')

@section('content')

<h4 class="text-lg font-semibold mb-4">
    Preview Jadwal Shift Berdasarkan Periode
</h4>

<form method="GET" action="{{ route('jadwal-shift.preview') }}" class="mb-6 flex gap-4">

    <div>
        <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai"
            class="border rounded-lg px-4 py-2"
            value="{{ request('tanggal_mulai') }}"
            required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai"
            class="border rounded-lg px-4 py-2"
            value="{{ request('tanggal_selesai') }}"
            required>
    </div>

    <div class="flex items-end">
        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Tampilkan
        </button>
    </div>

</form>

@if(count($dates) > 0)

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">

            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nama Karyawan</th>

                    @foreach($dates as $date)
                    <th class="px-4 py-3 text-center">
                        {{ $date->locale('id')->translatedFormat('l') }}
                        <br>
                        <span class="text-xs text-gray-500">
                            {{ $date->format('d M') }}
                        </span>
                    </th>
                    @endforeach
                </tr>
            </thead>

            <tbody class="divide-y">

                @foreach($karyawans as $i => $karyawan)
                <tr class="hover:bg-gray-50">

                    <td class="px-4 py-3">
                        {{ $i + 1 }}
                    </td>

                    <td class="px-4 py-3 font-medium">
                        {{ $karyawan->user->name }}
                    </td>

                    @foreach($dates as $date)

                    @php
                    $jadwal = $karyawan->weekly_schedule[$date->format('Y-m-d')] ?? null;
                    @endphp

                    <td class="px-4 py-3 text-center">
                        @if($jadwal && $jadwal->shift)
                        <div class="font-semibold">
                            {{ ucfirst($jadwal->shift->nama_shift) }}
                        </div>

                        <div class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($jadwal->shift->jam_masuk)->format('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($jadwal->shift->jam_pulang)->format('H:i') }}
                        </div>
                        @else
                        <span class="text-gray-400 text-sm">
                            Libur / Belum Diatur
                        </span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach

            </tbody>

        </table>
    </div>
</div>

@endif

@endsection