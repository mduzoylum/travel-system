# 📋 İstenen Özellik Özeti

## Talebiniz

Aynı otel ve oda tipi için farklı tarih periyotlarında **farklı para birimleri**yle fiyatlandırma yapabilme yeteneği.

### Örnek Senaryo

Görselinizdeki örneğe göre:
- **27 Mart - 6 Nisan 2025**: TRY para birimi (Maliyet: 10000 TRY, Komisyon: 2000 TRY)
- **21 Nisan - 30 Nisan 2025**: TRY para birimi (Maliyet: 10000 TRY, Komisyon: 2000 TRY)  
- **1 Mayıs - 11 Mayıs 2025**: EUR para birimi (Maliyet: 1000 EUR, Komisyon: 200 EUR)

### Kritik Gereksinim

Müşteri, **7 Nisan giriş - 11 Nisan çıkış** tarihlerinde rezervasyon yapmak istediğinde ve **TRY para birimi**nde sorgu yaptığında:

**Sistem şunu yapmalı:**
1. İlk 4 gece (7-10 Nisan): TRY kontratından alır → 12000 TRY/gece
2. Son 1 gece (11 Nisan): EUR kontratından alır → EUR 1200'ü TRY'ye çevirir → 42600 TRY
3. Toplam tutarı TRY olarak gösterir → ~90600 TRY + servis bedeli

## ✅ Yapılan İşler

### 1. Veritabanı Tabloları
- ✅ `contract_room_periods` - Periyot bazlı fiyatlandırma
- ✅ `exchange_rates` - Döviz kurları

### 2. Model ve Servisler
- ✅ `ContractRoomPeriod` modeli
- ✅ `ExchangeRate` modeli  
- ✅ `CurrencyExchangeService` - Para birimi dönüştürme
- ✅ `PricingService::calculateMultiPeriodPrice()` - Çoklu periyot hesaplama
- ✅ `ReservationService` güncellendi

### 3. Otomatik Entegrasyon
✅ **Arka planda otomatik çalışır!** 

`makeReservation()` metodu çağrıldığında:
- Otomatik olarak çoklu periyot fiyat hesaplaması yapılır
- Farklı para birimleri hedef para birimine dönüştürülür
- Toplam fiyat hesaplanır

### 4. Test Arayüzü
- ✅ `/admin/test-pricing` - Geliştiriciler için test sayfası

## 🎯 Müşteri Nasıl Test Eder?

### Senaryo 1: Rezervasyon Oluşturma (Tinker ile)

```php
php artisan tinker

// Tinker içinde:
$user = App\Models\User::first();
$room = App\DDD\Modules\Contract\Models\ContractRoom::with('periods')->first();

$reservationService = new App\DDD\Modules\Reservation\Services\ReservationService();

// Rezervasyon oluştur (otomatik çoklu periyot fiyat hesaplanır)
$reservation = $reservationService->makeReservation(
    $user, 
    $room,
    '2025-04-07',  // Giriş
    '2025-04-11',  // Çıkış
    1,             // Misafir sayısı
    'TRY'          // Hedef para birimi
);

echo "Toplam Fiyat: " . $reservation->total_price . " TRY";
```

### Senaryo 2: Fiyat Hesaplama (API gibi)

```php
php artisan tinker

// Tinker içinde:
$user = App\Models\User::first();
$room = App\DDD\Modules\Contract\Models\ContractRoom::with('periods')->first();

$pricingService = new App\DDD\Modules\Contract\Services\PricingService();

$result = $pricingService->calculateMultiPeriodPrice(
    $room,
    $user, 
    '2025-04-07',  // Giriş
    '2025-04-11',  // Çıkış
    'TRY',         // Hedef para birimi
    1              // Misafir sayısı
);

echo "Gece Sayısı: " . $result['nights'] . "\n";
echo "Toplam: " . $result['grand_total'] . " " . $result['currency'] . "\n";
echo "\nGece Gece Detay:\n";
foreach($result['nightly_breakdown'] as $night) {
    echo "  - {$night['date']}: {$night['sale_price']} {$night['currency']} (özgün: {$night['period_currency']})\n";
}
```

### Senaryo 3: Web Arayüzü (Geliştiriciler için)

```
1. Tarayıcıda http://localhost/admin/test-pricing açın
2. Oda seçin
3. Giriş tarihi: 2025-04-07
4. Çıkış tarihi: 2025-04-11
5. Para birimi: TRY
6. "Fiyat Hesapla" butonuna basın

Sonuçta göreceksiniz:
- Gece gece fiyat detayları
- Para birimi dönüşümleri
- Toplam tutar
```

## 📊 Veri Örneği

Sisteme eklenmiş test verileri:

**Periyotlar:**
- 01-10 Nisan: TRY, 12000 TL/gece
- 11-20 Nisan: EUR, 1200 EUR/gece
- 21-30 Nisan: TRY, 12000 TL/gece

**Döviz Kurları:**
- EUR → TRY: 35.50
- TRY → EUR: 0.0282
- USD → TRY: 32.75

## 🔗 İlgili Dosyalar

- Models: `app/DDD/Modules/Contract/Models/ContractRoomPeriod.php`, `ExchangeRate.php`
- Services: `app/DDD/Modules/Contract/Services/PricingService.php`, `CurrencyExchangeService.php`
- Migrations: `database/migrations/*_create_contract_room_periods_table.php`, `*_create_exchange_rates_table.php`
- Test: `routes/web.php` (test-pricing route'ları), `resources/views/admin/test-pricing.blade.php`

## ⚙️ Yapılandırma

### Döviz Kurları Ekleyin

```bash
php artisan db:seed --class=ExchangeRateSeeder
```

### Yeni Periyot Ekleyin

```php
App\DDD\Modules\Contract\Models\ContractRoomPeriod::create([
    'contract_room_id' => 1,
    'start_date' => '2025-05-01',
    'end_date' => '2025-05-15',
    'currency' => 'EUR',
    'base_price' => 1000,
    'sale_price' => 1200,
    'is_active' => true
]);
```

## 🎉 Sonuç

Artık sistem:
- ✅ Farklı tarih aralıklarında farklı para birimleriyle fiyatlandırma destekliyor
- ✅ Para birimlerini otomatik dönüştürüyor
- ✅ Rezervasyon sırasında otomatik olarak çoklu periyot fiyat hesaplaması yapıyor
- ✅ Geriye dönük uyumlu (periyot yoksa eski yöntemi kullanıyor)

**Test sayfası sadece gösterim amaçlıdır. Asıl özellik arka planda otomatik çalışmaktadır!**
