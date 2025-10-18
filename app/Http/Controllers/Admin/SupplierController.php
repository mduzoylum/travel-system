<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use App\DDD\Modules\Supplier\Domain\ValueObjects\SupplierType;
use App\DDD\Modules\Supplier\Infrastructure\Services\MockApiService;
use App\DDD\Modules\Contract\Models\Hotel;
use App\Models\SupplierGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index()
    {
        // Admin olmayan kullanıcılar sadece API entegrasyonu olmayan tedarikçileri görebilir
        if (!auth()->user()->isAdmin()) {
            $suppliers = Supplier::with('group')
                ->whereNull('api_endpoint')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $suppliers = Supplier::with('group')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }
        
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $groups = SupplierGroup::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.suppliers.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'nullable|exists:supplier_groups,id',
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'accounting_code' => 'nullable|string|max:50',
            'types' => 'required|array|min:1',
            'types.*' => 'required|string|in:hotel,flight,car,activity,transfer',
            'description' => 'nullable|string',
            'api_endpoint' => 'nullable|url',
            'api_version' => 'nullable|string|max:20',
            'api_username' => 'nullable|string|max:100',
            'api_password' => 'nullable|string|max:100',
            'api_key' => 'nullable|string|max:255',
            'sync_frequency' => 'nullable|string',
            'payment_type' => 'nullable|in:cari,credit_card',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|in:on,1,true',
            'sync_enabled' => 'nullable|in:on,1,true'
        ]);

        // Sadece izin verilen alanları al
        $data = [
            'group_id' => $request->group_id,
            'name' => $request->name,
            'country' => $request->country,
            'city' => $request->city,
            'accounting_code' => $request->accounting_code,
            'types' => $request->types,
            'description' => $request->description,
            'api_endpoint' => $request->api_endpoint,
            'api_version' => $request->api_version,
            'sync_frequency' => $request->sync_frequency,
            'payment_type' => $request->payment_type ?? 'cari',
            'address' => $request->address,
            'tax_rate' => $request->tax_rate ?? 0,
            'is_active' => $request->has('is_active'),
            'sync_enabled' => $request->has('sync_enabled'),
        ];

        // Logo yükleme
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('suppliers/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Ödeme periyodlarını işle
        $paymentPeriods = [];
        if ($request->payment_period_type === 'days') {
            $paymentPeriods = [
                'type' => 'days',
                'before_booking' => $request->payment_days_before ?? 0,
                'after_booking' => $request->payment_days_after ?? 0
            ];
        } elseif ($request->payment_period_type === 'monthly') {
            $paymentPeriods = [
                'type' => 'monthly',
                'days' => $request->payment_monthly_days ?? []
            ];
        }
        $data['payment_periods'] = $paymentPeriods;

        // İletişim kişilerini işle
        $contactPersons = [];
        if ($request->contact_names) {
            foreach ($request->contact_names as $index => $name) {
                if ($name && isset($request->contact_phones[$index])) {
                    $contactPersons[] = [
                        'name' => $name,
                        'phone' => $request->contact_phones[$index],
                        'email' => $request->contact_emails[$index] ?? null
                    ];
                }
            }
        }
        $data['contact_persons'] = $contactPersons;

        // E-postaları işle
        $emails = [];
        if ($request->supplier_emails) {
            foreach ($request->supplier_emails as $index => $email) {
                if ($email && isset($request->supplier_email_names[$index])) {
                    $emails[] = [
                        'name' => $request->supplier_email_names[$index],
                        'email' => $email,
                        'is_primary' => $request->supplier_email_primary[$index] ?? false
                    ];
                }
            }
        }
        $data['emails'] = $emails;

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
        // Admin olmayan kullanıcılar XML/API entegrasyonu olan tedarikçileri görüntüleyemez
        if (!auth()->user()->isAdmin() && ($supplier->api_endpoint || $supplier->api_credentials)) {
            abort(403, 'Bu tedarikçiye erişim yetkiniz yok. Sadece admin kullanıcılar XML entegrasyonu olan tedarikçileri görüntüleyebilir.');
        }
        
        $supplier->load(['hotels', 'group']);
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
        
        $groups = SupplierGroup::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.suppliers.edit', compact('supplier', 'groups'));
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
            'group_id' => 'nullable|exists:supplier_groups,id',
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'accounting_code' => 'nullable|string|max:50',
            'types' => 'required|array|min:1',
            'types.*' => 'required|string|in:hotel,flight,car,activity,transfer',
            'description' => 'nullable|string',
            'api_endpoint' => 'nullable|url',
            'api_version' => 'nullable|string|max:20',
            'api_username' => 'nullable|string|max:100',
            'api_password' => 'nullable|string|max:100',
            'api_key' => 'nullable|string|max:255',
            'sync_frequency' => 'nullable|string',
            'payment_type' => 'nullable|in:cari,credit_card',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
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
            'group_id' => $request->group_id,
            'name' => $request->name,
            'country' => $request->country,
            'city' => $request->city,
            'accounting_code' => $request->accounting_code,
            'types' => $request->types,
            'description' => $request->description,
            'api_endpoint' => $request->api_endpoint,
            'api_version' => $request->api_version,
            'sync_frequency' => $request->sync_frequency,
            'payment_type' => $request->payment_type ?? 'cari',
            'address' => $request->address,
            'tax_rate' => $request->tax_rate ?? 0,
            'is_active' => $request->has('is_active'),
            'sync_enabled' => $request->has('sync_enabled'),
        ];

        // Logo yükleme
        if ($request->hasFile('logo')) {
            // Eski logoyu sil
            if ($supplier->logo) {
                \Storage::disk('public')->delete($supplier->logo);
            }
            $logoPath = $request->file('logo')->store('suppliers/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Ödeme periyodlarını işle
        $paymentPeriods = [];
        if ($request->payment_period_type === 'days') {
            $paymentPeriods = [
                'type' => 'days',
                'before_booking' => $request->payment_days_before ?? 0,
                'after_booking' => $request->payment_days_after ?? 0
            ];
        } elseif ($request->payment_period_type === 'monthly') {
            $paymentPeriods = [
                'type' => 'monthly',
                'days' => $request->payment_monthly_days ?? []
            ];
        }
        $data['payment_periods'] = $paymentPeriods;

        // İletişim kişilerini işle
        $contactPersons = [];
        if ($request->contact_names) {
            foreach ($request->contact_names as $index => $name) {
                if ($name && isset($request->contact_phones[$index])) {
                    $contactPersons[] = [
                        'name' => $name,
                        'phone' => $request->contact_phones[$index],
                        'email' => $request->contact_emails[$index] ?? null
                    ];
                }
            }
        }
        $data['contact_persons'] = $contactPersons;

        // E-postaları işle
        $emails = [];
        if ($request->supplier_emails) {
            foreach ($request->supplier_emails as $index => $email) {
                if ($email && isset($request->supplier_email_names[$index])) {
                    $emails[] = [
                        'name' => $request->supplier_email_names[$index],
                        'email' => $email,
                        'is_primary' => $request->supplier_email_primary[$index] ?? false
                    ];
                }
            }
        }
        $data['emails'] = $emails;

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
        // Sadece admin kullanıcılar tedarikçi silebilir
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Tedarikçileri sadece admin kullanıcılar silebilir.');
        }
        
        // Tedarikçinin logosunu sil
        if ($supplier->logo) {
            \Storage::disk('public')->delete($supplier->logo);
        }
        
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Tedarikçi başarıyla silindi.');
    }

    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update(['is_active' => !$supplier->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'Tedarikçi durumu başarıyla güncellendi.',
            'is_active' => $supplier->is_active
        ]);
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