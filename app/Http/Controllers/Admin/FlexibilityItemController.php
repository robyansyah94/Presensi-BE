<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlexibilityItem;
use Illuminate\Http\Request;

class FlexibilityItemController extends Controller
{
    public function index()
    {
        $items = FlexibilityItem::latest()->get();
        return view('admin.integrity.marketplace.index', compact('items'));
    }

    public function create()
    {
        return view('admin.integrity.marketplace.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_name'         => 'required|string|max:100',
            'description'       => 'nullable|string',
            'icon'              => 'nullable|string|max:10',
            'token_type'        => 'required|in:late_tolerance,wfh,excuse',
            'tolerance_minutes' => 'nullable|integer|min:1|required_if:token_type,late_tolerance',
            'point_cost'        => 'required|integer|min:1',
            'stock_limit'       => 'nullable|integer|min:1',
            'is_active'         => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['icon']      = $data['icon'] ?? '🎫';

        FlexibilityItem::create($data);

        return redirect()->route('admin.integrity.marketplace.index')
            ->with('success', 'Item marketplace berhasil ditambahkan!');
    }

    public function edit(FlexibilityItem $item)
    {
        return view('admin.integrity.marketplace.edit', compact('item'));
    }

    public function update(Request $request, FlexibilityItem $item)
    {
        $data = $request->validate([
            'item_name'         => 'required|string|max:100',
            'description'       => 'nullable|string',
            'icon'              => 'nullable|string|max:10',
            'token_type'        => 'required|in:late_tolerance,wfh,excuse',
            'tolerance_minutes' => 'nullable|integer|min:1|required_if:token_type,late_tolerance',
            'point_cost'        => 'required|integer|min:1',
            'stock_limit'       => 'nullable|integer|min:1',
            'is_active'         => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $item->update($data);

        return redirect()->route('admin.integrity.marketplace.index')
            ->with('success', 'Item marketplace berhasil diperbarui!');
    }

    public function destroy(FlexibilityItem $item)
    {
        $item->delete();
        return redirect()->route('admin.integrity.marketplace.index')
            ->with('success', 'Item berhasil dihapus.');
    }
}