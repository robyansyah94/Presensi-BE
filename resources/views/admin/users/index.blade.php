@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

@if(session('success'))
<div class="bg-success/25 text-success text-sm rounded-md p-4 mb-4" role="alert">
    <span class="font-bold">Success!</span>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-danger/25 text-danger text-sm rounded-md p-4 mb-4" role="alert">
    <span class="font-bold">Danger!</span>
    {{ session('error') }}
</div>
@endif

<script>
    setTimeout(function() {
        let alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 1500); //detik
</script>


<!-- Page Title -->
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">DATA USER</h4>

    <a href="{{ route('users.create') }}"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        + Tambah User
    </a>
</div>

<div class="flex gap-2 mb-4">

    <a href="{{ route('users.index') }}"
        class="px-4 py-2 rounded 
       {{ request('role') == null ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
        Semua
    </a>

    <a href="{{ route('users.index', ['role' => 'karyawan']) }}"
        class="px-4 py-2 rounded 
       {{ request('role') == 'karyawan' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
        Karyawan
    </a>

    <a href="{{ route('users.index', ['role' => 'admin']) }}"
        class="px-4 py-2 rounded 
       {{ request('role') == 'admin' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
        Admin
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
                @foreach($users as $i => $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $i + 1 }}</td>

                    <!-- NAMA + FOTO -->
                    <td class="px-4 py-3 flex items-center gap-3">
                        <img
                            src="{{ $user->karyawan && $user->karyawan->foto 
                ? asset('storage/'.$user->karyawan->foto) 
                : asset('admin/images/users/avatar-1.jpg') }}"
                            class="w-10 h-10 rounded-full object-cover">

                        <span class="font-semibold text-gray-800">
                            {{ $user->name }}
                        </span>
                    </td>

                    <!-- NIP -->
                    <td class="px-4 py-3">
                        {{ $user->karyawan?->nip ?? '-' }}
                    </td>

                    <!-- EMAIL -->
                    <td class="px-4 py-3">
                        {{ $user->email }}
                    </td>

                    <!-- JABATAN -->
                    <td class="px-4 py-3">
                        {{ $user->karyawan?->jabatan?->nama_jabatan ?? '-' }}
                    </td>

                    <!-- ROLE -->
                    <td class="px-4 py-3 capitalize">
                        {{ $user->role }}
                    </td>

                    <!-- AKSI -->
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">

                            <!-- DETAIL -->
                            <button type="button"
                                class="btn-detail btn rounded-full border border-info text-info hover:bg-info hover:text-white"
                                data-nama="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-nip="{{ $user->karyawan?->nip }}"
                                data-nohp="{{ $user->karyawan?->no_hp }}"
                                data-alamat="{{ $user->karyawan?->alamat }}"
                                data-jabatan="{{ $user->karyawan?->jabatan?->nama_jabatan }}"
                                data-role="{{ $user->role }}"
                                data-foto="{{ $user->karyawan && $user->karyawan->foto 
                    ? asset('storage/'.$user->karyawan->foto) 
                    : asset('admin/images/users/avatar-1.jpg') }}">
                                Detail
                            </button>

                            <!-- EDIT -->
                            @if($user->karyawan)
                            <a href="{{ route('users.edit', $user->karyawan->id) }}"
                                class="btn rounded-full border border-warning text-warning hover:bg-warning hover:text-white">
                                Edit
                            </a>
                            @endif

                            <!-- HAPUS -->
                            @if($user->karyawan)
                            <form action="{{ route('users.destroy', $user->karyawan->id) }}"
                                method="POST"
                                id="delete-form-{{ $user->karyawan->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="confirmDelete('{{ $user->karyawan->id }}')"
                                    class="btn rounded-full border border-danger text-danger hover:bg-danger hover:text-white">
                                    Hapus
                                </button>
                            </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- hapus -->
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data karyawan ini akan dihapus permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                // Cari form berdasarkan ID dan submit
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>

<!-- detail karyawan -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const d = this.dataset;

                Swal.fire({
                    width: 350,
                    showCloseButton: true,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-xl shadow-lg'
                    },
                    html: `
                    <div style="font-family: 'Inter', sans-serif; color: #334155;">
                        <div style="text-align: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 20px;">
                            <div style="position: relative; display: inline-block;">
                                <img src="${d.foto}" 
                                     style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
                            </div>
                            <h3 style="margin: 15px 0 5px 0; font-size: 1.25rem; font-weight: 700; color: #1e293b;">${d.nama}</h3>
                            <span style="background-color: #eff6ff; color: #2563eb; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">
                                ${d.jabatan}
                            </span>
                        </div>

                        <div style="text-align: left; display: flex; flex-direction: column; gap: 12px; padding: 0 10px;">
                            ${detailItem('NIP', d.nip)}
                            ${detailItem('Email', d.email)}
                            ${detailItem('No HP', d.nohp)}
                            ${detailItem('Alamat', d.alamat)}
                            ${detailItem('Role', d.role)}
                        </div>
                    </div>
                    `
                });
            });
        });

        // Fungsi pembantu agar kode lebih bersih
        function detailItem(label, value) {
            return `
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #f8fafc; padding-bottom: 10px;">
                    <span style="font-size: 0.9rem; font-weight: 600; color: #94a3b8; min-width: 80px;">${label}</span>
                    <span style="font-size: 0.95rem; font-weight: 500; color: #334155; text-align: right; padding-left: 15px;">${value || '-'}</span>
                </div>
            `;
        }
    });
</script>

@endpush