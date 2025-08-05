@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Yeni Kontrat Ekle</h2>
    <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Geri Dön
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.contracts.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hotel_id" class="form-label">Otel *</label>
                                <select class="form-select @error('hotel_id') is-invalid @enderror" 
                                        id="hotel_id" name="hotel_id" required>
                                    <option value="">Otel Seçiniz</option>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel->id }}" 
                                                {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>
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
                                <label for="firm_id" class="form-label">Firma *</label>
                                <select class="form-select @error('firm_id') is-invalid @enderror" 
                                        id="firm_id" name="firm_id" required>
                                    <option value="">Firma Seçiniz</option>
                                    @foreach($firms as $firm)
                                        <option value="{{ $firm->id }}" 
                                                {{ old('firm_id') == $firm->id ? 'selected' : '' }}>
                                            {{ $firm->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Bitiş Tarihi *</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
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
                                    <option value="TRY" {{ old('currency') == 'TRY' ? 'selected' : '' }}>TRY (₺)</option>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
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
                                       id="base_price" name="base_price" value="{{ old('base_price') }}" required>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="commission_rate" class="form-label">Komisyon Oranı (%) *</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control @error('commission_rate') is-invalid @enderror" 
                                       id="commission_rate" name="commission_rate" value="{{ old('commission_rate') }}" required>
                                @error('commission_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="service_fee" class="form-label">Servis Ücreti</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('service_fee') is-invalid @enderror" 
                                       id="service_fee" name="service_fee" value="{{ old('service_fee') }}">
                                @error('service_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_terms" class="form-label">Ödeme Koşulları</label>
                                <input type="text" class="form-control @error('payment_terms') is-invalid @enderror" 
                                       id="payment_terms" name="payment_terms" value="{{ old('payment_terms') }}" 
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
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           {{ old('is_active') ? 'checked' : '' }}>
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
                                           {{ old('auto_renewal') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_renewal">
                                        Otomatik Yenileme
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kontrat Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Bilgilendirme</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Kontrat Oluşturma</h6>
                    <ul class="mb-0">
                        <li>Kontrat oluşturduktan sonra oda ekleyebilirsiniz</li>
                        <li>Aynı otel ve firma için sadece bir aktif kontrat olabilir</li>
                        <li>Komisyon oranı %0-100 arasında olmalıdır</li>
                        <li>Otomatik yenileme seçeneği kontrat bitiminde otomatik uzatma sağlar</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endpush 