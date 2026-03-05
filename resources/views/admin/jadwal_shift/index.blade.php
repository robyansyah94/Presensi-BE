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
    }, 2000);
</script>

<div class="mb-6">
    <h4 class="text-lg font-semibold mb-3">Atur Jadwal Shift Mingguan (Senin–Jumat)</h4>

    <form action="{{ route('jadwal-shift.store') }}" method="POST">
        @csrf

        <div class="flex gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai"
                    class="border rounded-lg px-4 py-2 w-64"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai"
                    class="border rounded-lg px-4 py-2 w-64"
                    required>
            </div>
        </div>

        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Nama Karyawan</th>
                            <th class="px-4 py-3 text-left">Shift</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach($karyawans as $i => $karyawan)
                        <tr>
                            <td class="px-4 py-3">{{ $i + 1 }}</td>

                            <td class="px-4 py-3">
                                {{ $karyawan->user->name }}
                            </td>

                            <td class="px-4 py-3">
                                <select name="shift[{{ $karyawan->id }}]"
                                    class="border rounded-lg px-3 py-2 w-full"
                                    required>
                                    <option value="">-- Pilih Shift --</option>
                                    @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">
                                        {{ ucfirst($shift->nama_shift) }}
                                        ({{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }})
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

        <div class="mt-4">
            <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Simpan Jadwal Mingguan
            </button>
        </div>

    </form>
</div>

@endsection