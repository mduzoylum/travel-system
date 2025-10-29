# 🎯 Müşteri Test Kılavuzu

## Amaç
Müşterilerin farklı tarih periyotlarında farklı para birimleriyle fiyatlandırma özelliğini test etmesi için.

---

## 📍 Adresler

### 1. Admin Panel - Kontrat Odaları
```
http://localhost/admin/contracts/{contract_id}/rooms
```

### 2. Fiyat Hesaplama Sayfası
```
http://localhost/admin/test-pricing
```

---

## 🚀 Test Adımları

### Seçenek 1: Kontrat Odaları Sayfasından (Önerilen)

1. **Admin panelden kontrat odalarına gidin**
   - Menü: `Kontratlar` → İstediğiniz kontratı açın → `Odalar` tabına gidin
   - Veya direkt: `/admin/contracts/1/rooms`

2. **Oda listesinde "Fiyat Hesapla" butonuna tıklayın** (hesap makinesi ikonu)
   - Otomatik olarak test sayfasına yönlendirilir
   - Oda seçili gelir

3. **Tarih aralığını girin**
   - Giriş tarihi: Örn. `2025-04-07`
   - Çıkış tarihi: Örn. `2025-04-11`
   - Para birimi: `TRY` (veya başka bir para birimi)
   - Misafir sayısı: `1`

4. **"Fiyat Hesapla" butonuna basın**

5. **Sonuçları inceleyin**
   - Toplam fiyat
   - Gece gece detay
   - Para birimi dönüşümleri
   - Kullanılan periyotlar

### Seçenek 2: Direkt Test Sayfasından

1. **Test sayfasına gidin**
   ```
   http://localhost/admin/test-pricing
   ```

2. **Oda seçin**
   - Dropdown'dan periyot tanımlı bir oda seçin
   - Periyot sayısını göreceksiniz

3. **Tarih ve parametreleri girin**
   - Giriş: `2025-04-07`
   - Çıkış: `2025-04-11`
   - Para birimi: `TRY`
   - Misafir: `1`

4. **"Fiyat Hesapla" butonuna basın**

---

## 📊 Beklenen Sonuçlar

### Test Verileri
- **01-10 Nisan**: TRY, 12000 TL/gece
- **11-20 Nisan**: EUR, 1200 EUR/gece
- **21-30 Nisan**: TRY, 12000 TL/gece

### Senaryo: 7-11 Nisan, TRY Para Birimi

**Giriş:** 07 Nisan 2025  
**Çıkış:** 11 Nisan 2025  
**Toplam:** 4 gece

**Beklenen Sonuç:**

| Gece | Tarih | Periyot | Para Birimi | Satış Fiyatı | TRY'ye Çevrilmiş |
|------|-------|---------|-------------|--------------|------------------|
| 1 | 07 Nisan | TRY | TRY | 12000 | 12000 TRY |
| 2 | 08 Nisan | TRY | TRY | 12000 | 12000 TRY |
| 3 | 09 Nisan | TRY | TRY | 12000 | 12000 TRY |
| 4 | 10 Nisan | TRY | TRY | 12000 | 12000 TRY |

**Toplam:** 48000 TRY + Servis Bedeli

### Senaryo: 7-15 Nisan, TRY Para Birimi

**Giriş:** 07 Nisan 2025  
**Çıkış:** 15 Nisan 2025  
**Toplam:** 8 gece

**Beklenen Sonuç:**

| Gece | Tarih | Periyot | Para Birimi | Satış Fiyatı | TRY'ye Çevrilmiş |
|------|-------|---------|-------------|--------------|------------------|
| 1-4 | 07-10 Nisan | TRY | TRY | 12000/gece | 48000 TRY |
| 5-8 | 11-14 Nisan | EUR | EUR | 1200/gece | 170400 TRY (1200 × 35.5 × 4) |

**Toplam:** 218400 TRY + Servis Bedeli

---

## 🎨 Ekran Görüntüleri

### 1. Kontrat Odaları Sayfası
```
[Oda Listesi]
- Butonlar: 🧮 Fiyat Hesapla | 📅 Periyotlar | ✏️ Düzenle | 🗑️ Sil
```

### 2. Fiyat Hesaplama Sayfası
```
[Sol Panel]
- Oda seçimi
- Giriş/Çıkış tarihleri
- Para birimi seçimi
- Misafir sayısı
- [Fiyat Hesapla] butonu

[Sağ Panel]
- Oda periyotları listesi
- Döviz kurları listesi

[Sonuç]
- Gece sayısı
- Toplam fiyat
- Gece gece breakdown
- Para birimi dönüşümleri
```

---

## ✅ Başarı Kriterleri

Test başarılı sayılır eğer:
- ✅ Farklı tarih aralıkları için farklı periyotlar görüntüleniyor
- ✅ Para birimleri doğru dönüştürülüyor
- ✅ Toplam fiyat doğru hesaplanıyor
- ✅ Her gece için detaylı breakdown gösteriliyor
- ✅ TRY, EUR, USD gibi farklı para birimleri test edilebiliyor

---

## 🔧 Sorun Giderme

### "Döviz kuru bulunamadı" Hatası
**Çözüm:**
```bash
php artisan db:seed --class=ExchangeRateSeeder
```

### "Periyot bulunamadı" Uyarısı
**Çözüm:** Odada periyot tanımlanmamış, varsayılan fiyat kullanılacak

### Butonlar Görünmüyor
**Çözüm:** Admin yetkinizin olduğundan ve giriş yaptığınızdan emin olun

---

## 📝 Notlar

- Test sayfası sadece görüntüleme amaçlıdır
- Asıl rezervasyon sırasında otomatik olarak bu hesaplama yapılır
- Periyot yoksa sistem varsayılan fiyatı kullanır
- Tarih aralığı periyotlardan büyükse, periyot dışındaki günler varsayılan fiyatla hesaplanır
