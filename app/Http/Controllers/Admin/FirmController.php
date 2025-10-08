<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DDD\Modules\Firm\Models\Firm;
use Illuminate\Http\Request;

class FirmController extends Controller
{
    public function index()
    {
        $firms = Firm::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.firms.index', compact('firms'));
    }

    public function create()
    {
        return view('admin.firms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:firms,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'service_fee' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        Firm::create($data);

        return redirect()->route('admin.firms.index')
            ->with('success', 'Firma başarıyla eklendi.');
    }

    public function show(Firm $firm)
    {
        $firm->load(['creditAccounts', 'contracts']);
        return view('admin.firms.show', compact('firm'));
    }

    public function edit(Firm $firm)
    {
        return view('admin.firms.edit', compact('firm'));
    }

    public function update(Request $request, Firm $firm)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:firms,email,' . $firm->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'service_fee' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $firm->update($data);

        return redirect()->route('admin.firms.index')
            ->with('success', 'Firma başarıyla güncellendi.');
    }

    public function destroy(Firm $firm)
    {
        $firm->delete();

        return redirect()->route('admin.firms.index')
            ->with('success', 'Firma başarıyla silindi.');
    }
} 