@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Page Title -->
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">DATA KARYAWAN</h4>

    <a href="{{ route('karyawan.create') }}"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        + Tambah Karyawan
    </a>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr class="text-sm">
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Karyawan</th>
                    <th class="px-4 py-3 text-left">Nip</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Jabatan</th>
                    <th class="px-4 py-3 text-left">Role</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($karyawan as $i => $k)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 flex items-center gap-3">
                        <img src="{{ $k->foto ? asset('storage/'.$k->foto) : asset('admin/images/users/avatar-1.jpg') }}"
                            class="w-10 h-10 rounded-full object-cover">
                        <span class="font-semibold text-gray-800">
                            {{ $k->user->name }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $k->nip }}</td>
                    <td class="px-4 py-3">{{ $k->user->email }}</td>
                    <td class="px-4 py-3">{{ $k->jabatan->nama_jabatan }}</td>
                    <td class="px-4 py-3">{{ $k->user->role }}</td>

                    <!-- AKSI -->
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">

                            <a href="#"
                                class="btn rounded-full border border-info text-info hover:bg-info hover:text-white">
                                Detail
                            </a>

                            <a href="#"
                                class="btn rounded-full border border-warning text-warning hover:bg-warning hover:text-white">
                                Edit
                            </a>

                            <form action="#" method="POST"
                                onsubmit="return confirm('Yakin hapus data?')">
                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn rounded-full border border-danger text-danger hover:bg-danger hover:text-white">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection