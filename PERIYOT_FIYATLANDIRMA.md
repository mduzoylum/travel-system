# Periyot Bazlı Farklı Para Birimi Fiyatlandırma

## Genel Bakış

Bu özellik, aynı otel ve oda tipi için farklı tarih periyotlarında farklı para birimleriyle fiyatlandırma yapılmasına olanak sağlar.

## Örnek Senaryo

Örnek görseldeki gibi:
- **27 Mart - 6 Nisan 2025**: TRY para birimi
- **21 Nisan - 30 Nisan 2025**: TRY para birimi
- **1 Mayıs - 11 Mayıs 2025**: EUR para birimi

## Kullanım Senaryosu

Müşteri, 7 Nisan giriş ve 11 Nisan çıkış için TRY para biriminde sorgu yaparsa:
- **7 Nisan - 10 Nisan** (4 gece): TRY kontratından alınır
- **11 Nisan**: EUR kontratından TRY'ye çevrilerek alınır
- Toplam fiyat TRY olarak gösterilir

## Yeni Özellikler

### 1. Tablolar

#### `contract_room_periods`
Her oda için farklı tarih periyotlarında farklı para birimleri ve fiyatlar tanımlanabilir.

```sql
- contract_room_id (FK)
- start_date (başlangıç tarihi)
- end_date (bitiş tarihi)
- currency (TRY, EUR, USD, vb.)
- base_price (maliyet)
- sale_price (satış fiyatı)
- is_active (aktif mi?)
```

#### `exchange_rates`
Para birimi dönüştürme için döviz kurları saklanır.

```sql
- from_currency (kaynak para birimi)
- to_currency (hedef para birimi)
- rate (döviz kuru)
- valid_from / valid_until (geçerlilik tarihleri)
- is_active
```

### 2. Yeni Model ve Servisler

#### `ContractRoomPeriod` Model
- Tarih bazlı periyot yönetimi
- Para birimi desteği
- Periyot çakışma kontrolü

#### `ExchangeRate` Model
- Döviz kurlarını saklama ve getirme
- Tarih bazlı kur sorgulama

#### `CurrencyExchangeService`
- Para birimi dönüştürme işlemleri
- Çoklu miktar toplama

#### `PricingService::calculateMultiPeriodPrice()`
- Tarih aralığı için tüm geceleri hesaplar
- Her gece için ilgili periyodu bulur
- Farklı para birimlerini hedef para birimine çevirir
- Toplam fiyatı hesaplar

#### `ReservationService::makeReservation()`
- `targetCurrency` parametresi eklendi
- Çoklu periyot fiyat hesaplamasını kullanır

### 3. Güncellenen Modeller

#### `ContractRoom`
- `periods()` ilişkisi eklendi
- `getPeriodForDate()` - Tarih için periyot getir
- `getPeriodsForDateRange()` - Tarih aralığı için periyotlar getir

#### `Money` ValueObject
- `convertTo()` metodu eklendi - Para birimi dönüştürme

## Kullanım Örneği

```php
use App\DDD\Modules\Contract\Services\PricingService;

$pricingService = new PricingService();

// Çoklu periyot fiyat hesaplama
$result = $pricingService->calculateMultiPeriodPrice(
    $room,          // ContractRoom
    $user,          // User
    '2025-04-07',   // checkin date
    '2025-04-11',   // checkout date
    'TRY',          // target currency
    1               // guest count
);

// Sonuç:
// - nights: 4
// - base_price: toplam maliyet
// - sale_price: toplam satış fiyatı
// - service_fee: servis bedeli
// - grand_total: toplam
// - currency: TRY
// - nightly_breakdown: her gece için detay
```

## Veritabanı Kurulumu

```bash
# Migration'ları çalıştır
php artisan migrate

# Örnek döviz kurlarını ekle
php artisan db:seed --class=ExchangeRateSeeder

# Örnek periyot verilerini ekle (isteğe bağlı)
php artisan db:seed --class=ContractRoomPeriodSeeder
```

## Avantajlar

1. **Esneklik**: Aynı otel için farklı tarihlerde farklı para birimleriyle fiyatlandırma
2. **Otomatik Dönüştürme**: Farklı para birimleri otomatik olarak hedef para birimine çevrilir
3. **Detaylı Raporlama**: Her gece için ayrıntılı fiyat breakdown'ı
4. **Geriye Dönük Uyumluluk**: Periyot yoksa eski hesaplama yöntemi kullanılır

## Notlar

- Aynı tarih aralığında birden fazla periyot olmamalıdır (validation eklenmeli)
- Döviz kurları tarih bazlı saklanır
- Para birimi dönüştürme sırasında kur bulunamazsa exception fırlatılır
- Periyot yoksa ContractRoom'daki varsayılan fiyatlar kullanılır
