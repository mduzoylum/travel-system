# Kontrat Önceliklendirme Sistemi

## Genel Bakış

Bu sistem, oteller için hem **genel kontratlar** (tüm firmalara geçerli) hem de **firmaya özel kontratlar** oluşturulmasına olanak tanır.

### Kontrat Tipleri

1. **Genel Kontrat** (🌍)
   - `firm_id` = `NULL`
   - Tüm firmalara geçerli
   - Firmaya özel kontrat yoksa kullanılır

2. **Firmaya Özel Kontrat**
   - `firm_id` = belirli bir firma ID'si
   - Sadece o firmaya geçerli
   - Her zaman genel kontratın önündedir

## Öncelik Mantığı

Bir kullanıcı otel araması yaptığında sistem şu sırayı izler:

```
1. Kullanıcının firmasına özel AKTIF ve GEÇERLİ tarihli kontrat var mı?
   ├─ EVET → Bu kontratı kullan
   └─ HAYIR → Adım 2'ye geç

2. Genel (firm_id = NULL) AKTIF ve GEÇERLİ tarihli kontrat var mı?
   ├─ EVET → Bu kontratı kullan
   └─ HAYIR → Bu otel için fiyat gösterme
```

### Örnek Senaryolar

#### Senaryo 1: Firmaya Özel + Genel Kontrat Var
- Otel: Grand Hotel Istanbul
- Genel Kontrat: 1000 TL/gece
- Firma A Özel Kontrat: 850 TL/gece

**Sonuç:**
- Firma A kullanıcıları → 850 TL görür (firmaya özel)
- Firma B kullanıcıları → 1000 TL görür (genel)
- Firma C kullanıcıları → 1000 TL görür (genel)

#### Senaryo 2: Firmaya Özel Kontrat Süresi Doldu
- Otel: Beach Resort Antalya
- Genel Kontrat: 2000 TL/gece (Aktif: 01.01.2025 - 31.12.2025)
- Firma A Özel Kontrat: 1700 TL/gece (Dolmuş: 01.01.2025 - 31.05.2025)

**Sonuç (1 Haziran 2025):**
- Firma A kullanıcıları → 2000 TL görür (özel kontrat dolmuş, genel geçerli)
- Firma B kullanıcıları → 2000 TL görür (genel)

#### Senaryo 3: Sadece Genel Kontrat
- Otel: City Hotel Ankara
- Genel Kontrat: 1500 TL/gece

**Sonuç:**
- Tüm firmalar → 1500 TL görür

## Kullanım

### 1. Genel Kontrat Oluşturma

Admin panelinde kontrat oluştururken **Firma** alanını **boş bırakın** veya "🌍 Genel Kontrat (Tüm Firmalar)" seçeneğini seçin.

```php
Contract::create([
    'hotel_id' => 1,
    'firm_id' => null, // Genel kontrat
    'start_date' => '2025-01-01',
    'end_date' => '2025-12-31',
    'currency' => 'TRY',
    'is_active' => true,
    // ... diğer alanlar
]);
```

### 2. Firmaya Özel Kontrat Oluşturma

Firma alanından belirli bir firma seçin.

```php
Contract::create([
    'hotel_id' => 1,
    'firm_id' => 5, // Belirli bir firma
    'start_date' => '2025-01-01',
    'end_date' => '2025-12-31',
    'currency' => 'TRY',
    'is_active' => true,
    // ... diğer alanlar
]);
```

### 3. Kod İçinde Kullanım

#### Kullanıcı için uygun kontratı al

```php
use App\DDD\Modules\Contract\Services\ContractSelectionService;

$service = app(ContractSelectionService::class);

// Belirli bir otel ve firma için
$contract = $service->getContractForFirm($hotelId, $firmId);

// Kullanıcı üzerinden
$contract = $service->getContractForUser($hotelId, $user);
```

#### Rezervasyon sürecinde kullanım

```php
use App\DDD\Modules\Reservation\Services\ReservationService;

$reservationService = app(ReservationService::class);

// Kullanıcı için müsait odaları getir
$result = $reservationService->getAvailableRoomsForUser(
    $user, 
    $hotelId, 
    '2025-06-01', 
    '2025-06-05'
);

// Sonuç:
// [
//     'contract' => Contract,
//     'rooms' => Collection of ContractRoom,
//     'hotel' => Hotel,
//     'is_firm_specific' => true/false
// ]
```

#### Bir otel için tüm kontratları göster

