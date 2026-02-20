@extends('admin.layouts.app')

@section('title', 'Tambah Jabatan')

@section('content')

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Tambah Jabatan</h4>
    </div>

    <div class="p-6">
        <form action="{{ route('jabatan.store') }}" method="POST">
            @csrf

            <div class="grid lg:grid-cols-2 gap-6">

                {{-- Nama Jabatan --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Nama Jabatan
                    </label>

                    <input type="text"
                        name="nama_jabatan"
                        value="{{ old('nama_jabatan') }}"
                        class="form-input @error('nama_jabatan') border-red-500 @enderror">

                    @error('nama_jabatan')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div class="lg:col-span-2">
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Keterangan
                    </label>

                    <textarea name="keterangan"
                        rows="4"
                        class="form-input @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>

                    @error('keterangan')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            <div class="mt-6 flex gap-2">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Simpan
                </button>

                <a href="{{ route('jabatan.index') }}"
                    class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection