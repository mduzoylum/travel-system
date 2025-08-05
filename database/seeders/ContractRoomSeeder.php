<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Contract\Models\ContractRoom;
use App\DDD\Modules\Contract\Models\Contract;

class ContractRoomSeeder extends Seeder
{
    public function run(): void
    {
        // Get contracts
        $contracts = Contract::all();

        foreach ($contracts as $contract) {
            // Standard Room - Bed & Breakfast
            ContractRoom::firstOrCreate(
                [
                    'contract_id' => $contract->id,
                    'room_type' => 'Standart Oda',
                    'meal_plan' => 'BB',
                ],
                [
                    'base_price' => $contract->hotel->min_price * 0.8,
                    'sale_price' => $contract->hotel->min_price,
                ]
            );

            // Standard Room - Half Board
            ContractRoom::firstOrCreate(
                [
                    'contract_id' => $contract->id,
                    'room_type' => 'Standart Oda',
                    'meal_plan' => 'HB',
                ],
                [
                    'base_price' => $contract->hotel->min_price * 0.9,
                    'sale_price' => $contract->hotel->min_price * 1.2,
                ]
            );

            // Standard Room - Full Board
            ContractRoom::firstOrCreate(
                [
                    'contract_id' => $contract->id,
                    'room_type' => 'Standart Oda',
                    'meal_plan' => 'FB',
                ],
                [
                    'base_price' => $contract->hotel->min_price * 1.1,
                    'sale_price' => $contract->hotel->min_price * 1.4,
                ]
            );

            // Deluxe Room - Bed & Breakfast
            ContractRoom::firstOrCreate(
                [
                    'contract_id' => $contract->id,
                    'room_type' => 'Deluxe Oda',
                    'meal_plan' => 'BB',
                ],
                [
                    'base_price' => $contract->hotel->min_price * 1.2,
                    'sale_price' => $contract->hotel->min_price * 1.5,
                ]
            );

            // Deluxe Room - Half Board
            ContractRoom::firstOrCreate(
                [
                    'contract_id' => $contract->id,
                    'room_type' => 'Deluxe Oda',
                    'meal_plan' => 'HB',
                ],
                [
                    'base_price' => $contract->hotel->min_price * 1.3,
                    'sale_price' => $contract->hotel->min_price * 1.7,
                ]
            );

            // Suite Room - Bed & Breakfast (only for 4-5 star hotels)
            if ($contract->hotel->stars >= 4) {
                ContractRoom::firstOrCreate(
                    [
                        'contract_id' => $contract->id,
                        'room_type' => 'Suit Oda',
                        'meal_plan' => 'BB',
                    ],
                    [
                        'base_price' => $contract->hotel->min_price * 1.8,
                        'sale_price' => $contract->hotel->min_price * 2.2,
                    ]
                );

                ContractRoom::firstOrCreate(
                    [
                        'contract_id' => $contract->id,
                        'room_type' => 'Suit Oda',
                        'meal_plan' => 'HB',
                    ],
                    [
                        'base_price' => $contract->hotel->min_price * 1.9,
                        'sale_price' => $contract->hotel->min_price * 2.4,
                    ]
                );
            }

            // Presidential Suite (only for 5 star hotels)
            if ($contract->hotel->stars == 5) {
                ContractRoom::firstOrCreate(
                    [
                        'contract_id' => $contract->id,
                        'room_type' => 'Başkanlık Suiti',
                        'meal_plan' => 'BB',
                    ],
                    [
                        'base_price' => $contract->hotel->min_price * 3.0,
                        'sale_price' => $contract->hotel->min_price * 3.5,
                    ]
                );

                ContractRoom::firstOrCreate(
                    [
                        'contract_id' => $contract->id,
                        'room_type' => 'Başkanlık Suiti',
                        'meal_plan' => 'HB',
                    ],
                    [
                        'base_price' => $contract->hotel->min_price * 3.1,
                        'sale_price' => $contract->hotel->min_price * 3.7,
                    ]
                );
            }
        }
    }
} 