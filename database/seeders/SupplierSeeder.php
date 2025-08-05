<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::firstOrCreate(
            ['name' => 'OtelBest'],
            [
                'type' => 'otelbest',
                'api_endpoint' => 'https://api.otelbest.com/v1',
                'api_credentials' => json_encode([
                    'api_key' => 'sample_api_key_123',
                    'username' => 'bizigo_user',
                    'password' => 'encrypted_password'
                ]),
                'is_active' => true,
                'sync_enabled' => true,
                'last_sync_at' => now()->subHours(2),
            ]
        );

        Supplier::firstOrCreate(
            ['name' => 'BookingPro'],
            [
                'type' => 'bookingpro',
                'api_endpoint' => 'https://api.bookingpro.com/v2',
                'api_credentials' => json_encode([
                    'api_key' => 'booking_pro_key_456',
                    'partner_id' => 'BIZIGO001'
                ]),
                'is_active' => true,
                'sync_enabled' => true,
                'last_sync_at' => now()->subHours(1),
            ]
        );

        Supplier::firstOrCreate(
            ['name' => 'HotelDirect'],
            [
                'type' => 'hoteldirect',
                'api_endpoint' => 'https://api.hoteldirect.com/v1',
                'api_credentials' => json_encode([
                    'api_key' => 'hotel_direct_key_789',
                    'agency_code' => 'BIZIGO'
                ]),
                'is_active' => true,
                'sync_enabled' => false,
                'last_sync_at' => null,
            ]
        );

        Supplier::firstOrCreate(
            ['name' => 'TravelNet'],
            [
                'type' => 'travelnet',
                'api_endpoint' => 'https://api.travelnet.com/v3',
                'api_credentials' => json_encode([
                    'api_key' => 'travel_net_key_012',
                    'user_id' => 'bizigo_travel'
                ]),
                'is_active' => false,
                'sync_enabled' => false,
                'last_sync_at' => null,
            ]
        );
    }
} 