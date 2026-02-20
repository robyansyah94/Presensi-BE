@extends('admin.layouts.app')

@section('title', 'Edit Jabatan')

@section('content')

<div class="flex items-center justify-between mb-5">
    <h4 class="text-default-900 text-lg font-semibold">Edit Jabatan</h4>
</div>

<div class="card p-6">
    <form action="{{ route('jabatan.update', $jabatan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nama Jabatan -->
        <div class="mb-4">
            <label class="text-default-800 text-sm font-medium inline-block mb-2">
                Nama Jabatan
            </label>
            <input type="text"
                name="nama_jabatan"
                value="{{ $jabatan->nama_jabatan }}"
                class="form-input"
                required>

            @error('nama_jabatan')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Keterangan/deskripsi -->
        <div class="mb-4">
            <label class="text-default-800 text-sm font-medium inline-block mb-2">
                Deskripsi
            </label>
            <textarea name="keterangan"
                rows="2"
                class="form-input">{{ old('keterangan', $jabatan->keterangan) }}</textarea>

            @error('keterangan')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Button -->
        <div class="mt-6">
            <button type="submit" class="btn bg-primary text-white">
                Update Jabatan
            </button>
            <a href="{{ route('jabatan.index') }}" class="btn border ml-2">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection