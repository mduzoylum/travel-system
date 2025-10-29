<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Contract\Models\Contract;
use App\DDD\Modules\Contract\Models\ContractRoom;
use App\DDD\Modules\Contract\Models\ContractRoomPeriod;
use Carbon\Carbon;

class ContractRoomPeriodSeeder extends Seeder
{
    public function run(): void
    {
        // İlk kontrat ve oda (örnek)
        $contract = Contract::with('rooms')->first();
        
        if (!$contract || $contract->rooms->isEmpty()) {
            $this->command->warn('Kontrat veya oda bulunamadı. Önce ContractSeeder çalıştırın.');
            return;
        }

        $room = $contract->rooms->first();

        // Örnek: Farklı para birimlerinde farklı periyotlar
        $periods = [
            // Nisan ayının başında TRY
            [
                'contract_room_id' => $room->id,
                'start_date' => '2025-04-01',
                'end_date' => '2025-04-10',
                'currency' => 'TRY',
                'base_price' => 10000.00,
                'sale_price' => 12000.00,
                'notes' => 'Nisan başı dönem - TRY',
                'is_active' => true,
            ],
            // Nisan ortasında EUR
            [
                'contract_room_id' => $room->id,
                'start_date' => '2025-04-11',
                'end_date' => '2025-04-20',
                'currency' => 'EUR',
                'base_price' => 1000.00,
                'sale_price' => 1200.00,
                'notes' => 'Nisan ortası dönem - EUR',
                'is_active' => true,
            ],
            // Nisan sonunda tekrar TRY
            [
                'contract_room_id' => $room->id,
                'start_date' => '2025-04-21',
                'end_date' => '2025-04-30',
                'currency' => 'TRY',
                'base_price' => 10000.00,
                'sale_price' => 12000.00,
                'notes' => 'Nisan sonu dönem - TRY',
                'is_active' => true,
            ],
        ];

        foreach ($periods as $periodData) {
            ContractRoomPeriod::updateOrCreate(
                [
                    'contract_room_id' => $periodData['contract_room_id'],
                    'start_date' => $periodData['start_date'],
                    'end_date' => $periodData['end_date'],
                ],
                $periodData
            );
        }

        $this->command->info('Contract Room Periods seeded successfully!');
    }
}
