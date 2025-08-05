<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Contract\Models\Contract;
use App\DDD\Modules\Contract\Models\Hotel;
use App\DDD\Modules\Firm\Models\Firm;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $bizigo = Firm::where('email_domain', 'bizigo.com')->first();
        $travelCorp = Firm::where('email_domain', 'travelcorp.com')->first();
        $tourismPlus = Firm::where('email_domain', 'tourismplus.com')->first();

        // Get hotels
        $grandIstanbul = Hotel::where('name', 'Grand Istanbul Hotel')->first();
        $bosphorusPalace = Hotel::where('name', 'Bosphorus Palace')->first();
        $sultanahmetBoutique = Hotel::where('name', 'Sultanahmet Boutique')->first();
        $mediterraneanResort = Hotel::where('name', 'Mediterranean Resort')->first();
        $kaleiciHotel = Hotel::where('name', 'Kaleiçi Hotel')->first();
        $cappadociaCave = Hotel::where('name', 'Cappadocia Cave Hotel')->first();
        $aegeanCoast = Hotel::where('name', 'Aegean Coast Hotel')->first();
        $bodrumMarina = Hotel::where('name', 'Bodrum Marina Hotel')->first();

        // Bizigo contracts
        Contract::firstOrCreate(
            [
                'hotel_id' => $grandIstanbul->id,
                'firm_id' => $bizigo->id,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'currency' => 'TRY',
                'is_active' => true,
            ]
        );

        Contract::firstOrCreate(
            [
                'hotel_id' => $bosphorusPalace->id,
                'firm_id' => $bizigo->id,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'currency' => 'TRY',
                'is_active' => true,
            ]
        );

        Contract::firstOrCreate(
            [
                'hotel_id' => $mediterraneanResort->id,
                'firm_id' => $bizigo->id,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'currency' => 'TRY',
                'is_active' => true,
            ]
        );

        // TravelCorp contracts
        Contract::firstOrCreate(
            [
                'hotel_id' => $sultanahmetBoutique->id,
                'firm_id' => $travelCorp->id,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'currency' => 'TRY',
                'is_active' => true,
            ]
        );

        Contract::firstOrCreate(
            [
                'hotel_id' => $kaleiciHotel->id,
                'firm_id' => $travelCorp->id,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'currency' => 'TRY',
                'is_active' => true,
            ]
        );

        Contract::firstOrCreate(
            [
                'hotel_id' => $cappadociaCave->id,
                'firm_id' => $travelCorp->id,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'currency' => 'TRY',
                'is_active' => true,
            ]
        );

        // TourismPlus contracts
        Contract::firstOrCreate(
            [
                'hotel_id' => $aegeanCoast->id,
                'firm_id' => $tourismPlus->id,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'currency' => 'TRY',
                'is_active' => true,
            ]
        );

        Contract::firstOrCreate(
            [
                'hotel_id' => $bodrumMarina->id,
                'firm_id' => $tourismPlus->id,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'currency' => 'TRY',
                'is_active' => true,
            ]
        );

        // Shared contracts (multiple firms can have contracts with same hotel)
        Contract::firstOrCreate(
            [
                'hotel_id' => $grandIstanbul->id,
                'firm_id' => $travelCorp->id,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'currency' => 'TRY',
                'is_active' => true,
            ]
        );

        Contract::firstOrCreate(
            [
                'hotel_id' => $mediterraneanResort->id,
                'firm_id' => $tourismPlus->id,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'currency' => 'TRY',
                'is_active' => true,
            ]
        );
    }
} 