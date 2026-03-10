@extends('admin.layouts.app')

@section('title', 'Edit Shift')

@section('content')

<div class="flex items-center justify-between mb-5">
    <h4 class="text-default-900 text-lg font-semibold">Edit Shift</h4>
</div>

<div class="card p-6">
    <form action="{{ route('shift.update', $shift->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid lg:grid-cols-2 gap-6">

            {{-- Nama Shift --}}
            <div>
                <label class="text-default-800 text-sm font-medium inline-block mb-2">
                    Nama Shift
                </label>

                <input type="text"
                    name="nama_shift"
                    value="{{ old('nama_shift', $shift->nama_shift) }}"
                    class="form-input"
                    required>

                @error('nama_shift')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Toleransi Telat --}}
            <div>
                <label class="text-default-800 text-sm font-medium inline-block mb-2">
                    Toleransi Telat (Menit)
                </label>

                <input type="number"
                    name="toleransi_telat"
                    value="{{ old('toleransi_telat', $shift->toleransi_telat) }}"
                    class="form-input"
                    required>

                @error('toleransi_telat')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jam Masuk --}}
            <div>
                <label class="text-default-800 text-sm font-medium inline-block mb-2">
                    Jam Masuk
                </label>

                <input type="time"
                    name="jam_masuk"
                    value="{{ old('jam_masuk', $shift->jam_masuk) }}"
                    class="form-input"
                    required>

                @error('jam_masuk')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jam Pulang --}}
            <div>
                <label class="text-default-800 text-sm font-medium inline-block mb-2">
                    Jam Pulang
                </label>

                <input type="time"
                    name="jam_pulang"
                    value="{{ old('jam_pulang', $shift->jam_pulang) }}"
                    class="form-input"
                    required>

                @error('jam_pulang')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="mt-6">
            <button type="submit" class="btn bg-primary text-white">
                Update Shift
            </button>

            <a href="{{ route('shift.index') }}" class="btn border ml-2">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection