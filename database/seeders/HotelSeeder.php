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
                'image' => 'https://example.com/images/grand-istanbul.jpg',
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
                'image' => 'https://example.com/images/bosphorus-palace.jpg',
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
                'image' => 'https://example.com/images/sultanahmet-boutique.jpg',
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
                'image' => 'https://example.com/images/mediterranean-resort.jpg',
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
                'image' => 'https://example.com/images/kaleici-hotel.jpg',
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
                'image' => 'https://example.com/images/cappadocia-cave.jpg',
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
                'image' => 'https://example.com/images/aegean-coast.jpg',
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
                'image' => 'https://example.com/images/bodrum-marina.jpg',
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
                'image' => null,
                'is_active' => false,
            ]
        );
    }
}
