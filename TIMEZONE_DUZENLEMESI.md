# Timezone Düzenlemesi

## Sorun
Sistem UTC saatine kayıt yapıyor, ancak Türkiye saati kullanılması gerekiyor (UTC+3). Bu durum özellikle otomatik iptaller ve uçak ürünü opsiyon saatlerinde kritik önem taşıyor.

## Yapılan Değişiklikler

### 1. config/app.php
```php
'timezone' => 'Europe/Istanbul', // Turkey timezone (UTC+3)
```

### 2. app/Providers/AppServiceProvider.php
```php
// Timezone'u Türkiye saati olarak ayarla
Carbon::setLocale('tr');
date_default_timezone_set(config('app.timezone'));
```

## Canlı Sunucuda Yapılacaklar

### Adım 1: Dosyaları Güncelle
```bash
git pull origin main
```

### Adım 2: Cache Temizle
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Adım 3: Sunucu Zaman Dilimini Kontrol Et
```bash
# PHP timezone'unu kontrol et
php -i | grep timezone

# MySQL timezone'unu kontrol et
mysql -e "SELECT @@global.time_zone, @@session.time_zone;"
```

### Adım 4: MySQL'de Timezone Ayarla (Gerekirse)
```sql
-- MySQL timezone'u Europe/Istanbul yap
SET GLOBAL time_zone = '+03:00';
```

Ya da my.cnf dosyasına ekleyin:
```ini
default_time_zone = '+03:00'
```

### Adım 5: PHP.ini Kontrol Et (Opsiyonel)
Canlı sunucuda `/etc/php.ini` veya PHP-FPM config dosyasını kontrol edin:
```ini
date.timezone = Europe/Istanbul
```

## Test Etme

### Test 1: Yeni Kredi İşlemi
```php
php artisan tinker

$account = App\DDD\Modules\Credit\Domain\Entities\CreditAccount::first();
$account->addCredit(new App\DDD\Modules\Credit\Domain\ValueObjects\Money(100, 'EUR'), 'Test');
echo "Time: " . now()->format('Y-m-d H:i:s') . "\n";
```

Sonuç Türkiye saati ile olmalı.

### Test 2: Mevcut Kayıtları Kontrol Et
```php
php artisan tinker

$transaction = App\DDD\Modules\Credit\Domain\Entities\CreditTransaction::latest()->first();
echo "Created at: " . $transaction->created_at->format('Y-m-d H:i:s') . "\n";
echo "Current time: " . now()->format('Y-m-d H:i:s') . "\n";
```

## Önemli Notlar

⚠️ **ÖNEMLİ:**
- Eski kayıtlar UTC olarak kaldı (değiştirilmedi)
- Yeni kayıtlar artık Türkiye saati ile kaydedilecek
- Eğer eski kayıtları düzeltmek istiyorsanız migration yazılmalı
- Sunucunun sistem saati de Türkiye saati olmalı

## Sorun Devam Ederse

### 1. PHP-FPM Restart
```bash
sudo systemctl restart php-fpm
# veya
sudo service php8.2-fpm restart
```

### 2. Web Server Restart
```bash
sudo systemctl restart nginx
# veya
sudo service apache2 restart
```

### 3. Log Kontrol
```bash
tail -f storage/logs/laravel.log
```

## Gelecek Kayıtlar İçin Garanti

Artık tüm `created_at` ve `updated_at` alanları Türkiye saati ile kaydedilecek:
- CreditTransactions
- Reservations
- Contracts
- CreditAccounts
- Ve diğer tüm modeller

## Eski Kayıtları Düzeltmek İçin (Opsiyonel)

Eğer eski kayıtları da Türkiye saatine çevirmek isterseniz:

```php
php artisan tinker

// Tüm credit_transactions için +3 saat ekle
DB::table('credit_transactions')->chunk(100, function($transactions) {
    foreach($transactions as $transaction) {
        $oldTime = Carbon::createFromFormat('Y-m-d H:i:s', $transaction->created_at, 'UTC');
        $newTime = $oldTime->copy()->addHours(3);
        
        DB::table('credit_transactions')
            ->where('id', $transaction->id)
            ->update(['created_at' => $newTime, 'updated_at' => $newTime]);
    }
});
```

**⚠️ DİKKAT:** Bu işlem geri alınamaz. Yedek alın!
