@extends('admin.layouts.app')

@section('title', 'Lokasi Kantor')

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

<!-- Page Title -->
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">DATA LOKASI KANTOR</h4>

    <a href="{{ route('lokasi-kantor.create') }}"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        + Tambah Lokasi Kantor
    </a>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr class="text-sm">
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nama Kantor</th>
                    <th class="px-4 py-3 text-left">Latitude</th>
                    <th class="px-4 py-3 text-left">Longitude</th>
                    <th class="px-4 py-3 text-left">Radius (meter)</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($lokasiKantors as $i => $lokasi)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $i + 1 }}</td>

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $lokasi->nama_kantor }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $lokasi->latitude }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $lokasi->longitude }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ number_format($lokasi->radius_meter) }} m
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">

                            <!-- EDIT -->
                            <a href="{{ route('lokasi-kantor.edit', $lokasi->id) }}"
                                class="btn rounded-full border border-warning text-warning hover:bg-warning hover:text-white">
                                Edit
                            </a>

                            <!-- HAPUS -->
                            <form action="{{ route('lokasi-kantor.destroy', $lokasi->id) }}"
                                method="POST"
                                id="delete-form-{{ $lokasi->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    data-id="{{ $lokasi->id }}"
                                    data-nama="{{ $lokasi->nama_kantor }}"
                                    class="btn-hapus btn rounded-full border border-danger text-danger hover:bg-danger hover:text-white">
                                    Hapus
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @endforeach

                @if($lokasiKantors->count() == 0)
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-500">
                        Data lokasi kantor belum tersedia.
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
    setTimeout(function() {
        document.querySelectorAll('[role="alert"]').forEach(function(el) {
            el.style.display = 'none';
        });
    }, 2000);

    document.querySelectorAll('.btn-hapus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var nama = this.dataset.nama;
            Swal.fire({
                title: "Hapus Lokasi Kantor?",
                text: '"' + nama + '" akan dihapus permanen!',
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then(function(result) {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });
</script>
@endpush