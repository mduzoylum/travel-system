<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Approval\Models\ApprovalRule;
use App\DDD\Modules\Approval\Models\ApprovalScenario;

class ApprovalRuleSeeder extends Seeder
{
    public function run(): void
    {
        $scenarios = ApprovalScenario::all();

        foreach ($scenarios as $scenario) {
            if (str_contains($scenario->name, 'Yüksek Değerli')) {
                // High-value rules
                ApprovalRule::firstOrCreate(
                    [
                        'scenario_id' => $scenario->id,
                        'rule_type' => 'amount',
                        'field_name' => 'total_price',
                    ],
                    [
                        'operator' => 'greater_than',
                        'value' => json_encode(['5000']),
                        'priority' => 1,
                        'is_active' => true,
                    ]
                );
            } elseif (str_contains($scenario->name, 'Yurt Dışı')) {
                // International travel rules
                ApprovalRule::firstOrCreate(
                    [
                        'scenario_id' => $scenario->id,
                        'rule_type' => 'destination',
                        'field_name' => 'country',
                    ],
                    [
                        'operator' => 'not_equals',
                        'value' => json_encode(['Türkiye']),
                        'priority' => 1,
                        'is_active' => true,
                    ]
                );
            } elseif (str_contains($scenario->name, 'Lüks Otel')) {
                // Luxury hotel rules
                ApprovalRule::firstOrCreate(
                    [
                        'scenario_id' => $scenario->id,
                        'rule_type' => 'price_range',
                        'field_name' => 'hotel_stars',
                    ],
                    [
                        'operator' => 'equals',
                        'value' => json_encode(['5']),
                        'priority' => 1,
                        'is_active' => true,
                    ]
                );
            } else {
                // Standard rules
                ApprovalRule::firstOrCreate(
                    [
                        'scenario_id' => $scenario->id,
                        'rule_type' => 'amount',
                        'field_name' => 'total_price',
                    ],
                    [
                        'operator' => 'between',
                        'value' => json_encode(['1000', '5000']),
                        'priority' => 1,
                        'is_active' => true,
                    ]
                );

                ApprovalRule::firstOrCreate(
                    [
                        'scenario_id' => $scenario->id,
                        'rule_type' => 'destination',
                        'field_name' => 'country',
                    ],
                    [
                        'operator' => 'equals',
                        'value' => json_encode(['Türkiye']),
                        'priority' => 2,
                        'is_active' => true,
                    ]
                );
            }

            // Add duration rule for all scenarios
            ApprovalRule::firstOrCreate(
                [
                    'scenario_id' => $scenario->id,
                    'rule_type' => 'duration',
                    'field_name' => 'stay_duration',
                ],
                [
                    'operator' => 'greater_than',
                    'value' => json_encode(['7']), // More than 7 days
                    'priority' => 3,
                    'is_active' => true,
                ]
            );
        }
    }
} 