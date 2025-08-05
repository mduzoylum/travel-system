<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Firm\Models\FirmUser;
use App\Models\User;
use App\DDD\Modules\Firm\Models\Firm;

class FirmUserSeeder extends Seeder
{
    public function run(): void
    {
        $bizigo = Firm::where('email_domain', 'bizigo.com')->first();
        $travelCorp = Firm::where('email_domain', 'travelcorp.com')->first();
        $tourismPlus = Firm::where('email_domain', 'tourismplus.com')->first();

        // Bizigo firm users
        FirmUser::firstOrCreate(
            [
                'firm_id' => $bizigo->id,
                'user_id' => User::where('email', 'ahmet@bizigo.com')->first()->id,
            ],
            [
                'role' => 'ceo',
                'department' => 'Yönetim',
            ]
        );

        FirmUser::firstOrCreate(
            [
                'firm_id' => $bizigo->id,
                'user_id' => User::where('email', 'ayse@bizigo.com')->first()->id,
            ],
            [
                'role' => 'manager',
                'department' => 'Pazarlama',
            ]
        );

        FirmUser::firstOrCreate(
            [
                'firm_id' => $bizigo->id,
                'user_id' => User::where('email', 'mehmet@bizigo.com')->first()->id,
            ],
            [
                'role' => 'staff',
                'department' => 'Satış',
            ]
        );

        // TravelCorp firm users
        FirmUser::firstOrCreate(
            [
                'firm_id' => $travelCorp->id,
                'user_id' => User::where('email', 'fatma@bizigo.com')->first()->id,
            ],
            [
                'role' => 'manager',
                'department' => 'Operasyon',
            ]
        );

        FirmUser::firstOrCreate(
            [
                'firm_id' => $travelCorp->id,
                'user_id' => User::where('email', 'ali@bizigo.com')->first()->id,
            ],
            [
                'role' => 'staff',
                'department' => 'Müşteri Hizmetleri',
            ]
        );

        // TourismPlus firm users
        FirmUser::firstOrCreate(
            [
                'firm_id' => $tourismPlus->id,
                'user_id' => User::where('email', 'zeynep@bizigo.com')->first()->id,
            ],
            [
                'role' => 'ceo',
                'department' => 'Yönetim',
            ]
        );

        FirmUser::firstOrCreate(
            [
                'firm_id' => $tourismPlus->id,
                'user_id' => User::where('email', 'mustafa@bizigo.com')->first()->id,
            ],
            [
                'role' => 'manager',
                'department' => 'Finans',
            ]
        );
    }
} 