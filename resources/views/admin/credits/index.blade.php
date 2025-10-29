@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kredi Hesapları</h2>
    <a href="{{ route('admin.credits.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Kredi Hesabı
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($creditAccounts->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Firma</th>
                            <th>Bakiye</th>
                            <th>Para Birimi</th>
                            <th>Son İşlem</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($creditAccounts as $account)
                        <tr>
                            <td>{{ $account->id }}</td>
                            <td>
                                <strong>{{ $account->firm->name ?? 'Bilinmeyen Firma' }}</strong>
                                @if($account->firm)
                                    <br>
                                    <small class="text-muted">{{ $account->firm->email_domain ?? '' }}</small>
                                @endif
                            </td>
                            <td>
                                @if($account->balance > 0)
                                    <span class="text-success fw-bold">{{ number_format($account->balance, 2) }}</span>
                                @elseif($account->balance < 0)
                                    <span class="text-danger fw-bold">{{ number_format($account->balance, 2) }}</span>
                                @else
                                    <span class="text-muted">{{ number_format($account->balance, 2) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $account->currency }}</span>
                            </td>
                            <td>
                                @php $lastTx = $account->transactions->first(); @endphp
                                @if($lastTx)
                                    {{ $lastTx->created_at->format('d.m.Y H:i') }}
                                @else
                                    <span class="text-muted">İşlem yok</span>
                                @endif
                            </td>
                            <td>
                                @if($account->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.credits.show', $account) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.credits.edit', $account) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.credits.transactions', $account) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-history"></i> İşlemler
                                    </a>
                                    <form action="{{ route('admin.credits.destroy', $account) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu kredi hesabını silmek istediğinizden emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $creditAccounts->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Henüz kredi hesabı oluşturulmamış</h5>
                <p class="text-muted">İlk kredi hesabını oluşturmak için yukarıdaki "Yeni Kredi Hesabı" butonunu kullanın.</p>
            </div>
        @endif
    </div>
</div>

<!-- Quick Stats -->
@if($creditAccounts->count() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Toplam Bakiye</h5>
                        <h3 class="mb-0">{{ number_format($creditAccounts->sum('balance'), 2) }} ₺</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Aktif Hesaplar</h5>
                        <h3 class="mb-0">{{ $creditAccounts->where('balance', '>', 0)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Borçlu Hesaplar</h5>
                        <h3 class="mb-0">{{ $creditAccounts->where('balance', '<', 0)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Toplam İşlem</h5>
                        <h3 class="mb-0">{{ $creditAccounts->sum(function($account) { return $account->transactions->count(); }) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exchange-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection 