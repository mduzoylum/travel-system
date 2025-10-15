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
            'firm_id' => 'nullable|sometimes|exists:firms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'currency' => 'required|in:TRY,USD,EUR,GBP',
            'base_price' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:100'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['auto_renewal'] = $request->has('auto_renewal');
        
        // Boş string gönderilirse null yap
        if ($request->firm_id === '' || $request->firm_id === 'general') {
            $data['firm_id'] = null;
        }

        // Aynı otel ve firma için aktif kontrat var mı kontrol et
        $query = Contract::where('hotel_id', $request->hotel_id)
            ->where('is_active', true);
        
        if ($data['firm_id']) {
            // Firmaya özel kontrat kontrolü
            $query->where('firm_id', $data['firm_id']);
        } else {
            // Genel kontrat kontrolü (firm_id null)
            $query->whereNull('firm_id');
        }
        
        $existingContract = $query->first();

        if ($existingContract) {
            $firmName = $data['firm_id'] ? 'firma' : 'genel';
            return back()->withInput()
                ->with('error', "Bu otel için zaten aktif bir {$firmName} kontrat bulunmaktadır.");
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
            'firm_id' => 'nullable|sometimes|exists:firms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'currency' => 'required|in:TRY,USD,EUR,GBP',
            'base_price' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:100'
        ], [
            'firm_id.exists' => 'Seçilen firma bulunamadı.',
            'hotel_id.exists' => 'Seçilen otel bulunamadı.',
            'end_date.after' => 'Bitiş tarihi başlangıç tarihinden sonra olmalıdır.'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['auto_renewal'] = $request->has('auto_renewal');
        
        // Boş string gönderilirse null yap
        if ($request->firm_id === '' || $request->firm_id === 'general') {
            $data['firm_id'] = null;
        }

        // Aynı otel ve firma için başka aktif kontrat var mı kontrol et (kendisi hariç)
        $query = Contract::where('hotel_id', $request->hotel_id)
            ->where('is_active', true)
            ->where('id', '!=', $contract->id);
        
        if ($data['firm_id']) {
            // Firmaya özel kontrat kontrolü
            $query->where('firm_id', $data['firm_id']);
        } else {
            // Genel kontrat kontrolü (firm_id null)
            $query->whereNull('firm_id');
        }
        
        $existingContract = $query->first();

        if ($existingContract) {
            $firmName = $data['firm_id'] ? 'firma' : 'genel';
            return back()->withInput()
                ->with('error', "Bu otel için zaten aktif bir {$firmName} kontrat bulunmaktadır.");
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

    public function getContractsByHotel($hotelId)
    {
        $contracts = Contract::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->select('id', 'hotel_id', 'firm_id', 'base_price', 'currency', 'start_date', 'end_date')
            ->with('firm:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($contract) {
                return [
                    'id' => $contract->id,
                    'name' => ($contract->firm ? $contract->firm->name . ' - ' : '') . 
                              'Kontrat #' . $contract->id,
                    'base_price' => $contract->base_price,
                    'currency' => $contract->currency,
                    'start_date' => $contract->start_date,
                    'end_date' => $contract->end_date
                ];
            });

        return response()->json($contracts);
    }
} 