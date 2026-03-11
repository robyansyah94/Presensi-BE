<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentCategory;
use Illuminate\Http\Request;

class AssessmentCategoryController extends Controller
{
    //Daftar semua kategori penilaian
    public function index()
    {
        $categories = AssessmentCategory::latest()->paginate(10);
        return view('admin.assessment.categories.index', compact('categories'));
    }

    //Form tambah kategori baru
    public function create()
    {
        return view('admin.assessment.categories.create');
    }

    //Simpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:assessment_categories,name',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah ada.',
        ]);

        AssessmentCategory::create([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.assessment.categories.index')
            ->with('success', 'Kategori penilaian berhasil ditambahkan.');
    }

    /**
     * Form edit kategori
     */
    public function edit(AssessmentCategory $category)
    {
        return view('admin.assessment.categories.edit', compact('category'));
    }

    /**
     * Update kategori
     */
    public function update(Request $request, AssessmentCategory $category)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:assessment_categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah ada.',
        ]);

        $category->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.assessment.categories.index')
            ->with('success', 'Kategori penilaian berhasil diperbarui.');
    }

    /**
     * Toggle aktif / nonaktif kategori
     */
    public function toggleActive(AssessmentCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.assessment.categories.index')
            ->with('success', "Kategori berhasil {$status}.");
    }

    //Hapus kategori
    public function destroy(AssessmentCategory $category)
    {
        // Cegah hapus jika sudah punya data penilaian
        if ($category->details()->exists()) {
            return redirect()->route('admin.assessment.categories.index')
                ->with('error', 'Kategori tidak bisa dihapus karena sudah digunakan dalam penilaian.');
        }

        $category->delete();

        return redirect()->route('admin.assessment.categories.index')
            ->with('success', 'Kategori penilaian berhasil dihapus.');
    }
}
