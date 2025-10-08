@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kontratlar</h2>
    <a href="{{ route('admin.contracts.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Kontrat Ekle
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $contracts->where('is_active', true)->count() }}</h4>
                        <small>Aktif Kontrat</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-contract fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $contracts->filter(function($c) { return $c->isExpiringSoon(); })->count() }}</h4>
                        <small>Yakında Dolacak</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
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
                        <h4 class="mb-0">{{ $contracts->filter(function($c) { return $c->isExpired(); })->count() }}</h4>
                        <small>Süresi Dolmuş</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x"></i>
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
                        <h4 class="mb-0">{{ $contracts->sum(function($c) { return $c->rooms->count(); }) }}</h4>
                        <small>Toplam Oda</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-bed fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($contracts->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Otel</th>
                            <th>Firma</th>
                            <th>Tarih Aralığı</th>
                            <th>Para Birimi</th>
                            <th>Komisyon</th>
                            <th>Oda Sayısı</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $contract)
                        <tr>
                            <td>{{ $contract->id }}</td>
                            <td>
                                @if($contract->hotel)
                                    <strong>{{ $contract->hotel->name }}</strong>
                                    <br><small class="text-muted">{{ $contract->hotel->city }}</small>
                                @else
                                    <span class="text-danger">Otel bulunamadı</span>
                                @endif
                            </td>
                            <td>
                                @if($contract->firm)
                                    <strong>{{ $contract->firm->name }}</strong>
                                    @if($contract->auto_renewal)
                                        <br><span class="badge bg-info">Otomatik Yenileme</span>
                                    @endif
                                @else
                                    <strong>🌍 Genel Kontrat</strong>
                                    <br><small class="text-muted">Tüm firmalar</small>
                                @endif
                            </td>
                            <td>
                                <small>
                                    {{ $contract->start_date->format('d.m.Y') }} - {{ $contract->end_date->format('d.m.Y') }}
                                    <br>
                                    @if($contract->isExpired())
                                        <span class="text-danger">Süresi dolmuş</span>
                                    @elseif($contract->isExpiringSoon())
                                        <span class="text-warning">{{ $contract->getRemainingDays() }} gün kaldı</span>
                                    @else
                                        <span class="text-success">{{ $contract->getRemainingDays() }} gün kaldı</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $contract->currency }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $contract->commission_rate }}%</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $contract->rooms->count() }} Oda</span>
                            </td>
                            <td>
                                <span class="badge {{ $contract->getStatusBadge() }}">
                                    @if($contract->isExpired())
                                        <i class="fas fa-times"></i> Süresi Dolmuş
                                    @elseif($contract->isExpiringSoon())
                                        <i class="fas fa-clock"></i> Yakında Dolacak
                                    @else
                                        <i class="fas fa-check"></i> Aktif
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.contracts.rooms', $contract) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-bed"></i>
                                    </a>
                                    <form action="{{ route('admin.contracts.destroy', $contract) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu kontratı silmek istediğinizden emin misiniz?')">
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
            
            <div class="d-flex justify-content-center mt-4">
                {{ $contracts->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Henüz kontrat eklenmemiş</h5>
                <p class="text-muted">İlk kontratı eklemek için yukarıdaki "Yeni Kontrat Ekle" butonunu kullanın.</p>
            </div>
        @endif
    </div>
</div>
@endsection 