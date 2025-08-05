<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Profit\Models\ProfitRule;
use App\DDD\Modules\Firm\Models\Firm;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;

class ProfitRuleSeeder extends Seeder
{
    public function run(): void
    {
        $firms = Firm::all();
        $suppliers = Supplier::all();

        foreach ($firms as $firm) {
            // Domestic travel rules
            ProfitRule::firstOrCreate(
                [
                    'name' => 'Yurt İçi Standart Kar Marjı',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => 'Türkiye içi seyahatler için standart kar marjı',
                    'supplier_id' => null,
                    'destination' => 'Türkiye',
                    'trip_type' => 'domestic',
                    'travel_type' => 'round_trip',
                    'fee_type' => 'percentage',
                    'fee_value' => 15.00, // 15%
                    'min_fee' => 100.00,
                    'max_fee' => 1000.00,
                    'tier_rules' => json_encode([
                        ['min_amount' => 0, 'max_amount' => 1000, 'percentage' => 10],
                        ['min_amount' => 1000, 'max_amount' => 5000, 'percentage' => 15],
                        ['min_amount' => 5000, 'max_amount' => null, 'percentage' => 20],
                    ]),
                    'is_active' => true,
                    'priority' => 1,
                ]
            );

            // International travel rules
            ProfitRule::firstOrCreate(
                [
                    'name' => 'Yurt Dışı Kar Marjı',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => 'Yurt dışı seyahatler için kar marjı',
                    'supplier_id' => null,
                    'destination' => null,
                    'trip_type' => 'international',
                    'travel_type' => 'round_trip',
                    'fee_type' => 'percentage',
                    'fee_value' => 25.00, // 25%
                    'min_fee' => 200.00,
                    'max_fee' => 2000.00,
                    'tier_rules' => null,
                    'is_active' => true,
                    'priority' => 2,
                ]
            );

            // Luxury hotel rules
            ProfitRule::firstOrCreate(
                [
                    'name' => 'Lüks Otel Kar Marjı',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => '5 yıldızlı oteller için özel kar marjı',
                    'supplier_id' => null,
                    'destination' => null,
                    'trip_type' => 'domestic',
                    'travel_type' => 'round_trip',
                    'fee_type' => 'fixed',
                    'fee_value' => 500.00,
                    'min_fee' => 300.00,
                    'max_fee' => 1500.00,
                    'tier_rules' => null,
                    'is_active' => true,
                    'priority' => 3,
                ]
            );

            // Summer destination rules
            ProfitRule::firstOrCreate(
                [
                    'name' => 'Yaz Destinasyonu Kar Marjı',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => 'Antalya, Bodrum gibi yaz destinasyonları için kar marjı',
                    'supplier_id' => null,
                    'destination' => 'Antalya,Muğla',
                    'trip_type' => 'domestic',
                    'travel_type' => 'round_trip',
                    'fee_type' => 'percentage',
                    'fee_value' => 20.00, // 20%
                    'min_fee' => 150.00,
                    'max_fee' => 1200.00,
                    'tier_rules' => null,
                    'is_active' => true,
                    'priority' => 4,
                ]
            );
        }

        // Supplier-specific rules
        foreach ($suppliers as $supplier) {
            ProfitRule::firstOrCreate(
                [
                    'name' => $supplier->name . ' Özel Kar Marjı',
                    'supplier_id' => $supplier->id,
                ],
                [
                    'description' => $supplier->name . ' tedarikçisi için özel kar marjı',
                    'firm_id' => null,
                    'destination' => null,
                    'trip_type' => 'domestic',
                    'travel_type' => 'round_trip',
                    'fee_type' => 'percentage',
                    'fee_value' => 12.00, // 12%
                    'min_fee' => 80.00,
                    'max_fee' => 800.00,
                    'tier_rules' => null,
                    'is_active' => true,
                    'priority' => 5,
                ]
            );
        }

        // Global rules (not tied to specific firms or suppliers)
        ProfitRule::firstOrCreate(
            [
                'name' => 'Genel Kar Marjı',
                'firm_id' => null,
                'supplier_id' => null,
            ],
            [
                'description' => 'Tüm rezervasyonlar için geçerli genel kar marjı',
                'destination' => null,
                'trip_type' => 'domestic',
                'travel_type' => 'round_trip',
                'fee_type' => 'percentage',
                'fee_value' => 10.00, // 10%
                'min_fee' => 50.00,
                'max_fee' => 500.00,
                'tier_rules' => null,
                'is_active' => true,
                'priority' => 10,
            ]
        );
    }
} 