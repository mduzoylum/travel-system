<?php

namespace Database\Seeders;

use App\DDD\Modules\Contract\Models\Hotel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hotel::create([
            'name' => 'Hilton Taksim',
            'city' => 'İstanbul',
            'country' => 'Turkey',
            'stars' => 5,
            'min_price' => 4000,
            'is_contracted' => true,
        ]);

    }
}
