<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Credit\Domain\Entities\CreditAccount;
use App\DDD\Modules\Firm\Models\Firm;

class CreditAccountSeeder extends Seeder
{
    public function run(): void
    {
        $firms = Firm::all();

        foreach ($firms as $firm) {
            // Set credit limit based on firm size/activity
            $creditLimit = 50000.00; // Default limit
            
            if ($firm->name === 'Bizigo') {
                $creditLimit = 100000.00; // Higher limit for main firm
            } elseif ($firm->name === 'TravelCorp') {
                $creditLimit = 75000.00; // Medium limit
            }

            CreditAccount::firstOrCreate(
                ['firm_id' => $firm->id],
                [
                    'balance' => $creditLimit * 0.3, // Start with 30% of limit as balance
                    'credit_limit' => $creditLimit,
                    'currency' => 'TRY',
                    'is_active' => true,
                ]
            );
        }
    }
} 