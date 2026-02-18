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

                            <button type="button"
                                class="btn-detail btn rounded-full border border-info text-info hover:bg-info hover:text-white"
                                data-nama="{{ $k->user->name }}"
                                data-email="{{ $k->user->email }}"
                                data-nip="{{ $k->nip }}"
                                data-nohp="{{ $k->no_hp }}"
                                data-alamat="{{ $k->alamat }}"
                                data-jabatan="{{ $k->jabatan->nama_jabatan }}"
                                data-foto="{{ $k->foto ? asset('storage/'.$k->foto) : asset('admin/images/users/avatar-1.jpg') }}">
                                Detail
                            </button>


                            <a href="{{ route('karyawan.edit', $k->id) }}"
                                class="btn rounded-full border border-warning text-warning hover:bg-warning hover:text-white">
                                Edit
                            </a>

                            <form action="{{ route('karyawan.destroy', $k->id) }}" method="POST"
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

@push('scripts')

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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