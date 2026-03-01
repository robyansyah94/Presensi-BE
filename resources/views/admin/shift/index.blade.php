@extends('admin.layouts.app')

@section('title', 'Data Shift')

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

<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">DATA SHIFT</h4>

    <a href="{{ route('shift.create') }}"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        + Tambah Shift
    </a>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr class="text-sm">
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nama Shift</th>
                    <th class="px-4 py-3 text-left">Jam Masuk</th>
                    <th class="px-4 py-3 text-left">Jam Pulang</th>
                    <th class="px-4 py-3 text-left">Toleransi Telat</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($shifts as $i => $shift)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $i + 1 }}</td>

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ ucfirst($shift->nama_shift) }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $shift->toleransi_telat }} menit
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">

                            <a href="{{ route('shift.edit', $shift->id) }}"
                                class="btn rounded-full border border-warning text-warning hover:bg-warning hover:text-white">
                                Edit
                            </a>

                            <form action="{{ route('shift.destroy', $shift->id) }}"
                                method="POST"
                                id="delete-form-{{ $shift->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="confirmDelete('{{ $shift->id }}')"
                                    class="btn rounded-full border border-danger text-danger hover:bg-danger hover:text-white">
                                    Hapus
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-500">
                        Data shift belum tersedia.
                    </td>
                </tr>
                @endforelse
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
        text: "Data shift ini akan dihapus permanen!",
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