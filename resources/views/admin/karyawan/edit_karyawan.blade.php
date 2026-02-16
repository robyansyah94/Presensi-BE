@extends('admin.layouts.app')

@section('content')

<div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">Edit Karyawan</h4>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Form Edit Karyawan</h4>
    </div>

    <div class="p-6">
        <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid lg:grid-cols-2 gap-6">

                <!-- Nama -->
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Nama
                    </label>
                    <input type="text"
                        name="name"
                        value="{{ $karyawan->user->name }}"
                        class="form-input"
                        required>
                </div>

                <!-- Email -->
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Email
                    </label>
                    <input type="email"
                        name="email"
                        value="{{ $karyawan->user->email }}"
                        class="form-input"
                        required>
                </div>

                <!-- NIP -->
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        NIP
                    </label>
                    <input type="text"
                        name="nip"
                        value="{{ $karyawan->nip }}"
                        class="form-input"
                        required>
                </div>

                <!-- No HP -->
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        No HP
                    </label>
                    <input type="text"
                        name="no_hp"
                        value="{{ $karyawan->no_hp }}"
                        class="form-input"
                        required>
                </div>

                <!-- Alamat -->
                <div class="lg:col-span-2">
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Alamat
                    </label>
                    <input type="text"
                        name="alamat"
                        value="{{ $karyawan->alamat }}"
                        class="form-input"
                        required>
                </div>

                <!-- Jabatan -->
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Jabatan
                    </label>
                    <select name="jabatan_id" class="form-select" required>
                        @foreach($jabatan as $j)
                        <option value="{{ $j->id }}"
                            {{ $karyawan->jabatan_id == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jabatan }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <br>
                <!-- Foto Lama -->
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Foto Saat Ini
                    </label>
                    <div>
                        @if($karyawan->foto)
                        <img src="{{ asset('storage/' . $karyawan->foto) }}"
                            class="w-24 h-24 object-cover rounded">
                        @else
                        <span class="text-sm text-gray-400">Belum ada foto</span>
                        @endif
                    </div>
                </div>

                <!-- Upload Foto Baru -->
                <div class="lg:col-span-2">
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">
                        Ganti Foto (Opsional)
                    </label>
                    <input type="file"
                        name="foto"
                        class="form-input">
                    <small class="text-gray-400">Kosongkan jika tidak ingin mengganti foto</small>
                </div>

            </div>

            <div class="mt-6">
                <button type="submit" class="btn bg-primary text-white">
                    Update Karyawan
                </button>
                <a href="{{ route('karyawan.index') }}" class="btn border ml-2">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection