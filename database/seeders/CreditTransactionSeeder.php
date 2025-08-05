<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Credit\Domain\Entities\CreditTransaction;
use App\DDD\Modules\Credit\Domain\Entities\CreditAccount;
use Carbon\Carbon;

class CreditTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $creditAccounts = CreditAccount::all();

        foreach ($creditAccounts as $account) {
            $currentBalance = $account->balance;
            $currentDate = Carbon::now()->subMonths(3);

            // Initial credit transaction
            CreditTransaction::firstOrCreate(
                [
                    'credit_account_id' => $account->id,
                    'type' => 'credit',
                    'amount' => $account->balance,
                    'description' => 'Başlangıç kredisi',
                ],
                [
                    'reference_type' => 'manual',
                    'reference_id' => null,
                    'balance_after' => $currentBalance,
                    'created_at' => $currentDate,
                ]
            );

            // Generate some sample transactions over the last 3 months
            for ($i = 0; $i < 15; $i++) {
                $currentDate->addDays(rand(1, 7));
                
                // Random transaction type
                $transactionType = rand(0, 1) ? 'credit' : 'debit';
                
                // Random amount based on transaction type
                if ($transactionType === 'credit') {
                    $amount = rand(1000, 5000);
                    $currentBalance += $amount;
                    $description = 'Kredi yükleme';
                } else {
                    $amount = rand(500, 3000);
                    if ($currentBalance >= $amount) {
                        $currentBalance -= $amount;
                        $description = 'Rezervasyon ödemesi';
                    } else {
                        continue; // Skip if insufficient balance
                    }
                }

                CreditTransaction::firstOrCreate(
                    [
                        'credit_account_id' => $account->id,
                        'type' => $transactionType,
                        'amount' => $amount,
                        'description' => $description,
                        'created_at' => $currentDate,
                    ],
                    [
                        'reference_type' => $transactionType === 'credit' ? 'manual' : 'reservation',
                        'reference_id' => $transactionType === 'credit' ? null : rand(1, 100),
                        'balance_after' => $currentBalance,
                    ]
                );
            }

            // Update the account balance to reflect transactions
            $account->update(['balance' => $currentBalance]);
        }
    }
} 