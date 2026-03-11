@extends('admin.layouts.app')

@section('title', 'Kategori Penilaian')

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
    <h4 class="text-default-900 text-lg font-semibold">KATEGORI PENILAIAN</h4>
    <a href="{{ route('admin.assessment.categories.create') }}"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        + Tambah Indikator Baru
    </a>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr class="text-sm">
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nama Indikator</th>
                    <th class="px-4 py-3 text-left">Deskripsi</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($categories as $i => $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $categories->firstItem() + $i }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $category->description ?? '-' }}</td>

                    {{-- Toggle Status --}}
                    <td class="px-4 py-3 text-center">
                        <form action="{{ route('admin.assessment.categories.toggle', $category) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="btn rounded-full text-xs
                                    {{ $category->is_active
                                        ? 'border border-success text-success hover:bg-success hover:text-white'
                                        : 'border border-danger text-danger hover:bg-danger hover:text-white' }}">
                                {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.assessment.categories.edit', $category) }}"
                                class="btn rounded-full border border-warning text-warning hover:bg-warning hover:text-white">
                                Edit
                            </a>
                            <form action="{{ route('admin.assessment.categories.destroy', $category) }}"
                                method="POST"
                                id="delete-form-{{ $category->id }}">
                                @csrf @method('DELETE')
                                <button type="button"
                                    onclick="confirmDelete('{{ $category->id }}')"
                                    class="btn rounded-full border border-danger text-danger hover:bg-danger hover:text-white">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-500">
                        Belum ada kategori penilaian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($categories->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $categories->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Kategori ini akan dihapus permanen!",
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