```php
$allContracts = $service->getAllContractsForHotel($hotelId);

// Sonuç:
// [
//     'general' => Contract|null,
//     'firms' => [
//         5 => Contract, // Firma ID 5 için özel kontrat
//         8 => Contract, // Firma ID 8 için özel kontrat
//     ]
// ]
```

## Model Metodları

### Contract Model

```php
// Kontrat tipi kontrolü
$contract->isGeneralContract();  // true/false
$contract->isFirmSpecific();     // true/false
$contract->getContractType();    // "Genel Kontrat" veya "Firmaya Özel"
$contract->getDisplayName();     // "🌍 Genel Kontrat - Hotel Name"

// Query Scope'lar
Contract::general()->get();                    // Sadece genel kontratlar
Contract::firmSpecific($firmId)->get();        // Firmaya özel kontratlar
Contract::activeAndValid('2025-06-01')->get(); // Aktif ve geçerli kontratlar
```

## Önemli Notlar

### Validasyon Kuralları

1. Aynı otel için sadece **bir genel kontrat** olabilir (aynı tarih aralığında)
2. Aynı otel ve firma için sadece **bir firmaya özel kontrat** olabilir (aynı tarih aralığında)
3. Genel kontrat + birden fazla firmaya özel kontrat aynı anda olabilir

### Performans

- Kontrat seçimi cache'lenebilir
- Toplu arama işlemlerinde `getContractsForHotels()` kullanın
- Database indeksleri: `hotel_id`, `firm_id`, `is_active`, `start_date`, `end_date`

### Güvenlik

- Kullanıcılar sadece kendi firmalarının özel kontratlarını görebilir
- Access control `AccessRuleEvaluatorService` üzerinden kontrol edilir
- Admin panelinde tüm kontratlar görüntülenebilir

## Test Senaryoları

### 1. Temel Test

```php
// Genel kontrat oluştur
$generalContract = Contract::create([
    'hotel_id' => 1,
    'firm_id' => null,
    'start_date' => now(),
    'end_date' => now()->addYear(),
    'is_active' => true,
]);

// Firmaya özel kontrat oluştur
$firmContract = Contract::create([
    'hotel_id' => 1,
    'firm_id' => 5,
    'start_date' => now(),
    'end_date' => now()->addMonths(6),
    'is_active' => true,
]);

$service = app(ContractSelectionService::class);

// Firma 5 için kontrat al
$contract = $service->getContractForFirm(1, 5);
assert($contract->id === $firmContract->id); // Firmaya özel kontrat döner

// Firma 3 için kontrat al
$contract = $service->getContractForFirm(1, 3);
assert($contract->id === $generalContract->id); // Genel kontrat döner
```

### 2. Tarih Geçerliliği Testi

```php
// Firmaya özel kontrat dolmuş
$firmContract->update(['end_date' => now()->subDay()]);

// Firma 5 için kontrat al
$contract = $service->getContractForFirm(1, 5);
assert($contract->id === $generalContract->id); // Genel kontrata düşer
```

## Veritabanı Yapısı

```sql
ALTER TABLE contracts 
ADD COLUMN firm_id BIGINT UNSIGNED NULL AFTER hotel_id,
ADD FOREIGN KEY (firm_id) REFERENCES firms(id) ON DELETE SET NULL;

CREATE INDEX idx_contracts_lookup 
ON contracts(hotel_id, firm_id, is_active, start_date, end_date);
```

## Sorun Giderme

### Problem: Kullanıcı hiçbir fiyat göremiyor

**Çözüm:**
1. Otelin aktif ve geçerli tarihli kontratı var mı kontrol edin
2. Kullanıcının firmasının erişim kurallarını kontrol edin
3. Contract'ın `is_active = true` olduğundan emin olun

### Problem: Yanlış fiyat görünüyor

**Çözüm:**
1. `ContractSelectionService::getContractForUser()` ile hangi kontratın seçildiğini debug edin
2. Firmaya özel kontratın tarih aralığını kontrol edin
3. `getAllContractsForHotel()` ile tüm kontratları listeleyin

## İlgili Dosyalar

- `app/DDD/Modules/Contract/Services/ContractSelectionService.php` - Ana kontrat seçim servisi
- `app/DDD/Modules/Reservation/Services/ReservationService.php` - Rezervasyon servisi
- `app/DDD/Modules/Contract/Models/Contract.php` - Contract model
- `app/Http/Controllers/Admin/ContractController.php` - Admin kontrat yönetimi
- `resources/views/admin/contracts/` - Kontrat view'ları

