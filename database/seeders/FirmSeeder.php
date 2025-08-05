<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Firm\Models\Firm;

class FirmSeeder extends Seeder
{
    public function run(): void
    {
        Firm::firstOrCreate(
            ['email_domain' => 'bizigo.com'],
            [
                'name' => 'Bizigo',
                'contact_person' => 'Ahmet Yılmaz',
                'email' => 'info@bizigo.com',
                'phone' => '+90 212 555 0123',
                'address' => 'İstanbul, Türkiye',
                'tax_number' => '1234567890',
                'is_active' => true,
            ]
        );

        Firm::firstOrCreate(
            ['email_domain' => 'travelcorp.com'],
            [
                'name' => 'TravelCorp',
                'contact_person' => 'Ayşe Demir',
                'email' => 'info@travelcorp.com',
                'phone' => '+90 216 555 0456',
                'address' => 'İstanbul, Türkiye',
                'tax_number' => '0987654321',
                'is_active' => true,
            ]
        );

        Firm::firstOrCreate(
            ['email_domain' => 'tourismplus.com'],
            [
                'name' => 'TourismPlus',
                'contact_person' => 'Mehmet Kaya',
                'email' => 'info@tourismplus.com',
                'phone' => '+90 232 555 0789',
                'address' => 'İzmir, Türkiye',
                'tax_number' => '1122334455',
                'is_active' => true,
            ]
        );
    }
}


