<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\UserAccessRule\Models\UserAccessRule;
use App\DDD\Modules\Firm\Models\Firm;

class UserAccessRuleSeeder extends Seeder
{
    public function run(): void
    {
        $firms = Firm::all();

        foreach ($firms as $firm) {
            // CEO rules - full access
            UserAccessRule::firstOrCreate(
                [
                    'firm_id' => $firm->id,
                    'role' => 'ceo',
                ],
                [
                    'rules' => json_encode([
                        'hotel_stars' => [1, 2, 3, 4, 5],
                        'countries' => ['Türkiye', 'Yunanistan', 'İtalya', 'İspanya', 'Fransa'],
                        'max_price' => 10000,
                        'can_approve_reservations' => true,
                        'can_manage_users' => true,
                        'can_view_reports' => true,
                        'can_manage_contracts' => true,
                    ]),
                ]
            );

            // Manager rules - moderate access
            UserAccessRule::firstOrCreate(
                [
                    'firm_id' => $firm->id,
                    'role' => 'manager',
                ],
                [
                    'rules' => json_encode([
                        'hotel_stars' => [1, 2, 3, 4],
                        'countries' => ['Türkiye', 'Yunanistan'],
                        'max_price' => 5000,
                        'can_approve_reservations' => true,
                        'can_manage_users' => false,
                        'can_view_reports' => true,
                        'can_manage_contracts' => false,
                    ]),
                ]
            );

            // Staff rules - limited access
            UserAccessRule::firstOrCreate(
                [
                    'firm_id' => $firm->id,
                    'role' => 'staff',
                ],
                [
                    'rules' => json_encode([
                        'hotel_stars' => [1, 2, 3],
                        'countries' => ['Türkiye'],
                        'max_price' => 2000,
                        'can_approve_reservations' => false,
                        'can_manage_users' => false,
                        'can_view_reports' => false,
                        'can_manage_contracts' => false,
                    ]),
                ]
            );
        }
    }
} 