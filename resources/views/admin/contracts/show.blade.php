@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kontrat Detayları</h2>
    <div>
        <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <a href="{{ route('admin.contracts.rooms', $contract) }}" class="btn btn-success">
            <i class="fas fa-bed"></i> Odaları Yönet
        </a>
        <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Kontrat Bilgileri -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-contract"></i> Kontrat Bilgileri
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Kontrat ID:</strong></td>
                                <td>#{{ $contract->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Otel:</strong></td>
                                <td>
                                    <strong>{{ $contract->hotel->name ?? 'Otel bulunamadı' }}</strong>
                                    <br><small class="text-muted">{{ $contract->hotel->city }}, {{ $contract->hotel->country }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Firma:</strong></td>
                                <td>
                                    <strong>{{ $contract->firm->name ?? 'Firma bulunamadı' }}</strong>
                                    @if($contract->auto_renewal)
                                        <br><span class="badge bg-info">Otomatik Yenileme</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Durum:</strong></td>
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
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Başlangıç:</strong></td>
                                <td>{{ $contract->start_date->format('d.m.Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Bitiş:</strong></td>
                                <td>{{ $contract->end_date->format('d.m.Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kalan Gün:</strong></td>
                                <td>
                                    @if($contract->isExpired())
                                        <span class="text-danger">Süresi dolmuş</span>
                                    @else
                                        <span class="text-success">{{ $contract->getRemainingDays() }} gün</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Para Birimi:</strong></td>
                                <td><span class="badge bg-secondary">{{ $contract->currency }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($contract->description)
                <div class="mt-3">
                    <strong>Açıklama:</strong>
                    <p class="mb-0">{{ $contract->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Fiyatlandırma Bilgileri -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-money-bill-wave"></i> Fiyatlandırma
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Temel Fiyat</h6>
                            <h4 class="text-primary">{{ number_format($contract->base_price, 2) }} {{ $contract->currency }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Komisyon Oranı</h6>
                            <h4 class="text-info">{{ $contract->commission_rate }}%</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Servis Ücreti</h6>
                            <h4 class="text-warning">{{ number_format($contract->service_fee ?? 0, 2) }} {{ $contract->currency }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Toplam Komisyon</h6>
                            <h4 class="text-success">{{ number_format($contract->getTotalCommission(), 2) }} {{ $contract->currency }}</h4>
                        </div>
                    </div>
                </div>
                
                @if($contract->payment_terms)
                <div class="mt-3">
                    <strong>Ödeme Koşulları:</strong>
                    <p class="mb-0">{{ $contract->payment_terms }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Odalar -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bed"></i> Odalar ({{ $contract->rooms->count() }})
                </h5>
                <a href="{{ route('admin.contracts.rooms', $contract) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Oda Ekle
                </a>
            </div>
            <div class="card-body">
                @if($contract->rooms->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Oda Tipi</th>
                                    <th>Yemek Planı</th>
                                    <th>Temel Fiyat</th>
                                    <th>Satış Fiyatı</th>
                                    <th>Kar Marjı</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contract->rooms as $room)
                                <tr>
                                    <td>
                                        <strong>{{ $room->room_type }}</strong>
                                        @if($room->description)
                                            <br><small class="text-muted">{{ $room->description }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info">{{ $room->meal_plan }}</span></td>
                                    <td>{{ number_format($room->base_price, 2) }} {{ $contract->currency }}</td>
                                    <td>{{ number_format($room->sale_price, 2) }} {{ $contract->currency }}</td>
                                    <td>
                                        @php
                                            $profit = $room->sale_price - $room->base_price;
                                            $profitMargin = ($profit / $room->base_price) * 100;
                                        @endphp
                                        <span class="badge {{ $profitMargin > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format($profitMargin, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-bed fa-2x text-muted mb-3"></i>
                        <h6 class="text-muted">Henüz oda eklenmemiş</h6>
                        <p class="text-muted">Bu kontrata oda eklemek için "Oda Ekle" butonunu kullanın.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- İstatistikler -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">İstatistikler</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Toplam Oda:</span>
                        <strong>{{ $contract->rooms->count() }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Toplam Değer:</span>
                        <strong>{{ number_format($contract->getTotalValue(), 2) }} {{ $contract->currency }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Toplam Komisyon:</span>
                        <strong>{{ number_format($contract->getTotalCommission(), 2) }} {{ $contract->currency }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Ortalama Oda Fiyatı:</span>
                        <strong>
                            @if($contract->rooms->count() > 0)
                                {{ number_format($contract->getTotalValue() / $contract->rooms->count(), 2) }} {{ $contract->currency }}
                            @else
                                -
                            @endif
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı İşlemler -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Hızlı İşlemler</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.contracts.rooms', $contract) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-bed"></i> Odaları Yönet
                    </a>
                    <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit"></i> Kontratı Düzenle
                    </a>
                    @if($contract->is_active)
                        <button class="btn btn-outline-danger btn-sm" onclick="deactivateContract()">
                            <i class="fas fa-pause"></i> Kontratı Durdur
                        </button>
                    @else
                        <button class="btn btn-outline-success btn-sm" onclick="activateContract()">
                            <i class="fas fa-play"></i> Kontratı Aktifleştir
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kontrat Geçmişi -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Kontrat Geçmişi</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Oluşturulma:</small><br>
                    <strong>{{ $contract->created_at->format('d.m.Y H:i') }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Son Güncelleme:</small><br>
                    <strong>{{ $contract->updated_at->format('d.m.Y H:i') }}</strong>
                </div>
                @if($contract->isExpired())
                    <div class="alert alert-danger mt-3">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i>
                            Bu kontratın süresi {{ $contract->end_date->diffForHumans() }} dolmuş.
                        </small>
                    </div>
                @elseif($contract->isExpiringSoon())
                    <div class="alert alert-warning mt-3">
                        <small>
                            <i class="fas fa-clock"></i>
                            Bu kontrat {{ $contract->end_date->diffForHumans() }} dolacak.
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deactivateContract() {
    if (confirm('Bu kontratı durdurmak istediğinizden emin misiniz?')) {
        // AJAX ile kontrat durumunu değiştir
        fetch('{{ route("admin.contracts.edit", $contract) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                is_active: false
            })
        }).then(() => {
            location.reload();
        });
    }
}

function activateContract() {
    if (confirm('Bu kontratı aktifleştirmek istediğinizden emin misiniz?')) {
        // AJAX ile kontrat durumunu değiştir
        fetch('{{ route("admin.contracts.edit", $contract) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                is_active: true
            })
        }).then(() => {
            location.reload();
        });
    }
}
</script>
@endpush 