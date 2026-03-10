@extends('admin.layouts.app')

@section('title', 'Tambah Shift')

@section('content')

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Tambah Shift</h4>
    </div>

    <div class="p-6">
        <form action="{{ route('shift.store') }}" method="POST">
            @csrf

            <div class="grid lg:grid-cols-2 gap-6">

                {{-- Nama Shift --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Nama Shift
                    </label>

                    <input type="text"
                        name="nama_shift"
                        value="{{ old('nama_shift') }}"
                        class="form-input @error('nama_shift') border-red-500 @enderror">

                    @error('nama_shift')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Toleransi Telat --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Toleransi Telat (Menit)
                    </label>

                    <input type="number"
                        name="toleransi_telat"
                        value="{{ old('toleransi_telat') }}"
                        class="form-input @error('toleransi_telat') border-red-500 @enderror">

                    @error('toleransi_telat')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Jam Masuk --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Jam Masuk
                    </label>

                    <input type="time"
                        name="jam_masuk"
                        value="{{ old('jam_masuk') }}"
                        class="form-input @error('jam_masuk') border-red-500 @enderror">

                    @error('jam_masuk')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Jam Pulang --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Jam Pulang
                    </label>

                    <input type="time"
                        name="jam_pulang"
                        value="{{ old('jam_pulang') }}"
                        class="form-input @error('jam_pulang') border-red-500 @enderror">

                    @error('jam_pulang')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            <div class="mt-6 flex gap-2">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Simpan
                </button>

                <a href="{{ route('shift.index') }}"
                    class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection