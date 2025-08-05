<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Reservation\Models\Reservation;
use App\DDD\Modules\Contract\Models\ContractRoom;
use App\DDD\Modules\Firm\Models\FirmUser;
use App\Models\User;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $contractRooms = ContractRoom::all();
        $firmUsers = FirmUser::all();

        // Create reservations for the next 3 months
        for ($i = 0; $i < 50; $i++) {
            $contractRoom = $contractRooms->random();
            $firmUser = $firmUsers->random();
            
            // Generate random dates
            $checkinDate = Carbon::now()->addDays(rand(1, 90));
            $checkoutDate = $checkinDate->copy()->addDays(rand(1, 14));
            
            // Calculate total price based on duration and room price
            $duration = $checkinDate->diffInDays($checkoutDate);
            $totalPrice = $contractRoom->sale_price * $duration;
            
            // Determine status based on price and user role
            $status = 'pending';
            if ($totalPrice > 5000 && $firmUser->role === 'staff') {
                $status = 'awaiting_approval';
            } elseif ($totalPrice > 10000) {
                $status = 'awaiting_approval';
            } elseif (rand(1, 10) <= 7) {
                $status = 'approved';
            }

            Reservation::firstOrCreate(
                [
                    'user_id' => $firmUser->user_id,
                    'contract_room_id' => $contractRoom->id,
                    'checkin_date' => $checkinDate->format('Y-m-d'),
                    'checkout_date' => $checkoutDate->format('Y-m-d'),
                ],
                [
                    'guest_count' => rand(1, 4),
                    'total_price' => $totalPrice,
                    'status' => $status,
                ]
            );
        }

        // Create some past reservations
        for ($i = 0; $i < 20; $i++) {
            $contractRoom = $contractRooms->random();
            $firmUser = $firmUsers->random();
            
            $checkinDate = Carbon::now()->subDays(rand(1, 90));
            $checkoutDate = $checkinDate->copy()->addDays(rand(1, 7));
            
            $duration = $checkinDate->diffInDays($checkoutDate);
            $totalPrice = $contractRoom->sale_price * $duration;
            
            Reservation::firstOrCreate(
                [
                    'user_id' => $firmUser->user_id,
                    'contract_room_id' => $contractRoom->id,
                    'checkin_date' => $checkinDate->format('Y-m-d'),
                    'checkout_date' => $checkoutDate->format('Y-m-d'),
                ],
                [
                    'guest_count' => rand(1, 4),
                    'total_price' => $totalPrice,
                    'status' => 'approved',
                ]
            );
        }
    }
} 