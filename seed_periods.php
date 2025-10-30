<?php

echo "Kontrat kontrolü...\n";

$contract = \App\DDD\Modules\Contract\Models\Contract::first();

if (!$contract) {
    echo "Kontrat yok!\n";
    exit;
}

echo "Kontrat bulundu: ID {$contract->id}\n";

$room = $contract->rooms()->first();

if (!$room) {
    echo "Oda bulunamadı!\n";
    exit;
}

echo "Oda bulundu: ID {$room->id}\n";

// Periyotları ekle
$periods = [
    [
        'contract_room_id' => $room->id,
        'start_date' => '2025-04-01',
        'end_date' => '2025-04-10',
        'currency' => 'TRY',
        'base_price' => 10000,
        'sale_price' => 12000,
        'notes' => 'Nisan başı dönem',
        'is_active' => true,
    ],
    [
        'contract_room_id' => $room->id,
        'start_date' => '2025-04-11',
        'end_date' => '2025-04-20',
        'currency' => 'EUR',
        'base_price' => 1000,
        'sale_price' => 1200,
        'notes' => 'Nisan ortası dönem',
        'is_active' => true,
    ],
    [
        'contract_room_id' => $room->id,
        'start_date' => '2025-04-21',
        'end_date' => '2025-04-30',
        'currency' => 'TRY',
        'base_price' => 10000,
        'sale_price' => 12000,
        'notes' => 'Nisan sonu dönem',
        'is_active' => true,
    ],
];

foreach ($periods as $p) {
    $period = \App\DDD\Modules\Contract\Models\ContractRoomPeriod::updateOrCreate(
        [
            'contract_room_id' => $p['contract_room_id'],
            'start_date' => $p['start_date'],
            'end_date' => $p['end_date']
        ],
        $p
    );
    echo "Periyot: {$period->start_date} - {$period->end_date} ({$period->currency})\n";
}

echo "\n✅ Başarılı! Toplam periyot sayısı: " . \App\DDD\Modules\Contract\Models\ContractRoomPeriod::count() . "\n";

