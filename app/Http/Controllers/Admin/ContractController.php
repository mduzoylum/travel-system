<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DDD\Modules\Contract\Models\Contract;
use App\DDD\Modules\Contract\Models\Hotel;
use App\DDD\Modules\Firm\Models\Firm;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::with(['hotel', 'firm'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $hotels = Hotel::all();
        $firms = Firm::where('is_active', true)->get();
        return view('admin.contracts.create', compact('hotels', 'firms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'firm_id' => 'required|exists:firms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'currency' => 'required|in:TRY,USD,EUR,GBP',
            'base_price' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'service_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'auto_renewal' => 'boolean',
            'payment_terms' => 'nullable|string|max:100'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['auto_renewal'] = $request->has('auto_renewal');

        // Aynı otel ve firma için aktif kontrat var mı kontrol et
        $existingContract = Contract::where('hotel_id', $request->hotel_id)
            ->where('firm_id', $request->firm_id)
            ->where('is_active', true)
            ->first();

        if ($existingContract) {
            return back()->withInput()
                ->with('error', 'Bu otel ve firma için zaten aktif bir kontrat bulunmaktadır.');
        }

        $contract = Contract::create($data);

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Kontrat başarıyla oluşturuldu. Şimdi oda ekleyebilirsiniz.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['hotel', 'firm', 'rooms']);
        return view('admin.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $hotels = Hotel::all();
        $firms = Firm::where('is_active', true)->get();
        return view('admin.contracts.edit', compact('contract', 'hotels', 'firms'));
    }

    public function update(Request $request, Contract $contract)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'firm_id' => 'required|exists:firms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'currency' => 'required|in:TRY,USD,EUR,GBP',
            'base_price' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'service_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'auto_renewal' => 'boolean',
            'payment_terms' => 'nullable|string|max:100'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['auto_renewal'] = $request->has('auto_renewal');

        // Aynı otel ve firma için başka aktif kontrat var mı kontrol et (kendisi hariç)
        $existingContract = Contract::where('hotel_id', $request->hotel_id)
            ->where('firm_id', $request->firm_id)
            ->where('is_active', true)
            ->where('id', '!=', $contract->id)
            ->first();

        if ($existingContract) {
            return back()->withInput()
                ->with('error', 'Bu otel ve firma için zaten aktif bir kontrat bulunmaktadır.');
        }

        $contract->update($data);

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Kontrat başarıyla güncellendi.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Kontrat başarıyla silindi.');
    }

    public function rooms(Contract $contract)
    {
        $contract->load(['rooms']);
        return view('admin.contracts.rooms.index', compact('contract'));
    }

    public function addRoom(Request $request, Contract $contract)
    {
        $request->validate([
            'room_type' => 'required|string|max:100',
            'meal_plan' => 'required|string|max:50',
            'base_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $contract->rooms()->create($request->all());

        return redirect()->route('admin.contracts.rooms', $contract)
            ->with('success', 'Oda başarıyla eklendi.');
    }

    public function destroyRoom(Contract $contract, $roomId)
    {
        $room = $contract->rooms()->findOrFail($roomId);
        $room->delete();

        return redirect()->route('admin.contracts.rooms', $contract)
            ->with('success', 'Oda başarıyla silindi.');
    }
} 