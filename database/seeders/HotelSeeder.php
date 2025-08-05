<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Contract\Models\Hotel;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $otelBest = Supplier::where('type', 'otelbest')->first();
        $bookingPro = Supplier::where('type', 'bookingpro')->first();
        $hotelDirect = Supplier::where('type', 'hoteldirect')->first();

        // Istanbul Hotels
        Hotel::firstOrCreate(
            ['name' => 'Grand Istanbul Hotel'],
            [
                'city' => 'İstanbul',
                'country' => 'Türkiye',
                'stars' => 5,
                'min_price' => 1200.00,
                'is_contracted' => true,
                'supplier_id' => $otelBest->id,
                'external_id' => 'IST001',
                'image' => 'https://via.placeholder.com/400x300/4A90E2/FFFFFF?text=Grand+Istanbul+Hotel',
                'is_active' => true,
            ]
        );

        Hotel::firstOrCreate(
            ['name' => 'Bosphorus Palace'],
            [
                'city' => 'İstanbul',
                'country' => 'Türkiye',
                'stars' => 4,
                'min_price' => 800.00,
                'is_contracted' => true,
                'supplier_id' => $bookingPro->id,
                'external_id' => 'IST002',
                'image' => 'https://via.placeholder.com/400x300/50C878/FFFFFF?text=Bosphorus+Palace',
                'is_active' => true,
            ]
        );

        Hotel::firstOrCreate(
            ['name' => 'Sultanahmet Boutique'],
            [
                'city' => 'İstanbul',
                'country' => 'Türkiye',
                'stars' => 3,
                'min_price' => 450.00,
                'is_contracted' => true,
                'supplier_id' => $otelBest->id,
                'external_id' => 'IST003',
                'image' => 'https://via.placeholder.com/400x300/FF6B35/FFFFFF?text=Sultanahmet+Boutique',
                'is_active' => true,
            ]
        );

        // Antalya Hotels
        Hotel::firstOrCreate(
            ['name' => 'Mediterranean Resort'],
            [
                'city' => 'Antalya',
                'country' => 'Türkiye',
                'stars' => 5,
                'min_price' => 1500.00,
                'is_contracted' => true,
                'supplier_id' => $hotelDirect->id,
                'external_id' => 'ANT001',
                'image' => 'https://via.placeholder.com/400x300/87CEEB/FFFFFF?text=Mediterranean+Resort',
                'is_active' => true,
            ]
        );

        Hotel::firstOrCreate(
            ['name' => 'Kaleiçi Hotel'],
            [
                'city' => 'Antalya',
                'country' => 'Türkiye',
                'stars' => 4,
                'min_price' => 650.00,
                'is_contracted' => true,
                'supplier_id' => $bookingPro->id,
                'external_id' => 'ANT002',
                'image' => 'https://via.placeholder.com/400x300/FFD700/FFFFFF?text=Kaleici+Hotel',
                'is_active' => true,
            ]
        );

        // Cappadocia Hotels
        Hotel::firstOrCreate(
            ['name' => 'Cappadocia Cave Hotel'],
            [
                'city' => 'Nevşehir',
                'country' => 'Türkiye',
                'stars' => 4,
                'min_price' => 900.00,
                'is_contracted' => true,
                'supplier_id' => $otelBest->id,
                'external_id' => 'NEV001',
                'image' => 'https://via.placeholder.com/400x300/8B4513/FFFFFF?text=Cappadocia+Cave+Hotel',
                'is_active' => true,
            ]
        );

        // Izmir Hotels
        Hotel::firstOrCreate(
            ['name' => 'Aegean Coast Hotel'],
            [
                'city' => 'İzmir',
                'country' => 'Türkiye',
                'stars' => 4,
                'min_price' => 700.00,
                'is_contracted' => true,
                'supplier_id' => $hotelDirect->id,
                'external_id' => 'IZM001',
                'image' => 'https://via.placeholder.com/400x300/4682B4/FFFFFF?text=Aegean+Coast+Hotel',
                'is_active' => true,
            ]
        );

        // Bodrum Hotels
        Hotel::firstOrCreate(
            ['name' => 'Bodrum Marina Hotel'],
            [
                'city' => 'Muğla',
                'country' => 'Türkiye',
                'stars' => 5,
                'min_price' => 1800.00,
                'is_contracted' => true,
                'supplier_id' => $bookingPro->id,
                'external_id' => 'MUG001',
                'image' => 'https://via.placeholder.com/400x300/20B2AA/FFFFFF?text=Bodrum+Marina+Hotel',
                'is_active' => true,
            ]
        );

        // Non-contracted hotels
        Hotel::firstOrCreate(
            ['name' => 'Test Hotel'],
            [
                'city' => 'Ankara',
                'country' => 'Türkiye',
                'stars' => 3,
                'min_price' => 400.00,
                'is_contracted' => false,
                'supplier_id' => null,
                'external_id' => null,
                'image' => 'https://via.placeholder.com/400x300/808080/FFFFFF?text=Test+Hotel',
                'is_active' => false,
            ]
        );
    }
}
