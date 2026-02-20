@extends('admin.layouts.app')

@section('title', 'Data Jabatan')

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
        document.querySelectorAll('[role="alert"]').forEach(alert => {
            alert.style.display = 'none';
        });
    }, 2000);
</script>

<!-- Page Title -->
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">DATA JABATAN</h4>

    <a href="{{ route('jabatan.create') }}"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        + Tambah Jabatan
    </a>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr class="text-sm">
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nama Jabatan</th>
                    <th class="px-4 py-3 text-left">Deskripsi</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($jabatans as $i => $jabatan)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $i + 1 }}</td>

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $jabatan->nama_jabatan }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $jabatan->keterangan ?? '-' }}

                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">

                            <!-- EDIT -->
                            <a href="{{ route('jabatan.edit', $jabatan->id) }}"
                                class="btn rounded-full border border-warning text-warning hover:bg-warning hover:text-white">
                                Edit
                            </a>

                            <!-- HAPUS -->
                            <form action="{{ route('jabatan.destroy', $jabatan->id) }}"
                                method="POST"
                                id="delete-form-{{ $jabatan->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="confirmDelete('{{ $jabatan->id }}')"
                                    class="btn rounded-full border border-danger text-danger hover:bg-danger hover:text-white">
                                    Hapus
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @endforeach

                @if($jabatans->count() == 0)
                <tr>
                    <td colspan="3" class="text-center py-6 text-gray-500">
                        Data jabatan belum tersedia.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data jabatan ini akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>

@endpush