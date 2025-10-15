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
        $hotels = Hotel::with(['supplier', 'contracts'])->paginate(15);
        return view('admin.hotels.index', compact('hotels'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
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
        $suppliers = Supplier::where('is_active', true)->get();
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
} 