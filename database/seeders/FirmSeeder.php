<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Firm\Models\Firm;

class FirmSeeder extends Seeder
{
    public function run(): void
    {
        Firm::create([
            'name' => 'Bizigo',
            'email_domain' => 'bizigo.com',
            'is_active' => true,
        ]);
    }
}


