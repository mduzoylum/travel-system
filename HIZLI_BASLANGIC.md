# 🚀 Hızlı Başlangıç - Periyot Fiyatlandırma Testi

## 3 Adımda Test Edin

### Adım 1: Örnek Verileri Oluşturun

```bash
# 1. Döviz kurlarını ekleyin
php artisan db:seed --class=ExchangeRateSeeder

# 2. Test periyotlarını ekleyin
php artisan tinker < test_period_pricing.php
```

### Adım 2: Tarayıcıda Test Sayfasını Açın

```
http://localhost/admin/test-pricing
```

(Giriş yapmanız gerekiyor!)

### Adım 3: Test Parametrelerini Girin ve Hesaplayın

- **Oda**: Dropdown'dan seçin
- **Giriş**: `2025-04-07`
- **Çıkış**: `2025-04-11`
- **Para Birimi**: `TRY`
- **Misafir**: `1`

"Fiyat Hesapla" butonuna tıklayın!

## ✅ Başarılı Olursa...

Şunları göreceksiniz:
- 4 gece için toplam fiyat (TRY cinsinden)
- Her gece için ayrıntılı breakdown
- EUR → TRY dönüştürme sonuçları
- Oda periyotları listesi
- Döviz kurları listesi

## 📚 Detaylı Bilgi

- [TEST_KILAVUZU.md](TEST_KILAVUZU.md) - Detaylı test senaryoları
- [PERIYOT_FIYATLANDIRMA.md](PERIYOT_FIYATLANDIRMA.md) - Teknik dokümantasyon
