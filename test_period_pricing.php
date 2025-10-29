<?php

/**
 * Test script for multi-period pricing
 * Run: php artisan tinker < test_period_pricing.php
 */

// 1. Önce döviz kurlarını kontrol et
$rates = \App\DDD\Modules\Contract\Models\ExchangeRate::count();
echo "Döviz kuru sayısı: {$rates}\n";

if ($rates == 0) {
    echo "Döviz kurları yok, ekleniyor...\n";
    Artisan::call('db:seed', ['--class' => 'ExchangeRateSeeder']);
    echo "Döviz kurları eklendi!\n";
}

// 2. Kontrat ve oda kontrol et
$contract = \App\DDD\Modules\Contract\Models\Contract::with('rooms')->first();

if (!$contract) {
    echo "Henüz kontrat yok! Önce DatabaseSeeder çalıştırın:\n";
    echo "php artisan db:seed --class=ContractSeeder\n";
    exit;
}

echo "Kontrat bulundu: ID {$contract->id}\n";
echo "Oda sayısı: " . $contract->rooms->count() . "\n";

$room = $contract->rooms->first();
echo "Oda ID: {$room->id}\n";

// 3. Periyot ekle
echo "\nPeriyotlar ekleniyor...\n";

$periods = [
    [
        'contract_room_id' => $room->id,
        'start_date' => '2025-04-01',
        'end_date' => '2025-04-10',
        'currency' => 'TRY',
        'base_price' => 10000.00,
        'sale_price' => 12000.00,
        'notes' => 'Nisan başı dönem - TRY',
        'is_active' => true,
    ],
    [
        'contract_room_id' => $room->id,
        'start_date' => '2025-04-11',
        'end_date' => '2025-04-20',
        'currency' => 'EUR',
        'base_price' => 1000.00,
        'sale_price' => 1200.00,
        'notes' => 'Nisan ortası dönem - EUR',
        'is_active' => true,
    ],
    [
        'contract_room_id' => $room->id,
        'start_date' => '2025-04-21',
        'end_date' => '2025-04-30',
        'currency' => 'TRY',
        'base_price' => 10000.00,
        'sale_price' => 12000.00,
        'notes' => 'Nisan sonu dönem - TRY',
        'is_active' => true,
    ],
];

foreach ($periods as $periodData) {
    $period = \App\DDD\Modules\Contract\Models\ContractRoomPeriod::updateOrCreate(
        [
            'contract_room_id' => $periodData['contract_room_id'],
            'start_date' => $periodData['start_date'],
            'end_date' => $periodData['end_date'],
        ],
        $periodData
    );
    echo "Periyot eklendi: {$period->start_date} - {$period->end_date} ({$period->currency})\n";
}

// 4. Test hesaplama yap
echo "\n=== Test Hesaplama ===\n";
echo "Giriş: 2025-04-07\n";
echo "Çıkış: 2025-04-11\n";
echo "Para Birimi: TRY\n\n";

$user = \App\Models\User::first();
$pricingService = new \App\DDD\Modules\Contract\Services\PricingService();

$result = $pricingService->calculateMultiPeriodPrice(
    $room,
    $user,
    '2025-04-07',
    '2025-04-11',
    'TRY',
    1
);

echo "Gece Sayısı: {$result['nights']}\n";
echo "Toplam Satış Fiyatı: " . number_format($result['sale_price'], 2) . " TRY\n";
echo "Servis Bedeli: " . number_format($result['service_fee'], 2) . " TRY\n";
echo "GENEL TOPLAM: " . number_format($result['grand_total'], 2) . " TRY\n\n";

echo "Gece Gece Detay:\n";
foreach ($result['nightly_breakdown'] as $night) {
    echo "  - {$night['date']}: {$night['sale_price']} {$night['currency']} (özgün: {$night['period_currency']})\n";
}

echo "\n✅ Test başarılı! Artık web arayüzünden test edebilirsiniz:\n";
echo "   http://your-domain/admin/test-pricing\n";
