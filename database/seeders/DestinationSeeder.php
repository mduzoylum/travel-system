<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Contract\Models\Destination;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Türkiye
        $turkiye = Destination::create([
            'name' => 'Türkiye',
            'type' => 'country',
            'code' => 'TR',
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => 1
        ]);

        // İstanbul
        $istanbul = Destination::create([
            'name' => 'İstanbul',
            'type' => 'city',
            'code' => 'IST',
            'parent_id' => $turkiye->id,
            'is_active' => true,
            'sort_order' => 1
        ]);

        // İstanbul alt destinasyonları
        $taksim = Destination::create([
            'name' => 'Taksim',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $istanbul->id,
            'is_active' => true,
            'sort_order' => 1
        ]);

        $sisli = Destination::create([
            'name' => 'Şişli',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $istanbul->id,
            'is_active' => true,
            'sort_order' => 2
        ]);

        $sultanahmet = Destination::create([
            'name' => 'Sultanahmet',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $istanbul->id,
            'is_active' => true,
            'sort_order' => 3
        ]);

        $beyoglu = Destination::create([
            'name' => 'Beyoğlu',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $istanbul->id,
            'is_active' => true,
            'sort_order' => 4
        ]);

        // Antalya
        $antalya = Destination::create([
            'name' => 'Antalya',
            'type' => 'city',
            'code' => 'AYT',
            'parent_id' => $turkiye->id,
            'is_active' => true,
            'sort_order' => 2
        ]);

        // Antalya alt destinasyonları
        $kas = Destination::create([
            'name' => 'Kaş',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $antalya->id,
            'is_active' => true,
            'sort_order' => 1
        ]);

        $karaali = Destination::create([
            'name' => 'Karaali',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $antalya->id,
            'is_active' => true,
            'sort_order' => 2
        ]);

        $kemer = Destination::create([
            'name' => 'Kemer',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $antalya->id,
            'is_active' => true,
            'sort_order' => 3
        ]);

        $lara = Destination::create([
            'name' => 'Lara',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $antalya->id,
            'is_active' => true,
            'sort_order' => 4
        ]);

        // Bodrum
        $bodrum = Destination::create([
            'name' => 'Bodrum',
            'type' => 'city',
            'code' => 'BJV',
            'parent_id' => $turkiye->id,
            'is_active' => true,
            'sort_order' => 3
        ]);

        // Bodrum alt destinasyonları
        $torba = Destination::create([
            'name' => 'Torba',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $bodrum->id,
            'is_active' => true,
            'sort_order' => 1
        ]);

        $turkbuku = Destination::create([
            'name' => 'Türkbükü',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $bodrum->id,
            'is_active' => true,
            'sort_order' => 2
        ]);

        $yatikoy = Destination::create([
            'name' => 'Yalıkavak',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $bodrum->id,
            'is_active' => true,
            'sort_order' => 3
        ]);

        $gundogan = Destination::create([
            'name' => 'Gündoğan',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $bodrum->id,
            'is_active' => true,
            'sort_order' => 4
        ]);

        $gumusluk = Destination::create([
            'name' => 'Gümüşlük',
            'type' => 'sub_destination',
            'code' => null,
            'parent_id' => $bodrum->id,
            'is_active' => true,
            'sort_order' => 5
        ]);
    }
}
