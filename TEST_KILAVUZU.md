# Periyot Bazlı Fiyatlandırma Test Kılavuzu

## 🚀 Hızlı Test Adımları

### 1. Gerekli Verileri Oluşturun

#### A) Otomatik (Önerilen)
```bash
# Döviz kurlarını ekleyin
php artisan db:seed --class=ExchangeRateSeeder

# Test periyotlarını ekleyin (php artisan tinker kullanarak)
php artisan tinker < test_period_pricing.php
```

#### B) Manuel Seeder ile
```bash
# Sadece döviz kurları
php artisan db:seed --class=ExchangeRateSeeder

# Periyot seeder (contract_room_periods tablosuna veri ekler)
php artisan db:seed --class=ContractRoomPeriodSeeder
```

### 2. Test Sayfasına Gidin

Web tarayıcınızda şu adrese gidin:
```
http://localhost/admin/test-pricing
```
veya (XAMPP kullanıyorsanız)
```
http://localhost/travel-system/public/admin/test-pricing
```

### 3. Test Parametrelerini Girin

Sayfada göreceğiniz alanlar:
- **Oda Seçin**: Dropdown'dan periyot tanımlanmış bir oda seçin
- **Giriş Tarihi**: `2025-04-07` (TRY periyodunda)
- **Çıkış Tarihi**: `2025-04-11` (EUR periyodunda)
- **Para Birimi**: `TRY` (hedef para birimi)
- **Misafir Sayısı**: `1`

### 4. "Fiyat Hesapla" Butonuna Tıklayın

Sonuçta göreceksiniz:
- **Gece Sayısı**: 4 gece
- **Toplam Maliyet**: TRY cinsinden toplam maliyet
- **Toplam Satış Fiyatı**: TRY cinsinden toplam satış
- **Servis Bedeli**: Kullanıcının firma ayarlarından
- **GENEL TOPLAM**: Tüm masrafların toplamı
- **Gece Gece Detay**: Her gece için hangi periyot kullanıldığı ve para birimi dönüşümü

## 📊 Beklenen Sonuçlar

Örnek parametrelerle (7-11 Nisan, TRY):

| Tarih | Periyot | Para Birimi | Satış Fiyatı | Dönüştürülmüş TRY |
|-------|---------|-------------|--------------|-------------------|
| 07 Nisan | TRY | TRY | 12000 TRY | 12000 TRY |
| 08 Nisan | TRY | TRY | 12000 TRY | 12000 TRY |
| 09 Nisan | TRY | TRY | 12000 TRY | 12000 TRY |
| 10 Nisan | TRY | TRY | 12000 TRY | 12000 TRY |
| 11 Nisan | EUR | EUR | 1200 EUR | 42600 TRY (1200 × 35.5) |

**Toplam:** ~90600 TRY + Servis Bedeli

## 🔍 Veri Kontrolü

### Döviz Kurlarını Kontrol Edin
```sql
SELECT * FROM exchange_rates WHERE is_active = 1;
```
Veya tinker ile:
```php
php artisan tinker
>>> App\DDD\Modules\Contract\Models\ExchangeRate::all();
```

### Periyotları Kontrol Edin
```sql
SELECT * FROM contract_room_periods WHERE is_active = 1 ORDER BY start_date;
```
Veya tinker ile:
```php
php artisan tinker
>>> $room = App\DDD\Modules\Contract\Models\ContractRoom::first();
>>> $room->periods;
```

## 🎯 Test Senaryoları

### Senaryo 1: Tek Para Birimi (TRY)
- Giriş: 01 Nisan 2025
- Çıkış: 10 Nisan 2025
- Beklenen: Tüm geceler TRY periyodundan

### Senaryo 2: Para Birimi Değişimi (TRY → EUR)
- Giriş: 05 Nisan 2025
- Çıkış: 15 Nisan 2025
- Beklenen: İlk kısmı TRY, sonra EUR'ya çevrilmiş TRY

### Senaryo 3: Farklı Hedef Para Birimi (USD)
- Giriş: 07 Nisan 2025
- Çıkış: 11 Nisan 2025
- Para Birimi: USD
- Beklenen: Tüm fiyatlar USD'ye çevrilmiş

### Senaryo 4: Periyot Olmayan Oda
- Periyot tanımlanmamış bir oda seçin
- Beklenen: Varsayılan fiyat (ContractRoom'daki sale_price) kullanılır

## 🐛 Sorun Giderme

### "Döviz kuru bulunamadı" Hatası
- `ExchangeRateSeeder` çalıştırıldı mı kontrol edin
- `exchange_rates` tablosunda veri var mı bakın

### "Oda bulunamadı" Hatası
- `contract_rooms` tablosunda veri var mı kontrol edin
- En az bir kontrat ve oda olması gerekiyor

### "Periyot bulunamadı" Uyarısı
- Seçilen odada periyot tanımlı mı kontrol edin
- Tarih aralığı periyotlarla uyuşuyor mu bakın

### Hesaplama Sonucu Hiç Dönmüyor
- PHP hata loglarına bakın: `storage/logs/laravel.log`
- Tarayıcı konsolunu kontrol edin

## 📝 Notlar

- Test sayfası sadece admin kullanıcılar tarafından erişilebilir
- Giriş yapmış olmanız gerekiyor
- Döviz kurları tarihe göre saklanır ama şu an tüm 2025 için aynı kur geçerli
- Periyotlar birbiriyle çakışmamalı (örnekte çakışma yok)

## 🔗 İlgili Dosyalar

- Test Controller: `routes/web.php` (test-pricing route'ları)
- Test View: `resources/views/admin/test-pricing.blade.php`
- Test Script: `test_period_pricing.php`
- Hesaplama Servisi: `app/DDD/Modules/Contract/Services/PricingService.php`
- Para Birimi Servisi: `app/DDD/Modules/Contract/Services/CurrencyExchangeService.php`
