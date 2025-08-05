@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kredi Hesabı Düzenle</h2>
    <div>
        <a href="{{ route('admin.credits.show', $creditAccount) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> Görüntüle
        </a>
        <a href="{{ route('admin.credits.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.credits.update', $creditAccount) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="firm_id" class="form-label">Firma *</label>
                                <select class="form-select @error('firm_id') is-invalid @enderror" 
                                        id="firm_id" name="firm_id" required>
                                    <option value="">Firma Seçiniz</option>
                                    @foreach($firms as $firm)
                                        <option value="{{ $firm->id }}" 
                                                {{ old('firm_id', $creditAccount->firm_id) == $firm->id ? 'selected' : '' }}>
                                            {{ $firm->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('firm_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Para Birimi *</label>
                                <select class="form-select @error('currency') is-invalid @enderror" 
                                        id="currency" name="currency" required>
                                    <option value="TRY" {{ old('currency', $creditAccount->currency) == 'TRY' ? 'selected' : '' }}>TRY (₺)</option>
                                    <option value="USD" {{ old('currency', $creditAccount->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ old('currency', $creditAccount->currency) == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ old('currency', $creditAccount->currency) == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="credit_limit" class="form-label">Kredi Limiti *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control @error('credit_limit') is-invalid @enderror" 
                                           id="credit_limit" name="credit_limit" value="{{ old('credit_limit', $creditAccount->credit_limit) }}" required>
                                    <span class="input-group-text currency-symbol">{{ $creditAccount->currency }}</span>
                                </div>
                                @error('credit_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="balance" class="form-label">Mevcut Bakiye *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('balance') is-invalid @enderror" 
                                           id="balance" name="balance" value="{{ old('balance', $creditAccount->balance) }}" required>
                                    <span class="input-group-text currency-symbol">{{ $creditAccount->currency }}</span>
                                </div>
                                @error('balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Durum</label>
                                <select class="form-select @error('is_active') is-invalid @enderror" 
                                        id="is_active" name="is_active">
                                    <option value="1" {{ old('is_active', $creditAccount->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active', $creditAccount->is_active) == '0' ? 'selected' : '' }}>Pasif</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_terms" class="form-label">Ödeme Koşulları</label>
                                <select class="form-select @error('payment_terms') is-invalid @enderror" 
                                        id="payment_terms" name="payment_terms">
                                    <option value="immediate" {{ old('payment_terms', $creditAccount->payment_terms) == 'immediate' ? 'selected' : '' }}>Anında Ödeme</option>
                                    <option value="net_30" {{ old('payment_terms', $creditAccount->payment_terms) == 'net_30' ? 'selected' : '' }}>30 Gün Vadeli</option>
                                    <option value="net_60" {{ old('payment_terms', $creditAccount->payment_terms) == 'net_60' ? 'selected' : '' }}>60 Gün Vadeli</option>
                                    <option value="net_90" {{ old('payment_terms', $creditAccount->payment_terms) == 'net_90' ? 'selected' : '' }}>90 Gün Vadeli</option>
                                </select>
                                @error('payment_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notlar</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes', $creditAccount->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Değişiklikleri Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Hesap Özeti</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="120">Firma:</th>
                        <td><strong>{{ $creditAccount->firm->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Bakiye:</th>
                        <td>
                            <span class="badge bg-{{ $creditAccount->balance >= 0 ? 'success' : 'danger' }} fs-6">
                                {{ number_format($creditAccount->balance, 2) }} {{ $creditAccount->currency }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Limit:</th>
                        <td>{{ number_format($creditAccount->credit_limit, 2) }} {{ $creditAccount->currency }}</td>
                    </tr>
                    <tr>
                        <th>Kullanım:</th>
                        <td>
                            @php
                                $usagePercent = $creditAccount->credit_limit > 0 ? ($creditAccount->balance / $creditAccount->credit_limit) * 100 : 0;
                            @endphp
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $usagePercent > 80 ? 'danger' : ($usagePercent > 60 ? 'warning' : 'success') }}" 
                                     style="width: {{ min($usagePercent, 100) }}%">
                                    {{ number_format($usagePercent, 1) }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Durum:</th>
                        <td>
                            @if($creditAccount->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Pasif</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Hızlı İşlemler</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.credits.transactions', $creditAccount) }}" class="btn btn-info">
                        <i class="fas fa-history"></i> İşlem Geçmişi
                    </a>
                    <form action="{{ route('admin.credits.destroy', $creditAccount) }}" method="POST" onsubmit="return confirm('Bu kredi hesabını silmek istediğinizden emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Hesabı Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currencySelect = document.getElementById('currency');
    const currencySymbols = document.querySelectorAll('.currency-symbol');
    
    function updateCurrencySymbols() {
        const selectedCurrency = currencySelect.value;
        const symbolMap = {
            'TRY': '₺',
            'USD': '$',
            'EUR': '€',
            'GBP': '£'
        };
        
        currencySymbols.forEach(symbol => {
            symbol.textContent = symbolMap[selectedCurrency] || selectedCurrency;
        });
    }
    
    currencySelect.addEventListener('change', updateCurrencySymbols);
    updateCurrencySymbols(); // Initial update
});
</script>
@endpush 