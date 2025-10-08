@extends('layouts.admin')

@section('title', 'Kontrat Düzenle')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kontrat Düzenle</h2>
    <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Geri Dön
    </a>
</div>

<!-- Debug: Validation Hataları -->
@if ($errors->any())
    <div class="alert alert-danger">
        <h5>Validation Hataları:</h5>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Debug: Flash Mesajları -->
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.contracts.update', $contract) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hotel_id" class="form-label">Otel *</label>
                                <select class="form-select @error('hotel_id') is-invalid @enderror" 
                                        id="hotel_id" name="hotel_id" required>
                                    <option value="">Otel Seçiniz</option>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel->id }}" 
                                                {{ old('hotel_id', $contract->hotel_id) == $hotel->id ? 'selected' : '' }}>
                                            {{ $hotel->name }} - {{ $hotel->city }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('hotel_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="firm_id" class="form-label">Firma</label>
                                <select class="form-select @error('firm_id') is-invalid @enderror" 
                                        id="firm_id" name="firm_id">
                                    <option value="" {{ old('firm_id', $contract->firm_id) == null ? 'selected' : '' }}>
                                        🌍 Genel Kontrat (Tüm Firmalar)
                                    </option>
                                    @foreach($firms as $firm)
                                        <option value="{{ $firm->id }}" 
                                                {{ old('firm_id', $contract->firm_id) == $firm->id ? 'selected' : '' }}>
                                            {{ $firm->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Boş bırakırsanız tüm firmalara geçerli genel kontrat olur
                                </small>
                                @error('firm_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Başlangıç Tarihi *</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" 
                                       value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Bitiş Tarihi *</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" 
                                       value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Para Birimi *</label>
                                <select class="form-select @error('currency') is-invalid @enderror" 
                                        id="currency" name="currency" required>
                                    <option value="TRY" {{ old('currency', $contract->currency) == 'TRY' ? 'selected' : '' }}>TRY (₺)</option>
                                    <option value="USD" {{ old('currency', $contract->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ old('currency', $contract->currency) == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ old('currency', $contract->currency) == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="base_price" class="form-label">Temel Fiyat *</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('base_price') is-invalid @enderror" 
                                       id="base_price" name="base_price" 
                                       value="{{ old('base_price', $contract->base_price) }}" required>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="commission_rate" class="form-label">Komisyon Oranı (%) *</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control @error('commission_rate') is-invalid @enderror" 
                                       id="commission_rate" name="commission_rate" 
                                       value="{{ old('commission_rate', $contract->commission_rate) }}" required>
                                @error('commission_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="payment_terms" class="form-label">Ödeme Koşulları</label>
                                <input type="text" class="form-control @error('payment_terms') is-invalid @enderror" 
                                       id="payment_terms" name="payment_terms" 
                                       value="{{ old('payment_terms', $contract->payment_terms) }}" 
                                       placeholder="Örn: 30 gün vadeli">
                                @error('payment_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $contract->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           {{ old('is_active', $contract->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Aktif Kontrat
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="auto_renewal" name="auto_renewal" 
                                           {{ old('auto_renewal', $contract->auto_renewal) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_renewal">
                                        Otomatik Yenileme
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Mevcut Kontrat Bilgileri -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Mevcut Bilgiler</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Kontrat ID:</small><br>
                    <strong>#{{ $contract->id }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Oluşturulma:</small><br>
                    <strong>{{ $contract->created_at->format('d.m.Y H:i') }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Son Güncelleme:</small><br>
                    <strong>{{ $contract->updated_at->format('d.m.Y H:i') }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Durum:</small><br>
                    <span class="badge {{ $contract->getStatusBadge() }}">
                        @if($contract->isExpired())
                            <i class="fas fa-times"></i> Süresi Dolmuş
                        @elseif($contract->isExpiringSoon())
                            <i class="fas fa-clock"></i> Yakında Dolacak
                        @else
                            <i class="fas fa-check"></i> Aktif
                        @endif
                    </span>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Oda Sayısı:</small><br>
                    <strong>{{ $contract->rooms->count() }} Oda</strong>
                </div>
            </div>
        </div>

        <!-- Fiyat Hesaplama -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Fiyat Hesaplama</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Temel Fiyat:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="display_base_price" readonly>
                        <span class="input-group-text" id="currency_symbol">{{ $contract->currency }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Komisyon:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="display_commission" readonly>
                        <span class="input-group-text" id="currency_symbol2">{{ $contract->currency }}</span>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label fw-bold">Toplam Satış Fiyatı:</label>
                    <div class="input-group">
                        <input type="text" class="form-control fw-bold text-success" id="display_sale_price" readonly>
                        <span class="input-group-text" id="currency_symbol4">{{ $contract->currency }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Uyarılar -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Uyarılar</h6>
            </div>
            <div class="card-body">
                @if($contract->isExpired())
                    <div class="alert alert-danger">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i>
                            Bu kontratın süresi dolmuş. Yeni tarih belirleyin.
                        </small>
                    </div>
                @elseif($contract->isExpiringSoon())
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-clock"></i>
                            Bu kontrat {{ $contract->getRemainingDays() }} gün içinde dolacak.
                        </small>
                    </div>
                @endif
                
                <div class="alert alert-info">
                    <small>
                        <i class="fas fa-info-circle"></i>
                        Kontrat güncellendiğinde mevcut odalar etkilenmez.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const basePriceInput = document.getElementById('base_price');
    const commissionInput = document.getElementById('commission_rate');
    const currencySelect = document.getElementById('currency');
    
    const displayBasePrice = document.getElementById('display_base_price');
    const displayCommission = document.getElementById('display_commission');
    const displaySalePrice = document.getElementById('display_sale_price');
    
    // Para birimi sembolleri
    const currencySymbols = {
        'TRY': '₺',
        'USD': '$',
        'EUR': '€',
        'GBP': '£'
    };
    
    function updateCurrencySymbols() {
        const currency = currencySelect.value;
        const symbol = currencySymbols[currency] || currency;
        
        document.querySelectorAll('[id^="currency_symbol"]').forEach(el => {
            el.textContent = symbol;
        });
    }
    
    function calculatePrices() {
        const basePrice = parseFloat(basePriceInput.value) || 0;
        const commissionRate = parseFloat(commissionInput.value) || 0;
        
        const commission = (basePrice * commissionRate / 100);
        const salePrice = basePrice + commission;
        
        displayBasePrice.value = basePrice.toFixed(2);
        displayCommission.value = commission.toFixed(2);
        displaySalePrice.value = salePrice.toFixed(2);
    }
    
    // Event listeners
    basePriceInput.addEventListener('input', calculatePrices);
    commissionInput.addEventListener('input', calculatePrices);
    currencySelect.addEventListener('change', updateCurrencySymbols);
    
    // Tarih validasyonu
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
    });
    
    endDate.addEventListener('change', function() {
        if (this.value <= startDate.value) {
            this.setCustomValidity('Bitiş tarihi başlangıç tarihinden sonra olmalıdır');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // İlk hesaplama
    calculatePrices();
    updateCurrencySymbols();
});
</script>
@endpush 