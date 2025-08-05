<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use App\DDD\Modules\Supplier\Domain\ValueObjects\SupplierType;
use App\DDD\Modules\Supplier\Infrastructure\Services\MockApiService;
use App\DDD\Modules\Contract\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:hotel,flight,car,activity,transfer',
            'description' => 'nullable|string',
            'api_url' => 'nullable|url',
            'api_version' => 'nullable|string|max:10',
            'api_username' => 'nullable|string|max:100',
            'api_password' => 'nullable|string|max:100',
            'api_key' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sync_frequency' => 'nullable|string|in:hourly,daily,weekly,manual'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        // API credentials'ı JSON olarak sakla
        $apiCredentials = [];
        if ($request->api_username) $apiCredentials['username'] = $request->api_username;
        if ($request->api_password) $apiCredentials['password'] = $request->api_password;
        if ($request->api_key) $apiCredentials['api_key'] = $request->api_key;
        
        $data['api_credentials'] = !empty($apiCredentials) ? json_encode($apiCredentials) : null;

        Supplier::create($data);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Tedarikçi başarıyla eklendi.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['hotels']);
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:hotel,flight,car,activity,transfer',
            'description' => 'nullable|string',
            'api_url' => 'nullable|url',
            'api_version' => 'nullable|string|max:10',
            'api_username' => 'nullable|string|max:100',
            'api_password' => 'nullable|string|max:100',
            'api_key' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sync_frequency' => 'nullable|string|in:hourly,daily,weekly,manual'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        // API credentials'ı JSON olarak sakla
        $apiCredentials = [];
        if ($request->api_username) $apiCredentials['username'] = $request->api_username;
        if ($request->api_password) $apiCredentials['password'] = $request->api_password;
        if ($request->api_key) $apiCredentials['api_key'] = $request->api_key;
        
        // Şifre değiştirilmemişse mevcut şifreyi koru
        if (!$request->api_password && $supplier->api_credentials) {
            $existingCredentials = json_decode($supplier->api_credentials, true);
            if (isset($existingCredentials['password'])) {
                $apiCredentials['password'] = $existingCredentials['password'];
            }
        }
        
        $data['api_credentials'] = !empty($apiCredentials) ? json_encode($apiCredentials) : null;

        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Tedarikçi başarıyla güncellendi.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Tedarikçi başarıyla silindi.');
    }

    public function sync(Supplier $supplier)
    {
        try {
            // Mock API servisini kullan
            $apiService = new MockApiService($supplier->api_url ?? 'https://mock-api.example.com');
            
            $result = $apiService->getHotels();
            
            if ($result['success']) {
                // Otelleri veritabanına kaydet
                $hotelsCount = 0;
                foreach ($result['data'] as $hotelData) {
                    $hotel = Hotel::updateOrCreate(
                        ['external_id' => $hotelData['id'], 'supplier_id' => $supplier->id],
                        [
                            'name' => $hotelData['name'],
                            'city' => $hotelData['city'],
                            'country' => $hotelData['country'],
                            'stars' => $hotelData['stars'],
                            'description' => $hotelData['description'],
                            'min_price' => $hotelData['min_price'],
                            'is_active' => true
                        ]
                    );
                    $hotelsCount++;
                }
                
                // Son sync zamanını güncelle
                $supplier->update(['last_sync_at' => now()]);
                
                return response()->json([
                    'success' => true,
                    'message' => "{$hotelsCount} otel başarıyla senkronize edildi."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Supplier sync failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyon hatası: ' . $e->getMessage()
            ]);
        }
    }

    public function testConnection(Supplier $supplier)
    {
        try {
            // Mock API servisini kullan
            $apiService = new MockApiService($supplier->api_url ?? 'https://mock-api.example.com');
            
            $result = $apiService->testConnection();
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Supplier connection test failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bağlantı testi hatası: ' . $e->getMessage()
            ]);
        }
    }
} 