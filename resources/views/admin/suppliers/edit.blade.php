@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tedarikçi Düzenle</h2>
    <div>
        <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> Görüntüle
        </a>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Temel Bilgiler -->
            <h5 class="mb-3"><i class="fas fa-building"></i> Temel Bilgiler</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tedarikçi Adı *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $supplier->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                @if(auth()->user()->isAdmin())
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="group_id" class="form-label">Grup</label>
                            <select class="form-select @error('group_id') is-invalid @enderror" 
                                    id="group_id" name="group_id">
                                <option value="">Grup Seçiniz (Opsiyonel)</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" 
                                            {{ old('group_id', $supplier->group_id) == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Raporlama için tedarikçi grubu</small>
                            @error('group_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endif
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="accounting_code" class="form-label">Muhasebe Kodu</label>
                        <input type="text" class="form-control @error('accounting_code') is-invalid @enderror" 
                               id="accounting_code" name="accounting_code" value="{{ old('accounting_code', $supplier->accounting_code) }}" 
                               placeholder="örn: TED001">
                        @error('accounting_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="country" class="form-label">Ülke</label>
                        <input type="text" class="form-control @error('country') is-invalid @enderror" 
                               id="country" name="country" value="{{ old('country', $supplier->country) }}" 
                               placeholder="örn: Türkiye">
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="city" class="form-label">Şehir</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                               id="city" name="city" value="{{ old('city', $supplier->city) }}" 
                               placeholder="örn: İstanbul">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="types" class="form-label">Tedarikçi Türleri *</label>
                <div class="form-check-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="type_hotel" name="types[]" value="hotel"
                               {{ in_array('hotel', old('types', $supplier->types ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_hotel">Otel</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="type_flight" name="types[]" value="flight"
                               {{ in_array('flight', old('types', $supplier->types ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_flight">Uçuş</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="type_car" name="types[]" value="car"
                               {{ in_array('car', old('types', $supplier->types ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_car">Araç Kiralama</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="type_activity" name="types[]" value="activity"
                               {{ in_array('activity', old('types', $supplier->types ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_activity">Aktivite</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="type_transfer" name="types[]" value="transfer"
                               {{ in_array('transfer', old('types', $supplier->types ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_transfer">Transfer</label>
                    </div>
                </div>
                @error('types')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('types.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $supplier->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Ödeme Bilgileri -->
            <hr class="my-4">
            <h5 class="mb-3"><i class="fas fa-credit-card"></i> Ödeme Bilgileri</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_type" class="form-label">Ödeme Tipi</label>
                        <select class="form-select @error('payment_type') is-invalid @enderror" 
                                id="payment_type" name="payment_type">
                            <option value="cari" {{ old('payment_type', $supplier->payment_type) == 'cari' ? 'selected' : '' }}>Cari</option>
                            <option value="credit_card" {{ old('payment_type', $supplier->payment_type) == 'credit_card' ? 'selected' : '' }}>Kredi Kartı</option>
                        </select>
                        @error('payment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tax_rate" class="form-label">Vergi Oranı (%)</label>
                        <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" 
                               id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $supplier->tax_rate ?? 0) }}" 
                               min="0" max="100" step="0.01" placeholder="0.00">
                        @error('tax_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Ödeme Periyodları</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_period_type" id="payment_days" 
                                   value="days" {{ old('payment_period_type', $supplier->payment_periods['type'] ?? '') == 'days' ? 'checked' : '' }}>
                            <label class="form-check-label" for="payment_days">
                                Gün Sayısı Olarak
                            </label>
                        </div>
                        <div id="days_period" style="display: none;">
                            <div class="row mt-2">
                                <div class="col-6">
                                    <label for="payment_days_before" class="form-label small">Rezervasyon Öncesi (Gün)</label>
                                    <input type="number" class="form-control form-control-sm" 
                                           id="payment_days_before" name="payment_days_before" 
                                           value="{{ old('payment_days_before', $supplier->payment_periods['before_booking'] ?? 0) }}" min="0">
                                </div>
                                <div class="col-6">
                                    <label for="payment_days_after" class="form-label small">Rezervasyon Sonrası (Gün)</label>
                                    <input type="number" class="form-control form-control-sm" 
                                           id="payment_days_after" name="payment_days_after" 
                                           value="{{ old('payment_days_after', $supplier->payment_periods['after_booking'] ?? 0) }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_period_type" id="payment_monthly" 
                                   value="monthly" {{ old('payment_period_type', $supplier->payment_periods['type'] ?? '') == 'monthly' ? 'checked' : '' }}>
                            <label class="form-check-label" for="payment_monthly">
                                Ayın Belirli Günleri
                            </label>
                        </div>
                        <div id="monthly_period" style="display: none;">
                            <div class="mt-2">
                                <label class="form-label small">Ayın Hangi Günleri</label>
                                <div class="row">
                                    @for($i = 1; $i <= 31; $i++)
                                    <div class="col-2 mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="payment_monthly_days[]" value="{{ $i }}" 
                                                   id="day_{{ $i }}"
                                                   {{ in_array($i, old('payment_monthly_days', $supplier->payment_periods['days'] ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="day_{{ $i }}">{{ $i }}</label>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- İletişim Bilgileri -->
            <hr class="my-4">
            <h5 class="mb-3"><i class="fas fa-phone"></i> İletişim Bilgileri</h5>
            
            <div class="mb-3">
                <label class="form-label">İletişim Yetkilileri</label>
                <div id="contact-persons">
                    @if($supplier->contact_persons && count($supplier->contact_persons) > 0)
                        @foreach($supplier->contact_persons as $index => $person)
                        <div class="contact-person row mb-2">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="contact_names[]" 
                                       value="{{ old('contact_names.'.$index, $person['name'] ?? '') }}" placeholder="Ad Soyad">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="contact_phones[]" 
                                       value="{{ old('contact_phones.'.$index, $person['phone'] ?? '') }}" placeholder="Telefon">
                            </div>
                            <div class="col-md-4">
                                <input type="email" class="form-control" name="contact_emails[]" 
                                       value="{{ old('contact_emails.'.$index, $person['email'] ?? '') }}" placeholder="E-posta">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeContactPerson(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="contact-person row mb-2">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="contact_names[]" placeholder="Ad Soyad">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="contact_phones[]" placeholder="Telefon">
                            </div>
                            <div class="col-md-4">
                                <input type="email" class="form-control" name="contact_emails[]" placeholder="E-posta">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeContactPerson(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addContactPerson()">
                    <i class="fas fa-plus"></i> Yetkili Ekle
                </button>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Tedarikçi E-postaları</label>
                <div id="supplier-emails">
                    @if($supplier->emails && count($supplier->emails) > 0)
                        @foreach($supplier->emails as $index => $email)
                        <div class="supplier-email row mb-2">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="supplier_email_names[]" 
                                       value="{{ old('supplier_email_names.'.$index, $email['name'] ?? '') }}" placeholder="Ad Soyad">
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="supplier_emails[]" 
                                       value="{{ old('supplier_emails.'.$index, $email['email'] ?? '') }}" placeholder="E-posta">
                            </div>
                            <div class="col-md-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="supplier_email_primary[]" value="1"
                                           {{ old('supplier_email_primary.'.$index, $email['is_primary'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label small">Ana</label>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSupplierEmail(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="supplier-email row mb-2">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="supplier_email_names[]" placeholder="Ad Soyad">
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="supplier_emails[]" placeholder="E-posta">
                            </div>
                            <div class="col-md-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="supplier_email_primary[]" value="1">
                                    <label class="form-check-label small">Ana</label>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSupplierEmail(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSupplierEmail()">
                    <i class="fas fa-plus"></i> E-posta Ekle
                </button>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Adres</label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="3" placeholder="Tedarikçi adresi">{{ old('address', $supplier->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Logo -->
            <div class="mb-3">
                <label for="logo" class="form-label">Tedarikçi Logosu</label>
                @if($supplier->logo)
                    <div class="mb-2">
                        <img src="{{ Storage::url($supplier->logo) }}" alt="Mevcut Logo" class="img-thumbnail" style="max-width: 100px;">
                        <div class="form-text">Mevcut logo</div>
                    </div>
                @endif
                <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                       id="logo" name="logo" accept="image/*">
                <div class="form-text">Maksimum 5MB, JPG, PNG, GIF formatları desteklenir.</div>
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            @if(auth()->user()->isAdmin())
            <!-- API Entegrasyon Ayarları -->
            <hr class="my-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>API Entegrasyon Ayarları</strong> - Bu alanlar sadece admin kullanıcılar tarafından düzenlenebilir.
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="api_endpoint" class="form-label">API URL</label>
                        <input type="url" class="form-control @error('api_endpoint') is-invalid @enderror" 
                               id="api_endpoint" name="api_endpoint" value="{{ old('api_endpoint', $supplier->api_endpoint) }}" 
                               placeholder="https://api.example.com">
                        @error('api_endpoint')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="api_version" class="form-label">API Versiyonu</label>
                        <input type="text" class="form-control @error('api_version') is-invalid @enderror" 
                               id="api_version" name="api_version" value="{{ old('api_version', $supplier->api_version) }}" 
                               placeholder="v1.0">
                        @error('api_version')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="api_username" class="form-label">API Kullanıcı Adı</label>
                        <input type="text" class="form-control @error('api_username') is-invalid @enderror" 
                               id="api_username" name="api_username" value="{{ old('api_username', $supplier->api_credentials['username'] ?? '') }}">
                        @error('api_username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="api_password" class="form-label">API Şifresi</label>
                        <input type="password" class="form-control @error('api_password') is-invalid @enderror" 
                               id="api_password" name="api_password" placeholder="Değiştirmek için yeni şifre girin">
                        @error('api_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="api_key" class="form-label">API Anahtarı</label>
                        <input type="text" class="form-control @error('api_key') is-invalid @enderror" 
                               id="api_key" name="api_key" value="{{ old('api_key', $supplier->api_credentials['api_key'] ?? '') }}">
                        @error('api_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sync_frequency" class="form-label">Senkronizasyon Sıklığı</label>
                        <select class="form-select @error('sync_frequency') is-invalid @enderror" 
                                id="sync_frequency" name="sync_frequency">
                            <option value="">Seçiniz</option>
                            <option value="hourly" {{ old('sync_frequency', $supplier->sync_frequency) == 'hourly' ? 'selected' : '' }}>Saatlik</option>
                            <option value="daily" {{ old('sync_frequency', $supplier->sync_frequency) == 'daily' ? 'selected' : '' }}>Günlük</option>
                            <option value="weekly" {{ old('sync_frequency', $supplier->sync_frequency) == 'weekly' ? 'selected' : '' }}>Haftalık</option>
                            <option value="monthly" {{ old('sync_frequency', $supplier->sync_frequency) == 'monthly' ? 'selected' : '' }}>Aylık</option>
                        </select>
                        @error('sync_frequency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Durum Ayarları -->
            <hr class="my-4">
            <h5 class="mb-3"><i class="fas fa-cog"></i> Durum Ayarları</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                               {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Tedarikçi aktif
                        </label>
                    </div>
                </div>
                
                @if(auth()->user()->isAdmin())
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sync_enabled" name="sync_enabled" 
                               {{ old('sync_enabled', $supplier->sync_enabled) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sync_enabled">
                            Senkronizasyon aktif
                        </label>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Ödeme periyodu seçimi
document.querySelectorAll('input[name="payment_period_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('days_period').style.display = this.value === 'days' ? 'block' : 'none';
        document.getElementById('monthly_period').style.display = this.value === 'monthly' ? 'block' : 'none';
    });
});

// Sayfa yüklendiğinde mevcut seçimi göster
document.addEventListener('DOMContentLoaded', function() {
    const selectedType = document.querySelector('input[name="payment_period_type"]:checked');
    if (selectedType) {
        selectedType.dispatchEvent(new Event('change'));
    }
});

// İletişim kişisi ekleme
function addContactPerson() {
    const container = document.getElementById('contact-persons');
    const newPerson = document.createElement('div');
    newPerson.className = 'contact-person row mb-2';
    newPerson.innerHTML = `
        <div class="col-md-4">
            <input type="text" class="form-control" name="contact_names[]" placeholder="Ad Soyad">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="contact_phones[]" placeholder="Telefon">
        </div>
        <div class="col-md-4">
            <input type="email" class="form-control" name="contact_emails[]" placeholder="E-posta">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeContactPerson(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newPerson);
}

function removeContactPerson(button) {
    button.closest('.contact-person').remove();
}

// Tedarikçi e-postası ekleme
function addSupplierEmail() {
    const container = document.getElementById('supplier-emails');
    const newEmail = document.createElement('div');
    newEmail.className = 'supplier-email row mb-2';
    newEmail.innerHTML = `
        <div class="col-md-4">
            <input type="text" class="form-control" name="supplier_email_names[]" placeholder="Ad Soyad">
        </div>
        <div class="col-md-6">
            <input type="email" class="form-control" name="supplier_emails[]" placeholder="E-posta">
        </div>
        <div class="col-md-1">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="supplier_email_primary[]" value="1">
                <label class="form-check-label small">Ana</label>
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSupplierEmail(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newEmail);
}

function removeSupplierEmail(button) {
    button.closest('.supplier-email').remove();
}
</script>
@endpush