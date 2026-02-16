@extends('admin.layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Tambah Karyawan</h4>
    </div>

    <div class="p-6">
        <form action="{{ route('karyawan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid lg:grid-cols-2 gap-6">

                <!-- Nama -->
                <div>
                    <label for="name"
                        class="text-default-800 text-sm font-medium inline-block mb-2">
                        Nama
                    </label>
                    <input type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        placeholder="Masukkan Nama">
                </div>

                <!-- Email -->
                <div>
                    <label for="email"
                        class="text-default-800 text-sm font-medium inline-block mb-2">
                        Email
                    </label>
                    <input type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="Masukkan Email">
                </div>

                <!-- NIP -->
                <div>
                    <label for="nip"
                        class="text-default-800 text-sm font-medium inline-block mb-2">
                        NIP
                    </label>
                    <input type="text"
                        id="nip"
                        name="nip"
                        class="form-input"
                        placeholder="Masukkan NIP">
                </div>

                <!-- No HP -->
                <div>
                    <label for="no_hp"
                        class="text-default-800 text-sm font-medium inline-block mb-2">
                        No HP
                    </label>
                    <input type="number"
                        id="no_hp"
                        name="no_hp"
                        class="form-input"
                        placeholder="Masukkan No HP">
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat"
                        class="text-default-800 text-sm font-medium inline-block mb-2">
                        Alamat
                    </label>
                    <input type="text"
                        id="alamat"
                        name="alamat"
                        class="form-input"
                        placeholder="Masukkan Alamat">
                </div>

                <!-- Jabatan -->
                <div>
                    <label for="jabatan_id"
                        class="text-default-800 text-sm font-medium inline-block mb-2">
                        Jabatan
                    </label>
                    <select name="jabatan_id"
                        id="jabatan_id"
                        class="form-select">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($jabatan as $j)
                        <option value="{{ $j->id }}">
                            {{ $j->nama_jabatan }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Foto 1 -->
                <div class="lg:col-span-2">
                    <label for="foto"
                        class="text-default-800 text-sm font-medium inline-block mb-2">
                        Foto
                    </label>
                    <input type="file"
                        id="foto"
                        name="foto"
                        class="form-input">
                </div>

            </div>

            <!-- Button -->
            <div class="mt-6">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>

@endsection