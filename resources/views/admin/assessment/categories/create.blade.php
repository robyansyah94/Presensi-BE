@extends('admin.layouts.app')

@section('title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')

<!-- Page Title -->
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">
        {{ isset($category) ? 'EDIT KATEGORI PENILAIAN' : 'TAMBAH KATEGORI PENILAIAN' }}
    </h4>
</div>

<div class="card">
    <div class="p-6">
        <form action="{{ isset($category)
                ? route('admin.assessment.categories.update', $category)
                : route('admin.assessment.categories.store') }}"
            method="POST">
            @csrf
            @if(isset($category)) @method('PUT') @endif

            <div class="grid lg:grid-cols-2 gap-6">

                {{-- Nama Indikator --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Nama Indikator <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        name="name"
                        value="{{ old('name', $category->name ?? '') }}"
                        placeholder="cth: Disiplin, Kerja Sama, Komunikasi"
                        class="form-input @error('name') border-red-500 @enderror">
                    @error('name')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="lg:col-span-2">
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Deskripsi
                        <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="description"
                        rows="4"
                        placeholder="Jelaskan indikator ini secara singkat..."
                        class="form-input @error('description') border-red-500 @enderror">{{ old('description', $category->description ?? '') }}</textarea>
                    @error('description')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            <div class="mt-6 flex gap-2">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    {{ isset($category) ? 'Simpan Perubahan' : 'Simpan' }}
                </button>

                <a href="{{ route('admin.assessment.categories.index') }}"
                    class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection