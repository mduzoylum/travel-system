<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierGroup;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SupplierGroup::withCount('suppliers');
        
        // Grup tipi filtresi
        if ($request->has('group_type') && $request->group_type) {
            $query->where('group_type', $request->group_type);
        }
        
        $groups = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);
        
        return view('admin.supplier-groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.supplier-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:supplier_groups,name',
            'description' => 'nullable|string',
            'color' => 'required|string',
            'sort_order' => 'integer|min:0',
            'group_type' => 'required|in:report,profit,xml,manual'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->sort_order ?? 0;

        $group = SupplierGroup::create($data);

        Log::info('Supplier group created', ['group_id' => $group->id, 'name' => $group->name]);

        return redirect()->route('admin.supplier-groups.index')
            ->with('success', 'Tedarikçi grubu başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierGroup $supplierGroup)
    {
        $supplierGroup->load(['suppliers' => function($query) {
            $query->orderBy('name');
        }]);
        
        return view('admin.supplier-groups.show', compact('supplierGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierGroup $supplierGroup)
    {
        return view('admin.supplier-groups.edit', compact('supplierGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierGroup $supplierGroup)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:supplier_groups,name,' . $supplierGroup->id,
            'description' => 'nullable|string',
            'color' => 'required|string',
            'sort_order' => 'integer|min:0',
            'group_type' => 'required|in:report,profit,xml,manual'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->sort_order ?? 0;

        $supplierGroup->update($data);

        Log::info('Supplier group updated', ['group_id' => $supplierGroup->id, 'name' => $supplierGroup->name]);

        return redirect()->route('admin.supplier-groups.index')
            ->with('success', 'Tedarikçi grubu başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierGroup $supplierGroup)
    {
        // Gruba ait tedarikçi varsa silme
        if ($supplierGroup->suppliers()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Bu gruba ait tedarikçiler bulunduğu için silinemez. Önce tedarikçileri başka gruba taşıyın.');
        }

        $supplierGroup->delete();

        Log::info('Supplier group deleted', ['group_id' => $supplierGroup->id, 'name' => $supplierGroup->name]);

        return redirect()->route('admin.supplier-groups.index')
            ->with('success', 'Tedarikçi grubu başarıyla silindi.');
    }

    /**
     * Toggle group status
     */
    public function toggleStatus(SupplierGroup $supplierGroup)
    {
        $supplierGroup->update(['is_active' => !$supplierGroup->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'Grup durumu başarıyla güncellendi.',
            'is_active' => $supplierGroup->is_active
        ]);
    }
}
