<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Contract\Models\Hotel;
use App\DDD\Modules\Firm\Models\Firm;
use App\DDD\Modules\Contract\Models\Contract;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use App\DDD\Modules\Credit\Domain\Entities\CreditAccount;
use App\DDD\Modules\Credit\Domain\Entities\CreditTransaction;
use App\DDD\Modules\Credit\Domain\ValueObjects\Money;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Tedarikçiler
        $suppliers = [
            ['name' => 'OtelBest', 'type' => 'otelbest', 'is_active' => true],
            ['name' => 'X Firması', 'type' => 'x_firm', 'is_active' => true],
            ['name' => 'Y Firması', 'type' => 'y_firm', 'is_active' => true],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::firstOrCreate(['name' => $supplierData['name']], $supplierData);
        }

        // Firmalar
        $firms = [
            ['name' => 'ABC Şirketi', 'email_domain' => 'abc.com', 'is_active' => true],
            ['name' => 'XYZ Holding', 'email_domain' => 'xyz.com', 'is_active' => true],
            ['name' => 'DEF Turizm', 'email_domain' => 'def.com', 'is_active' => true],
        ];

        foreach ($firms as $firmData) {
            Firm::firstOrCreate(['name' => $firmData['name']], $firmData);
        }

        // Oteller
        $hotels = [
            [
                'name' => 'Grand Hotel Istanbul',
                'city' => 'İstanbul',
                'country' => 'Türkiye',
                'stars' => 5,
                'min_price' => 1500.00,
                'is_contracted' => true
            ],
            [
                'name' => 'Blue Sea Resort',
                'city' => 'Antalya',
                'country' => 'Türkiye',
                'stars' => 4,
                'min_price' => 1200.00,
                'is_contracted' => true
            ],
            [
                'name' => 'Mountain View Hotel',
                'city' => 'Bursa',
                'country' => 'Türkiye',
                'stars' => 3,
                'min_price' => 800.00,
                'is_contracted' => true
            ],
            [
                'name' => 'Business Center Hotel',
                'city' => 'İstanbul',
                'country' => 'Türkiye',
                'stars' => 4,
                'min_price' => 1800.00,
                'is_contracted' => true
            ],
            [
                'name' => 'Beach Paradise',
                'city' => 'Muğla',
                'country' => 'Türkiye',
                'stars' => 5,
                'min_price' => 2000.00,
                'is_contracted' => true
            ]
        ];

        foreach ($hotels as $hotelData) {
            Hotel::firstOrCreate(['name' => $hotelData['name']], $hotelData);
        }

        // Kontratlar
        $contracts = [
            [
                'hotel_id' => 1,
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(90),
                'currency' => 'TRY',
                'is_active' => true
            ],
            [
                'hotel_id' => 2,
                'start_date' => now()->addDays(15),
                'end_date' => now()->addDays(75),
                'currency' => 'TRY',
                'is_active' => true
            ],
            [
                'hotel_id' => 3,
                'start_date' => now()->addDays(60),
                'end_date' => now()->addDays(120),
                'currency' => 'TRY',
                'is_active' => true
            ]
        ];

        foreach ($contracts as $contractData) {
            Contract::firstOrCreate(['hotel_id' => $contractData['hotel_id']], $contractData);
        }

        // Kredi Hesapları
        $creditAccounts = [
            [
                'firm_id' => 1,
                'balance' => 50000.00,
                'credit_limit' => 100000.00,
                'currency' => 'TRY',
                'is_active' => true
            ],
            [
                'firm_id' => 2,
                'balance' => 75000.00,
                'credit_limit' => 150000.00,
                'currency' => 'TRY',
                'is_active' => true
            ],
            [
                'firm_id' => 3,
                'balance' => 25000.00,
                'credit_limit' => 50000.00,
                'currency' => 'TRY',
                'is_active' => true
            ]
        ];

        foreach ($creditAccounts as $accountData) {
            $creditAccount = CreditAccount::firstOrCreate(['firm_id' => $accountData['firm_id']], $accountData);
            
            // Kredi işlemleri ekle (sadece yeni oluşturulanlar için)
            if ($creditAccount->wasRecentlyCreated) {
                $money = new Money(50000.00, 'TRY');
                $creditAccount->addCredit($money, 'İlk kredi yükleme');
                
                $money = new Money(5000.00, 'TRY');
                $creditAccount->useCredit($money, 'Test rezervasyonu');
            }
        }

        $this->command->info('Test verileri başarıyla eklendi!');
    }
} 