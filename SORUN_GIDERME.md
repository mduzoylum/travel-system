# Sorun Giderme Rehberi

## 302 Found hatası alıyorsunuz

### Adım 1: Hata mesajını kontrol edin
Sayfa yenilendiğinde kırmızı bir hata mesajı görünecek.

### Adım 2: Log dosyasını kontrol edin
```bash
tail -50 storage/logs/laravel.log
```

### Adım 3: Yaygın Hatalar

#### "Döviz kuru bulunamadı"
**Çözüm:**
```bash
php artisan db:seed --class=ExchangeRateSeeder
```

#### "Oda bulunamadı"
**Çözüm:** Form'da bir oda seçtiğinizden emin olun.

#### "Kontrat bulunamadı"
**Çözüm:**
```bash
php artisan db:seed --class=ContractSeeder
# veya
php artisan migrate:fresh --seed
```

#### "Call to undefined method"
**Çözüm:** Migration'ların çalıştırıldığından emin olun:
```bash
php artisan migrate
```

### Adım 4: Manuel Test

Tinker ile direkt test edebilirsiniz:

```php
php artisan tinker

// Tinker içinde:
$room = App\DDD\Modules\Contract\Models\ContractRoom::first();
$user = App\Models\User::first();
$pricingService = new App\DDD\Modules\Contract\Services\PricingService();
$result = $pricingService->calculateMultiPeriodPrice(
    $room, $user, '2025-04-07', '2025-04-11', 'TRY', 1
);
print_r($result);
```

### Adım 5: Veritabanı Kontrol

```bash
# Tüm tablolar var mı?
php artisan migrate:status

# Periyotlar var mı?
php artisan tinker --execute="echo 'Periyot sayısı: ' . App\DDD\Modules\Contract\Models\ContractRoomPeriod::count() . PHP_EOL;"

# Döviz kurları var mı?
php artisan tinker --execute="echo 'Kur sayısı: ' . App\DDD\Modules\Contract\Models\ExchangeRate::count() . PHP_EOL;"
```

## Hala Çalışmıyor?

1. Cache'i temizleyin:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

2. Sunucuyu yeniden başlatın
```bash
# Ctrl+C ile durdurun
php artisan serve
```

3. Permissions kontrol edin
```bash
chmod -R 775 storage bootstrap/cache
```
