@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Yeni Kredi Hesabı</h2>
    <a href="{{ route('admin.credits.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Geri
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Kredi Hesabı Bilgileri</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.credits.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firm_id" class="form-label">Firma *</label>
                            <select class="form-select @error('firm_id') is-invalid @enderror" id="firm_id" name="firm_id" required>
                                <option value="">Firma Seçiniz</option>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>
                                        {{ $firm->name }} ({{ $firm->email_domain }})
                                    </option>
                                @endforeach
                            </select>
                            @error('firm_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="currency" class="form-label">Para Birimi *</label>
                            <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                <option value="">Para Birimi Seçiniz</option>
                                <option value="TRY" {{ old('currency') == 'TRY' ? 'selected' : '' }}>TRY (Türk Lirası)</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (Amerikan Doları)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (İngiliz Sterlini)</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="initial_balance" class="form-label">Başlangıç Bakiyesi</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control @error('initial_balance') is-invalid @enderror" 
                                       id="initial_balance" name="initial_balance" value="{{ old('initial_balance', 0) }}">
                                <span class="input-group-text" id="currency_symbol">₺</span>
                            </div>
                            @error('initial_balance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Opsiyonel: Hesap oluşturulurken başlangıç bakiyesi ekleyebilirsiniz. Kredili çalışmalarda genellikle 0'dır.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="credit_limit" class="form-label">Kredi Limiti *</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control @error('credit_limit') is-invalid @enderror" 
                                       id="credit_limit" name="credit_limit" value="{{ old('credit_limit', 0) }}" required>
                                <span class="input-group-text" id="credit_limit_symbol">₺</span>
                            </div>
                            @error('credit_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Kredili firmalar için zorunludur: Maksimum borç limiti belirleyiniz.</div>
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

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.credits.index') }}" class="btn btn-secondary me-2">İptal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kredi Hesabı Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Bilgilendirme</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Kredi Hesabı Nedir?</h6>
                    <p class="mb-0">Kredi hesapları, firmaların rezervasyon yapabilmesi için gerekli olan bakiye sistemidir.</p>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Önemli Notlar</h6>
                    <ul class="mb-0">
                        <li>Her firma için sadece bir kredi hesabı oluşturulabilir</li>
                        <li>Bakiye negatif olabilir (borç durumu)</li>
                        <li>Kredi limiti aşıldığında uyarı verilir</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currencySelect = document.getElementById('currency');
    const currencySymbol = document.getElementById('currency_symbol');
    const creditLimitSymbol = document.getElementById('credit_limit_symbol');
    
    const currencySymbols = {
        'TRY': '₺',
        'USD': '$',
        'EUR': '€',
        'GBP': '£'
    };
    
    currencySelect.addEventListener('change', function() {
        const symbol = currencySymbols[this.value] || '₺';
        currencySymbol.textContent = symbol;
        creditLimitSymbol.textContent = symbol;
    });
});
</script>
@endpush
@endsection 