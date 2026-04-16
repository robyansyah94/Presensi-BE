<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointRule;
use Illuminate\Http\Request;

class PointRuleController extends Controller
{
    public function index()
    {
        $rules = PointRule::latest()->get();
        return view('admin.integrity.rules.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.integrity.rules.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rule_name'           => 'required|string|max:100',
            'target_role'         => 'required|in:karyawan,admin,all',
            'condition_type'      => 'required|in:jam_masuk,menit_terlambat,menit_lebih_awal,status_presensi',
            'condition_operator'  => 'nullable|in:<,>,BETWEEN|required_unless:condition_type,status_presensi',
            'condition_value'     => 'required|string|max:20',
            'condition_value_max' => 'nullable|string|max:20|required_if:condition_operator,BETWEEN',
            'point_modifier'      => 'required|integer|not_in:0',
            'is_active'           => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        PointRule::create($data);

        return redirect()->route('admin.integrity.rules.index')
            ->with('success', 'Rule berhasil ditambahkan!');
    }

    public function edit(PointRule $rule)
    {
        return view('admin.integrity.rules.edit', compact('rule'));
    }

    public function update(Request $request, PointRule $rule)
    {
        $data = $request->validate([
            'rule_name'           => 'required|string|max:100',
            'target_role'         => 'required|in:karyawan,admin,all',
            'condition_type'      => 'required|in:jam_masuk,menit_terlambat,menit_lebih_awal,status_presensi',
            'condition_operator'  => 'nullable|in:<,>,BETWEEN|required_unless:condition_type,status_presensi',
            'condition_value'     => 'required|string|max:20',
            'condition_value_max' => 'nullable|string|max:20|required_if:condition_operator,BETWEEN',
            'point_modifier'      => 'required|integer|not_in:0',
            'is_active'           => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $rule->update($data);

        return redirect()->route('admin.integrity.rules.index')
            ->with('success', 'Rule berhasil diperbarui!');
    }

    public function destroy(PointRule $rule)
    {
        $rule->delete();
        return redirect()->route('admin.integrity.rules.index')
            ->with('success', 'Rule berhasil dihapus.');
    }

    public function toggle(PointRule $rule)
    {
        $rule->update(['is_active' => !$rule->is_active]);
        $status = $rule->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Rule berhasil {$status}.");
    }
}
