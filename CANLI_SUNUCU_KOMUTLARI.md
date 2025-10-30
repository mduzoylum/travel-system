# Canlı Sunucuda Çalıştırılacak Komutlar

## 1. Döviz Kurlarını Ekleyin
```bash
php artisan db:seed --class=ExchangeRateSeeder
```

## 2. Periyot Verilerini Ekleyin

Tinker'ı açın ve şu komutları tek tek çalıştırın:

```bash
php artisan tinker
```

Tinker içinde:

```php
$contract = \App\DDD\Modules\Contract\Models\Contract::first();
$room = $contract->rooms()->first();

$periods = [
    ['contract_room_id' => $room->id, 'start_date' => '2025-04-01', 'end_date' => '2025-04-10', 'currency' => 'TRY', 'base_price' => 10000, 'sale_price' => 12000, 'notes' => 'Test', 'is_active' => true],
    ['contract_room_id' => $room->id, 'start_date' => '2025-04-11', 'end_date' => '2025-04-20', 'currency' => 'EUR', 'base_price' => 1000, 'sale_price' => 1200, 'notes' => 'Test', 'is_active' => true],
    ['contract_room_id' => $room->id, 'start_date' => '2025-04-21', 'end_date' => '2025-04-30', 'currency' => 'TRY', 'base_price' => 10000, 'sale_price' => 12000, 'notes' => 'Test', 'is_active' => true]
];

foreach($periods as $p) { 
    \App\DDD\Modules\Contract\Models\ContractRoomPeriod::updateOrCreate(['contract_room_id' => $p['contract_room_id'], 'start_date' => $p['start_date'], 'end_date' => $p['end_date']], $p); 
    echo $p['start_date'] . ' - ' . $p['end_date'] . ' (' . $p['currency'] . ')' . PHP_EOL;
}
```

## Alternatif: Tek Komutla Çalıştırma

```bash
php artisan tinker --execute="\$contract = \App\DDD\Modules\Contract\Models\Contract::first(); \$room = \$contract->rooms()->first(); \$periods = [['contract_room_id' => \$room->id, 'start_date' => '2025-04-01', 'end_date' => '2025-04-10', 'currency' => 'TRY', 'base_price' => 10000, 'sale_price' => 12000, 'is_active' => true],['contract_room_id' => \$room->id, 'start_date' => '2025-04-11', 'end_date' => '2025-04-20', 'currency' => 'EUR', 'base_price' => 1000, 'sale_price' => 1200, 'is_active' => true],['contract_room_id' => \$room->id, 'start_date' => '2025-04-21', 'end_date' => '2025-04-30', 'currency' => 'TRY', 'base_price' => 10000, 'sale_price' => 12000, 'is_active' => true]]; foreach(\$periods as \$p) { \$period = \App\DDD\Modules\Contract\Models\ContractRoomPeriod::updateOrCreate(['contract_room_id' => \$p['contract_room_id'], 'start_date' => \$p['start_date'], 'end_date' => \$p['end_date']], \$p); echo \$period->start_date . ' - ' . \$period->end_date . ' (' . \$period->currency . ')' . PHP_EOL; }"
```
