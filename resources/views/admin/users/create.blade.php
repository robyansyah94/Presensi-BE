@extends('admin.layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Tambah Karyawan</h4>
    </div>

    <div class="p-6">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid lg:grid-cols-2 gap-6">

                {{-- Nama --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">Nama</label>
                    <input type="text" name="name"
                        value="{{ old('name') }}"
                        class="form-input @error('name') border-red-500 @enderror">

                    @error('name')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- NIP --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">NIP</label>
                    <input type="text" name="nip"
                        value="{{ old('nip') }}"
                        class="form-input @error('nip') border-red-500 @enderror">

                    @error('nip')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">Email</label>
                    <input type="email" name="email"
                        value="{{ old('email') }}"
                        class="form-input @error('email') border-red-500 @enderror">

                    @error('email')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">Password</label>
                    <input type="password" name="password"
                        class="form-input @error('password') border-red-500 @enderror">

                    @error('password')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- No HP --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">No HP</label>
                    <input type="number" name="no_hp"
                        value="{{ old('no_hp') }}"
                        class="form-input @error('no_hp') border-red-500 @enderror">

                    @error('no_hp')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">Alamat</label>
                    <input type="text" name="alamat"
                        value="{{ old('alamat') }}"
                        class="form-input @error('alamat') border-red-500 @enderror">

                    @error('alamat')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Jabatan --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">Jabatan</label>
                    <select name="jabatan_id"
                        class="form-select @error('jabatan_id') border-red-500 @enderror">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($jabatan as $j)
                        <option value="{{ $j->id }}"
                            {{ old('jabatan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jabatan }}
                        </option>
                        @endforeach
                    </select>

                    @error('jabatan_id')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">Status</label>
                    <select name="status"
                        class="form-select @error('status') border-red-500 @enderror">
                        <option value="">-- Pilih Status --</option>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>

                    @error('status')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">Role</label>
                    <select name="role"
                        class="form-select @error('role') border-red-500 @enderror">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    </select>

                    @error('role')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Foto --}}
                <div class="lg:col-span-2">
                    <label class="text-default-800 text-sm font-medium inline-block mb-2">Foto</label>
                    <input type="file" name="foto"
                        class="form-input @error('foto') border-red-500 @enderror">

                    @error('foto')
                    <small class="text-red-500 text-sm">{{ $message }}</small>
                    @enderror
                </div>

            </div>

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