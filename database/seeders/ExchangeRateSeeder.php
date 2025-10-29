<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Contract\Models\ExchangeRate;
use Carbon\Carbon;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            // EUR -> TRY
            [
                'from_currency' => 'EUR',
                'to_currency' => 'TRY',
                'rate' => 35.50,
                'valid_from' => '2025-01-01',
                'valid_until' => '2025-12-31',
                'is_active' => true,
            ],
            // USD -> TRY
            [
                'from_currency' => 'USD',
                'to_currency' => 'TRY',
                'rate' => 32.75,
                'valid_from' => '2025-01-01',
                'valid_until' => '2025-12-31',
                'is_active' => true,
            ],
            // TRY -> EUR (1 / 35.50)
            [
                'from_currency' => 'TRY',
                'to_currency' => 'EUR',
                'rate' => 0.0282,
                'valid_from' => '2025-01-01',
                'valid_until' => '2025-12-31',
                'is_active' => true,
            ],
            // TRY -> USD (1 / 32.75)
            [
                'from_currency' => 'TRY',
                'to_currency' => 'USD',
                'rate' => 0.0305,
                'valid_from' => '2025-01-01',
                'valid_until' => '2025-12-31',
                'is_active' => true,
            ],
            // USD -> EUR
            [
                'from_currency' => 'USD',
                'to_currency' => 'EUR',
                'rate' => 0.9225,
                'valid_from' => '2025-01-01',
                'valid_until' => '2025-12-31',
                'is_active' => true,
            ],
            // EUR -> USD
            [
                'from_currency' => 'EUR',
                'to_currency' => 'USD',
                'rate' => 1.0840,
                'valid_from' => '2025-01-01',
                'valid_until' => '2025-12-31',
                'is_active' => true,
            ],
        ];

        foreach ($rates as $rate) {
            ExchangeRate::updateOrCreate(
                [
                    'from_currency' => $rate['from_currency'],
                    'to_currency' => $rate['to_currency'],
                    'valid_from' => $rate['valid_from'],
                ],
                $rate
            );
        }
    }
}
