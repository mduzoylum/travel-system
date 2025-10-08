# Kontrat Sistemi Değişiklikleri - Özet

## Yapılan Değişiklikler

### 1. Controller Güncellemeleri
**Dosya:** `app/Http/Controllers/Admin/ContractController.php`

✅ **store() metodu:**
- `firm_id` validasyonu `nullable` yapıldı
- Boş veya 'general' değeri gelirse `null` olarak kaydediliyor
- Aynı otel için genel/firmaya özel kontrat kontrolü ayrıştırıldı
- Hata mesajları firma tipine göre özelleştirildi

✅ **update() metodu:**
- Aynı değişiklikler update metodunda da uygulandı

### 2. View Güncellemeleri

#### `resources/views/admin/contracts/create.blade.php`
✅ Firma alanı:
- `required` attribute'u kaldırıldı
- "🌍 Genel Kontrat (Tüm Firmalar)" seçeneği eklendi
- Açıklayıcı yardım metni eklendi

#### `resources/views/admin/contracts/edit.blade.php`
✅ Aynı değişiklikler edit sayfasında da uygulandı

#### `resources/views/admin/contracts/show.blade.php`
✅ Firma bilgisi gösterimi:
- `firm_id` null ise "🌍 Genel Kontrat" olarak gösteriliyor
- "Tüm firmalara geçerli" açıklaması eklendi

#### `resources/views/admin/contracts/index.blade.php`
✅ Kontrat listesi:
- Genel kontratlar "🌍 Genel Kontrat" olarak gösteriliyor

### 3. Yeni Servisler

#### `app/DDD/Modules/Contract/Services/ContractSelectionService.php` (YENİ)

Kontrat seçim mantığını yöneten ana servis:

```php
// Kullanıcı için uygun kontratı getir (firmaya özel > genel)
public function getContractForFirm($hotel, $firm = null, ?string $date = null): ?Contract

// Kullanıcı için kontrat seç
public function getContractForUser($hotel, $user, ?string $date = null): ?Contract

// Birden fazla otel için kontrat al
public function getContractsForHotels(array $hotelIds, ?int $firmId = null): array

// Bir otel için tüm kontratları getir
public function getAllContractsForHotel(int $hotelId, ?string $date = null): array

// Kontrat geçerliliğini kontrol et
public function isContractValid(Contract $contract, ?string $date = null): bool

// İki kontrat arasında önceliklendirme yap
public function prioritizeContracts(?Contract $contract1, ?Contract $contract2): ?Contract
```

### 4. ReservationService Güncellemeleri
**Dosya:** `app/DDD/Modules/Reservation/Services/ReservationService.php`

✅ Yeni metodlar eklendi:

```php
// Kullanıcı için müsait odaları getir (kontrat seçimi dahil)
public function getAvailableRoomsForUser(User $user, $hotel, string $checkinDate, string $checkoutDate): array

// Birden fazla otelde arama yap
public function searchHotelsForUser(User $user, array $hotelIds, string $checkinDate, string $checkoutDate): array

// Firma için özel kontratı olan otelleri listele
public function getFirmSpecificHotels(int $firmId, ?string $date = null): array

// Admin için kontrat öncelik durumunu göster
public function getContractPriorityStatus(int $hotelId, ?string $date = null): array
```

### 5. Contract Model Güncellemeleri
**Dosya:** `app/DDD/Modules/Contract/Models/Contract.php`

✅ Yeni helper metodlar:

```php
// Kontrat tipi kontrolü
public function isGeneralContract(): bool
public function isFirmSpecific(): bool
public function getContractType(): string
public function getDisplayName(): string

// Query Scope'lar
public function scopeGeneral($query)
public function scopeFirmSpecific($query, ?int $firmId = null)
public function scopeActiveAndValid($query, ?string $date = null)
```

### 6. Dokümantasyon
**Dosyalar:**
- `KONTRAT_ONCELIKLENDIRME.md` - Detaylı kullanım kılavuzu
- `DEGISIKLIK_OZETI.md` - Bu dosya

## Nasıl Çalışır?

### Kontrat Öncelik Sırası

```
1. Firmaya Özel Kontrat (aktif ve geçerli tarihli)
   ↓
2. Genel Kontrat (aktif ve geçerli tarihli)
   ↓
3. Kontrat bulunamadı
```

### Örnek Kullanım

#### 1. Genel Kontrat Oluşturma
Admin panelinde `/admin/contracts/create` sayfasında:
- Firma alanını boş bırakın veya "🌍 Genel Kontrat" seçin
- Diğer alanları doldurun
- Kaydet

#### 2. Firmaya Özel Kontrat Oluşturma
- Firma alanından belirli bir firma seçin
- Diğer alanları doldurun
- Kaydet

#### 3. Kod İçinde Kullanım

```php
// Servis örneği
$service = app(ContractSelectionService::class);

// Kullanıcı için kontrat al
$contract = $service->getContractForUser($hotelId, $user);

// Rezervasyon servisi ile oda ara
$reservationService = app(ReservationService::class);
$rooms = $reservationService->getAvailableRoomsForUser($user, $hotelId, '2025-06-01', '2025-06-05');
```

## Avantajları

✅ **Esneklik**: Hem genel hem firmaya özel fiyatlandırma
✅ **Önceliklendirme**: Firmaya özel kontratlar otomatik öncelikli
✅ **Tarih Kontrolü**: Süresi dolan firmaya özel kontratlar otomatik genel kontrata düşer
✅ **Kolay Yönetim**: Admin panelinden tek tıkla genel kontrat oluşturma
✅ **Genişletilebilir**: Yeni özellikler eklemek kolay

## Test Edilmesi Gerekenler

- [ ] Genel kontrat oluşturma
- [ ] Firmaya özel kontrat oluşturma
- [ ] Aynı otelde hem genel hem firmaya özel kontrat
- [ ] Firmaya özel kontrat tarihi dolunca genel kontrata düşme
- [ ] Kontrat listeleme sayfasında "Genel Kontrat" gösterimi
- [ ] Kontrat detay sayfasında firma bilgisi
- [ ] Kontrat güncelleme (genel ↔ firmaya özel değişim)
- [ ] İki genel kontrat oluşturma denemesi (hata vermeli)
- [ ] İki firmaya özel kontrat (aynı firma) oluşturma denemesi (hata vermeli)

## Veritabanı Değişikliği

Mevcut migration zaten var:
```
database/migrations/2025_08_03_201252_add_firm_id_to_contracts_table.php
```

`firm_id` alanı zaten nullable olarak tanımlı, ek migration gerekmez.

## Geriye Dönük Uyumluluk

✅ Mevcut kontratlar etkilenmez
✅ Tüm mevcut kontratlar firmaya özel olarak devam eder
✅ API değişikliği yok (sadece ek metodlar)
✅ Mevcut rezervasyon akışı çalışmaya devam eder

## Gelecek İyileştirmeler (Opsiyonel)

1. **Cache Mekanizması**: Kontrat sorguları cache'lenebilir
2. **API Endpoint'leri**: REST API için endpoint'ler eklenebilir
3. **Toplu İşlemler**: Birden fazla kontratı toplu aktif/pasif etme
4. **Raporlama**: Genel vs firmaya özel kontrat karşılaştırma raporları
5. **Bildirimler**: Firmaya özel kontrat dolmadan önce bildirim

## Destek

Sorularınız için: `KONTRAT_ONCELIKLENDIRME.md` dosyasına bakın.

