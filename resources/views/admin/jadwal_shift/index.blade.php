@extends('admin.layouts.app')

@section('title', 'Jadwal Shift Mingguan')

@section('content')

@if(session('success'))
<div class="bg-success/25 text-success text-sm rounded-md p-4 mb-4" role="alert">
    <span class="font-bold">Success!</span>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-danger/25 text-danger text-sm rounded-md p-4 mb-4" role="alert">
    <span class="font-bold">Error!</span>
    {{ session('error') }}
</div>
@endif

<script>
    setTimeout(function() {
        document.querySelectorAll('[role="alert"]').forEach(alert => {
            alert.style.display = 'none';
        });
    }, 3000);
</script>

<!-- HEADER -->
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">
        Atur Jadwal Shift Karyawan
    </h4>
</div>

<div class="mb-6">

    <form action="{{ route('jadwal-shift.store') }}" method="POST">
        @csrf

        <!-- PERIODE -->
        <div class="card mb-5 p-5">

            <h5 class="font-semibold mb-4 text-gray-700">
                Periode Jadwal
            </h5>

            <div class="flex flex-wrap gap-4">

                <div>
                    <label class="block text-sm font-medium mb-2">
                        Tanggal Mulai
                    </label>

                    <input type="date"
                        name="tanggal_mulai"
                        class="border rounded-lg px-4 py-2 w-64 focus:ring focus:ring-blue-200"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">
                        Tanggal Selesai
                    </label>

                    <input type="date"
                        name="tanggal_selesai"
                        class="border rounded-lg px-4 py-2 w-64 focus:ring focus:ring-blue-200"
                        required>
                </div>

            </div>

            <p class="text-xs text-gray-500 mt-3">
                * Jadwal hanya boleh dibuat untuk hari <b>Senin – Jumat</b>.
                Sabtu dan Minggu otomatis libur.
            </p>

        </div>

        <!-- TABLE -->
        <div class="card">
            <div class="overflow-x-auto">

                <table class="w-full border-collapse">

                    <thead class="bg-gray-100 border-b">
                        <tr class="text-sm">
                            <th class="px-4 py-3 text-left w-16">No</th>
                            <th class="px-4 py-3 text-left">Nama Karyawan</th>
                            <th class="px-4 py-3 text-left w-64">Shift</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @foreach($karyawans as $i => $karyawan)

                        <tr class="hover:bg-gray-50">

                            <td class="px-4 py-3">
                                {{ $i + 1 }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $karyawan->user->name }}
                            </td>

                            <td class="px-4 py-3">

                                <select
                                    name="shift[{{ $karyawan->id }}]"
                                    class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-blue-200"
                                    required>

                                    <option value="">-- Pilih Shift --</option>

                                    @foreach($shifts as $shift)

                                    <option value="{{ $shift->id }}">

                                        {{ ucfirst($shift->nama_shift) }}
                                        (
                                        {{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }}
                                        )

                                    </option>

                                    @endforeach

                                </select>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>
        </div>

        <!-- BUTTON -->
        <div class="mt-5">

            <button
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">

                Simpan Jadwal Shift

            </button>

        </div>

    </form>

</div>

@endsection