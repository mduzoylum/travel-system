<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Contract\Models\RoomAvailability;
use App\DDD\Modules\Contract\Models\ContractRoom;
use Carbon\Carbon;

class RoomAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $contractRooms = ContractRoom::all();
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addMonths(6)->endOfDay();

        foreach ($contractRooms as $contractRoom) {
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // Generate random stock based on room type and hotel stars
                $baseStock = 10; // Base stock for standard rooms
                
                if (str_contains($contractRoom->room_type, 'Deluxe')) {
                    $baseStock = 5;
                } elseif (str_contains($contractRoom->room_type, 'Suit')) {
                    $baseStock = 3;
                } elseif (str_contains($contractRoom->room_type, 'Başkanlık')) {
                    $baseStock = 1;
                }

                // Reduce stock for weekends and holidays
                $stock = $baseStock;
                if ($currentDate->isWeekend()) {
                    $stock = max(1, $stock - 2);
                }

                // Reduce stock for summer months (June, July, August)
                if ($currentDate->month >= 6 && $currentDate->month <= 8) {
                    $stock = max(1, $stock - 3);
                }

                // Reduce stock for winter months (December, January, February) for summer destinations
                $hotelCity = $contractRoom->contract->hotel->city;
                if (($currentDate->month == 12 || $currentDate->month <= 2) && 
                    in_array($hotelCity, ['Antalya', 'Muğla'])) {
                    $stock = max(1, $stock - 2);
                }

                // Add some randomness
                $stock = max(0, $stock + rand(-1, 1));

                if ($stock > 0) {
                    RoomAvailability::firstOrCreate(
                        [
                            'contract_room_id' => $contractRoom->id,
                            'date' => $currentDate->format('Y-m-d'),
                        ],
                        [
                            'stock' => $stock,
                        ]
                    );
                }

                $currentDate->addDay();
            }
        }
    }
} 