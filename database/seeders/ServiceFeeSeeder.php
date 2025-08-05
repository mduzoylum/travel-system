<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Profit\Models\ServiceFee;
use App\DDD\Modules\Firm\Models\Firm;

class ServiceFeeSeeder extends Seeder
{
    public function run(): void
    {
        $firms = Firm::all();

        foreach ($firms as $firm) {
            // Reservation service fees
            ServiceFee::firstOrCreate(
                [
                    'name' => 'Rezervasyon Hizmet Bedeli',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => 'Standart rezervasyon işlemleri için hizmet bedeli',
                    'service_type' => 'reservation',
                    'fee_type' => 'percentage',
                    'fee_value' => 5.00, // 5%
                    'min_amount' => 50.00,
                    'max_amount' => 500.00,
                    'currency' => 'TRY',
                    'is_active' => true,
                    'is_mandatory' => true,
                ]
            );

            ServiceFee::firstOrCreate(
                [
                    'name' => 'Acil Rezervasyon Bedeli',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => '24 saat içinde yapılan rezervasyonlar için ek bedel',
                    'service_type' => 'reservation',
                    'fee_type' => 'fixed',
                    'fee_value' => 100.00,
                    'min_amount' => null,
                    'max_amount' => null,
                    'currency' => 'TRY',
                    'is_active' => true,
                    'is_mandatory' => false,
                ]
            );

            // Cancellation service fees
            ServiceFee::firstOrCreate(
                [
                    'name' => 'İptal Hizmet Bedeli',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => 'Rezervasyon iptali için hizmet bedeli',
                    'service_type' => 'cancellation',
                    'fee_type' => 'percentage',
                    'fee_value' => 10.00, // 10%
                    'min_amount' => 25.00,
                    'max_amount' => 200.00,
                    'currency' => 'TRY',
                    'is_active' => true,
                    'is_mandatory' => true,
                ]
            );

            // Modification service fees
            ServiceFee::firstOrCreate(
                [
                    'name' => 'Değişiklik Hizmet Bedeli',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => 'Rezervasyon değişikliği için hizmet bedeli',
                    'service_type' => 'modification',
                    'fee_type' => 'fixed',
                    'fee_value' => 75.00,
                    'min_amount' => null,
                    'max_amount' => null,
                    'currency' => 'TRY',
                    'is_active' => true,
                    'is_mandatory' => true,
                ]
            );

            // Booking service fees
            ServiceFee::firstOrCreate(
                [
                    'name' => 'Rezervasyon Garanti Bedeli',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => 'Rezervasyon garantisi için ek bedel',
                    'service_type' => 'booking',
                    'fee_type' => 'percentage',
                    'fee_value' => 2.00, // 2%
                    'min_amount' => 20.00,
                    'max_amount' => 100.00,
                    'currency' => 'TRY',
                    'is_active' => true,
                    'is_mandatory' => false,
                ]
            );
        }

        // Global service fees (not tied to specific firms)
        ServiceFee::firstOrCreate(
            [
                'name' => 'Genel Hizmet Bedeli',
                'firm_id' => null,
            ],
            [
                'description' => 'Tüm firmalar için geçerli genel hizmet bedeli',
                'service_type' => 'reservation',
                'fee_type' => 'fixed',
                'fee_value' => 25.00,
                'min_amount' => null,
                'max_amount' => null,
                'currency' => 'TRY',
                'is_active' => true,
                'is_mandatory' => true,
            ]
        );
    }
} 