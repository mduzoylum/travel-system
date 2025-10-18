<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DDD\Modules\Contract\Models\Hotel;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::with(['supplier', 'contracts'])
            ->leftJoin('suppliers', 'hotels.supplier_id', '=', 'suppliers.id')
            ->select('hotels.*', 'suppliers.is_active as supplier_is_active')
            ->paginate(15);
        return view('admin.hotels.index', compact('hotels'));
    }

    public function create()
    {
        // Sadece API entegrasyonu olmayan tedarikçileri göster (kontrat girişi için)
        $suppliers = Supplier::where('is_active', true)
            ->whereNull('api_endpoint')
            ->get();
        return view('admin.hotels.create', compact('suppliers'));
    }

    public function show(Hotel $hotel)
    {
        $hotel->load(['supplier', 'contracts.firm', 'contracts.rooms']);
        return view('admin.hotels.show', compact('hotel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'stars' => 'required|integer|min:1|max:5',
            'min_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'external_id' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ], [
            'name.required' => 'Otel adı zorunludur.',
            'city.required' => 'Şehir zorunludur.',
            'country.required' => 'Ülke zorunludur.',
            'address.required' => 'Adres zorunludur.',
            'stars.required' => 'Yıldız sayısı seçilmelidir.',
            'stars.integer' => 'Yıldız sayısı geçerli bir sayı olmalıdır.',
            'stars.min' => 'Yıldız sayısı en az 1 olmalıdır.',
            'stars.max' => 'Yıldız sayısı en fazla 5 olabilir.',
            'min_price.required' => 'Minimum fiyat zorunludur.',
            'min_price.numeric' => 'Minimum fiyat geçerli bir sayı olmalıdır.',
            'min_price.min' => 'Minimum fiyat 0\'dan küçük olamaz.',
            'image.image' => 'Yüklenen dosya bir resim olmalıdır.',
            'image.mimes' => 'Resim formatı jpeg, png, jpg veya gif olmalıdır.',
            'image.max' => 'Resim boyutu en fazla 5MB olabilir.',
            'supplier_id.exists' => 'Seçilen tedarikçi bulunamadı.',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');

        // Tedarikçi seçildiyse, tedarikçinin muhasebe kodunu otomatik ata
        if ($request->supplier_id) {
            $supplier = Supplier::find($request->supplier_id);
            if ($supplier && $supplier->accounting_code) {
                $data['accounting_code'] = $supplier->accounting_code;
            }
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('hotels', 'public');
            $data['image'] = $imagePath;
        }

        Hotel::create($data);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Otel başarıyla eklendi.');
    }

    public function edit(Hotel $hotel)
    {
        // Sadece API entegrasyonu olmayan tedarikçileri göster (kontrat girişi için)
        $suppliers = Supplier::where('is_active', true)
            ->whereNull('api_endpoint')
            ->get();
        return view('admin.hotels.edit', compact('hotel', 'suppliers'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'stars' => 'required|integer|min:1|max:5',
            'min_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'external_id' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ], [
            'name.required' => 'Otel adı zorunludur.',
            'city.required' => 'Şehir zorunludur.',
            'country.required' => 'Ülke zorunludur.',
            'address.required' => 'Adres zorunludur.',
            'stars.required' => 'Yıldız sayısı seçilmelidir.',
            'stars.integer' => 'Yıldız sayısı geçerli bir sayı olmalıdır.',
            'stars.min' => 'Yıldız sayısı en az 1 olmalıdır.',
            'stars.max' => 'Yıldız sayısı en fazla 5 olabilir.',
            'min_price.required' => 'Minimum fiyat zorunludur.',
            'min_price.numeric' => 'Minimum fiyat geçerli bir sayı olmalıdır.',
            'min_price.min' => 'Minimum fiyat 0\'dan küçük olamaz.',
            'image.image' => 'Yüklenen dosya bir resim olmalıdır.',
            'image.mimes' => 'Resim formatı jpeg, png, jpg veya gif olmalıdır.',
            'image.max' => 'Resim boyutu en fazla 5MB olabilir.',
            'supplier_id.exists' => 'Seçilen tedarikçi bulunamadı.',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');

        // Tedarikçi değiştiyse, yeni tedarikçinin muhasebe kodunu ata
        if ($request->supplier_id && $request->supplier_id != $hotel->supplier_id) {
            $supplier = Supplier::find($request->supplier_id);
            if ($supplier && $supplier->accounting_code) {
                $data['accounting_code'] = $supplier->accounting_code;
            }
        }

        if ($request->hasFile('image')) {
            // Eski resmi sil
            if ($hotel->image) {
                Storage::disk('public')->delete($hotel->image);
            }
            $imagePath = $request->file('image')->store('hotels', 'public');
            $data['image'] = $imagePath;
        }

        $hotel->update($data);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Otel başarıyla güncellendi.');
    }

    public function destroy(Hotel $hotel)
    {
        if ($hotel->image) {
            Storage::disk('public')->delete($hotel->image);
        }
        
        $hotel->delete();

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Otel başarıyla silindi.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'supplier_id' => 'nullable|exists:suppliers,id'
        ]);

        // TODO: Excel import implementasyonu gelecekte eklenecek
        return redirect()->route('admin.hotels.index')
            ->with('info', 'Excel import özelliği yakında eklenecek.');
    }

    public function export(Request $request)
    {
        // TODO: Excel export implementasyonu gelecekte eklenecek
        return redirect()->route('admin.hotels.index')
            ->with('info', 'Excel export özelliği yakında eklenecek.');
    }

    public function downloadTemplate()
    {
        // TODO: Template download implementasyonu gelecekte eklenecek
        return redirect()->route('admin.hotels.index')
            ->with('info', 'Template download özelliği yakında eklenecek.');
    }

    public function getHotelsByDestination(Request $request)
    {
        $destination = $request->get('destination');
        
        if (!$destination) {
            return response()->json([]);
        }

        // Destinasyonu parse et (city, country formatında)
        $parts = explode(', ', $destination);
        $city = $parts[0] ?? '';
        $country = $parts[1] ?? '';

        $hotels = Hotel::where('city', $city)
            ->where('country', $country)
            ->where('is_active', true)
            ->whereHas('supplier', function($query) {
                $query->where('is_active', true);
            })
            ->select('id', 'name', 'city', 'country')
            ->orderBy('name')
            ->get();

        return response()->json($hotels);
    }
} 