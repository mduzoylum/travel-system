# 🏨 Travel System - Laravel DDD

Modern seyahat yönetim sistemi, Domain-Driven Design (DDD) mimarisi ile geliştirilmiştir.

## 🚀 Özellikler

### 📋 Modüller
- **Tedarikçi Modülü**: API entegrasyonu, rezervasyon yönetimi
- **Kontrat Modülü**: Otel kontratları, oda yönetimi, fiyatlandırma
- **Onay Akışı Modülü**: Çok aşamalı onay süreçleri
- **Karlandırma Modülü**: Komisyon hesaplamaları, kar analizi
- **Kredi Sistemi**: Kurumsal müşteri kredi yönetimi
- **Firma Yönetimi**: Müşteri firmaları ve kullanıcı yönetimi

### 🏗️ Teknik Özellikler
- **Laravel 11** framework
- **Domain-Driven Design (DDD)** mimarisi
- **Bootstrap 5** UI framework
- **MySQL** veritabanı
- **RESTful API** hazırlığı
- **Authentication & Authorization**

## 🛠️ Kurulum

### Gereksinimler
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM

### Adımlar

1. **Repository'yi klonlayın**
```bash
git clone https://github.com/KULLANICI_ADINIZ/travel-system.git
cd travel-system
```

2. **Bağımlılıkları yükleyin**
```bash
composer install
npm install
```

3. **Environment dosyasını oluşturun**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Veritabanı ayarlarını yapın**
```bash
# .env dosyasında veritabanı bilgilerini güncelleyin
php artisan migrate
php artisan db:seed
```

5. **Storage linkini oluşturun**
```bash
php artisan storage:link
```

6. **Uygulamayı başlatın**
```bash
php artisan serve
```

## 📁 Proje Yapısı

```
app/
├── DDD/
│   └── Modules/
│       ├── Approval/          # Onay akışı modülü
│       ├── Contract/          # Kontrat modülü
│       ├── Credit/            # Kredi modülü
│       ├── Firm/              # Firma modülü
│       ├── Profit/            # Karlandırma modülü
│       ├── Reservation/       # Rezervasyon modülü
│       └── Supplier/          # Tedarikçi modülü
├── Http/
│   ├── Controllers/
│   │   └── Admin/            # Admin paneli controller'ları
│   └── Middleware/
└── Models/
```

## 🔐 Varsayılan Kullanıcılar

Sistem kurulumu sonrası aşağıdaki kullanıcılar oluşturulur:

- **Admin**: `admin@example.com` / `password`
- **User**: `user@example.com` / `password`

## 📊 Veritabanı Şeması

Sistem aşağıdaki ana tabloları içerir:

- `users` - Kullanıcılar
- `firms` - Müşteri firmaları
- `hotels` - Oteller
- `contracts` - Kontratlar
- `contract_rooms` - Kontrat odaları
- `suppliers` - Tedarikçiler
- `credit_accounts` - Kredi hesapları
- `approval_scenarios` - Onay senaryoları
- `profit_rules` - Kar kuralları

## 🎯 Kullanım

### Admin Paneli
- **URL**: `http://localhost:8000/admin`
- **Özellikler**: Tüm modüllerin yönetimi

### API Endpoints
- Rezervasyon API'leri (geliştirme aşamasında)
- Tedarikçi entegrasyonları
- Onay akışı webhook'ları

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 📞 İletişim

- **Geliştirici**: [Adınız]
- **Email**: [email@example.com]
- **Proje Linki**: [https://github.com/KULLANICI_ADINIZ/travel-system](https://github.com/KULLANICI_ADINIZ/travel-system)
