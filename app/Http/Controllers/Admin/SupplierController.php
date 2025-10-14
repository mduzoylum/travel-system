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
        // Admin olmayan kullanıcılar için API entegrasyonu olmayan tedarikçileri göster
        if (!auth()->user()->isAdmin()) {
            $suppliers = Supplier::whereNull('api_endpoint')
                ->whereNull('api_credentials')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $suppliers = Supplier::orderBy('created_at', 'desc')->paginate(15);
        }
        
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
            'types' => 'required|array|min:1',
            'types.*' => 'required|string|in:hotel,flight,car,activity,transfer',
            'description' => 'nullable|string',
            'api_endpoint' => 'nullable|url',
            'api_version' => 'nullable|string|max:20',
            'api_username' => 'nullable|string|max:100',
            'api_password' => 'nullable|string|max:100',
            'api_key' => 'nullable|string|max:255',
            'sync_frequency' => 'nullable|string',
            'is_active' => 'nullable|in:on,1,true',
            'sync_enabled' => 'nullable|in:on,1,true'
        ]);

        // Sadece izin verilen alanları al
        $data = [
            'name' => $request->name,
            'types' => $request->types,
            'description' => $request->description,
            'api_endpoint' => $request->api_endpoint,
            'api_version' => $request->api_version,
            'sync_frequency' => $request->sync_frequency,
            'is_active' => $request->has('is_active'),
            'sync_enabled' => $request->has('sync_enabled'),
        ];

        // API credentials'ı JSON olarak sakla
        $apiCredentials = [];
        if ($request->api_username) {
            $apiCredentials['username'] = $request->api_username;
        }
        if ($request->api_password) {
            $apiCredentials['password'] = $request->api_password;
        }
        if ($request->api_key) {
            $apiCredentials['api_key'] = $request->api_key;
        }
        
        $data['api_credentials'] = !empty($apiCredentials) ? $apiCredentials : null;

        $supplier = Supplier::create($data);

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', 'Tedarikçi başarıyla eklendi.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['hotels']);
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        // API entegrasyonu olan tedarikçileri sadece admin düzenleyebilir
        if ($supplier->api_endpoint || $supplier->api_credentials) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'API entegrasyonu olan tedarikçileri sadece admin kullanıcılar düzenleyebilir.');
            }
        }
        
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        // API entegrasyonu olan tedarikçileri sadece admin güncelleyebilir
        if ($supplier->api_endpoint || $supplier->api_credentials) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'API entegrasyonu olan tedarikçileri sadece admin kullanıcılar güncelleyebilir.');
            }
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'types' => 'required|array|min:1',
            'types.*' => 'required|string|in:hotel,flight,car,activity,transfer',
            'description' => 'nullable|string',
            'api_endpoint' => 'nullable|url',
            'api_version' => 'nullable|string|max:20',
            'api_username' => 'nullable|string|max:100',
            'api_password' => 'nullable|string|max:100',
            'api_key' => 'nullable|string|max:255',
            'sync_frequency' => 'nullable|string',
            'is_active' => 'nullable|in:on,1,true',
            'sync_enabled' => 'nullable|in:on,1,true'
        ]);

        // Debug: Form verilerini logla
        Log::info('Supplier update request data:', $request->all());
        Log::info('is_active checkbox: ' . ($request->has('is_active') ? 'checked' : 'unchecked'));
        Log::info('sync_enabled checkbox: ' . ($request->has('sync_enabled') ? 'checked' : 'unchecked'));
        Log::info('Final is_active value: ' . ($request->has('is_active') ? 'true' : 'false'));
        Log::info('Final sync_enabled value: ' . ($request->has('sync_enabled') ? 'true' : 'false'));

        // Sadece izin verilen alanları al
        $data = [
            'name' => $request->name,
            'types' => $request->types,
            'description' => $request->description,
            'api_endpoint' => $request->api_endpoint,
            'api_version' => $request->api_version,
            'sync_frequency' => $request->sync_frequency,
            'is_active' => $request->has('is_active'),
            'sync_enabled' => $request->has('sync_enabled'),
        ];

        // API credentials'ı JSON olarak sakla
        $apiCredentials = [];
        if ($request->api_username) {
            $apiCredentials['username'] = $request->api_username;
        }
        if ($request->api_password) {
            $apiCredentials['password'] = $request->api_password;
        } elseif (!$request->api_password && $supplier->api_credentials) {
            // Şifre değiştirilmemişse mevcut şifreyi koru
            $existingCredentials = json_decode($supplier->api_credentials, true);
            if (isset($existingCredentials['password'])) {
                $apiCredentials['password'] = $existingCredentials['password'];
            }
        }
        if ($request->api_key) {
            $apiCredentials['api_key'] = $request->api_key;
        }
        
        $data['api_credentials'] = !empty($apiCredentials) ? $apiCredentials : null;

        $supplier->update($data);

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', 'Tedarikçi başarıyla güncellendi.');
    }

    public function destroy(Supplier $supplier)
    {
        // API entegrasyonu olan tedarikçileri sadece admin silebilir
        if ($supplier->api_endpoint || $supplier->api_credentials) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'API entegrasyonu olan tedarikçileri sadece admin kullanıcılar silebilir.');
            }
        }
        
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Tedarikçi başarıyla silindi.');
    }

    public function sync(Supplier $supplier)
    {
        try {
            // Mock API servisini kullan
            $apiService = new MockApiService($supplier->api_endpoint ?? 'https://mock-api.example.com');
            
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
            $apiService = new MockApiService($supplier->api_endpoint ?? 'https://mock-api.example.com');
            
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