@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kredi Hesabı Detayı</h2>
    <div>
        <a href="{{ route('admin.credits.edit', $creditAccount) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <a href="{{ route('admin.credits.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Hesap Bilgileri</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Firma:</th>
                                <td>
                                    <strong>{{ $creditAccount->firm->name ?? 'Bilinmeyen Firma' }}</strong>
                                    @if($creditAccount->firm)
                                        <br>
                                        <small class="text-muted">{{ $creditAccount->firm->email_domain ?? '' }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Para Birimi:</th>
                                <td>
                                    <span class="badge bg-secondary">{{ $creditAccount->currency }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Kredi Limiti:</th>
                                <td>
                                    @if($creditAccount->credit_limit > 0)
                                        <span class="text-info">{{ number_format($creditAccount->credit_limit, 2) }} {{ $creditAccount->currency }}</span>
                                    @else
                                        <span class="text-muted">Limit belirlenmemiş</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Mevcut Bakiye:</th>
                                <td>
                                    @if($creditAccount->balance > 0)
                                        <span class="text-success fw-bold fs-4">{{ number_format($creditAccount->balance, 2) }} {{ $creditAccount->currency }}</span>
                                    @elseif($creditAccount->balance < 0)
                                        <span class="text-danger fw-bold fs-4">{{ number_format($creditAccount->balance, 2) }} {{ $creditAccount->currency }}</span>
                                    @else
                                        <span class="text-muted fw-bold fs-4">{{ number_format($creditAccount->balance, 2) }} {{ $creditAccount->currency }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Durum:</th>
                                <td>
                                    @if($creditAccount->balance > 0)
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif($creditAccount->balance < 0)
                                        <span class="badge bg-danger">Borçlu</span>
                                    @else
                                        <span class="badge bg-secondary">Boş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Toplam İşlem:</th>
                                <td>
                                    <span class="badge bg-info">{{ $creditAccount->transactions->count() }} İşlem</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($creditAccount->notes)
                <div class="row">
                    <div class="col-12">
                        <h6>Notlar:</h6>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($creditAccount->notes)) !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Hızlı İşlemler</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{ route('admin.credits.add-credit', $creditAccount) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="amount" placeholder="Miktar" required>
                                <span class="input-group-text">{{ $creditAccount->currency }}</span>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Kredi Ekle
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('admin.credits.use-credit', $creditAccount) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="amount" placeholder="Miktar" required>
                                <span class="input-group-text">{{ $creditAccount->currency }}</span>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-minus"></i> Kredi Kullan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        @if($creditAccount->transactions->count() > 0)
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Son İşlemler</h5>
                <a href="{{ route('admin.credits.transactions', $creditAccount) }}" class="btn btn-sm btn-outline-primary">
                    Tüm İşlemler
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>İşlem Tipi</th>
                                <th>Miktar</th>
                                <th>Bakiye</th>
                                <th>Açıklama</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($creditAccount->transactions->take(5) as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    @if($transaction->type === 'credit')
                                        <span class="badge bg-success">Kredi Eklendi</span>
                                    @else
                                        <span class="badge bg-warning">Kredi Kullanıldı</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->type === 'credit')
                                        <span class="text-success">+{{ number_format($transaction->amount, 2) }} {{ $creditAccount->currency }}</span>
                                    @else
                                        <span class="text-danger">-{{ number_format($transaction->amount, 2) }} {{ $creditAccount->currency }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($transaction->balance_after, 2) }} {{ $creditAccount->currency }}</strong>
                                </td>
                                <td>{{ $transaction->description ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Balance Chart -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Bakiye Özeti</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($creditAccount->balance > 0)
                        <i class="fas fa-arrow-up text-success fa-3x mb-2"></i>
                        <h4 class="text-success">{{ number_format($creditAccount->balance, 2) }} {{ $creditAccount->currency }}</h4>
                        <p class="text-muted">Pozitif Bakiye</p>
                    @elseif($creditAccount->balance < 0)
                        <i class="fas fa-arrow-down text-danger fa-3x mb-2"></i>
                        <h4 class="text-danger">{{ number_format($creditAccount->balance, 2) }} {{ $creditAccount->currency }}</h4>
                        <p class="text-muted">Negatif Bakiye (Borç)</p>
                    @else
                        <i class="fas fa-equals text-muted fa-3x mb-2"></i>
                        <h4 class="text-muted">{{ number_format($creditAccount->balance, 2) }} {{ $creditAccount->currency }}</h4>
                        <p class="text-muted">Sıfır Bakiye</p>
                    @endif
                </div>

                @if($creditAccount->credit_limit > 0)
                <div class="progress mb-3" style="height: 20px;">
                    @php
                        $usagePercentage = abs($creditAccount->balance) / $creditAccount->credit_limit * 100;
                        $usagePercentage = min($usagePercentage, 100);
                    @endphp
                    <div class="progress-bar @if($usagePercentage > 80) bg-danger @elseif($usagePercentage > 50) bg-warning @else bg-success @endif" 
                         style="width: {{ $usagePercentage }}%">
                        {{ number_format($usagePercentage, 1) }}%
                    </div>
                </div>
                <small class="text-muted">Kredi Limiti Kullanımı</small>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">İstatistikler</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-success">{{ $creditAccount->transactions->where('type', 'credit')->count() }}</h6>
                        <small class="text-muted">Kredi Ekleme</small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-warning">{{ $creditAccount->transactions->where('type', 'debit')->count() }}</h6>
                        <small class="text-muted">Kredi Kullanım</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-success">{{ number_format($creditAccount->transactions->where('type', 'credit')->sum('amount'), 2) }}</h6>
                        <small class="text-muted">Toplam Eklenen</small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-warning">{{ number_format($creditAccount->transactions->where('type', 'debit')->sum('amount'), 2) }}</h6>
                        <small class="text-muted">Toplam Kullanılan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 