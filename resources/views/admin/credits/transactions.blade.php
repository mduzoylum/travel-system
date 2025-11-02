@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kredi İşlem Geçmişi</h2>
    <div>
        <a href="{{ route('admin.credits.show', $creditAccount) }}" class="btn btn-info">
            <i class="fas fa-arrow-left"></i> Hesaba Dön
        </a>
        <a href="{{ route('admin.credits.index') }}" class="btn btn-secondary">
            <i class="fas fa-list"></i> Tüm Hesaplar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-credit-card"></i> Hesap Bilgileri
                </h6>
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
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCreditModal">
                        <i class="fas fa-plus"></i> Kredi Ekle
                    </button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#useCreditModal">
                        <i class="fas fa-minus"></i> Kredi Kullan
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-history"></i> İşlem Geçmişi
                </h6>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>İşlem Türü</th>
                                    <th>Açıklama</th>
                                    <th>Tutar</th>
                                    <th>Bakiye</th>
                                    <th>İşlem Yapan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <small>{{ $transaction->created_at->format('d.m.Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($transaction->type === 'credit')
                                            <span class="badge bg-success">
                                                <i class="fas fa-plus"></i> Kredi Eklendi
                                            </span>
                                        @elseif($transaction->type === 'debit')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-minus"></i> Kredi Kullanıldı
                                            </span>
                                        @else
                                            <span class="badge bg-info">{{ $transaction->type }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transaction->description ?? '-' }}
                                    </td>
                                    <td>
                                        <span class="text-{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                            {{ $transaction->type === 'credit' ? '+' : '-' }}
                                            {{ number_format($transaction->amount, 2) }} {{ $creditAccount->currency }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($transaction->balance_after, 2) }} {{ $creditAccount->currency }}</strong>
                                    </td>
                                    <td>
                                        @if($transaction->performer)
                                            <small>{{ $transaction->performer->name }}</small>
                                            <br>
                                            <small class="text-muted">{{ $transaction->performer->email }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Henüz işlem yapılmamış</h5>
                        <p class="text-muted">Bu hesap için henüz kredi işlemi bulunmuyor.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Credit Modal -->
<div class="modal fade" id="addCreditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.credits.add-credit', $creditAccount) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Kredi Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Tutar ({{ $creditAccount->currency }}) *</label>
                        <input type="number" step="0.01" min="0" class="form-control" 
                               id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">Kredi Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Use Credit Modal -->
<div class="modal fade" id="useCreditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.credits.use-credit', $creditAccount) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Kredi Kullan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="use_amount" class="form-label">Tutar ({{ $creditAccount->currency }}) *</label>
                        <input type="number" step="0.01" min="0" max="{{ $creditAccount->balance }}" 
                               class="form-control" id="use_amount" name="amount" required>
                        <div class="form-text">Maksimum kullanılabilir: {{ number_format($creditAccount->balance, 2) }} {{ $creditAccount->currency }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="use_description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="use_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-warning">Kredi Kullan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Use credit amount validation
    const useAmountInput = document.getElementById('use_amount');
    const maxAmount = {{ $creditAccount->balance }};
    
    useAmountInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (value > maxAmount) {
            this.setCustomValidity('Tutar bakiye miktarını aşamaz');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endpush 