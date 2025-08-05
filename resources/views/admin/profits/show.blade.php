@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kar Kuralı Detayı</h1>
        <div>
            <a href="{{ route('admin.profits.edit', $profitRule) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Düzenle
            </a>
            <a href="{{ route('admin.profits.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kural Bilgileri</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Kural Adı</h6>
                            <p>{{ $profitRule->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Öncelik</h6>
                            <p>{{ $profitRule->priority }}</p>
                        </div>
                    </div>
                    
                    @if($profitRule->description)
                    <div class="row">
                        <div class="col-12">
                            <h6>Açıklama</h6>
                            <p>{{ $profitRule->description }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Firma</h6>
                            <p>{{ $profitRule->firm?->name ?? 'Tüm Firmalar' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Tedarikçi</h6>
                            <p>{{ $profitRule->supplier?->name ?? 'Tüm Tedarikçiler' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <h6>Destinasyon</h6>
                            <p>{{ $profitRule->destination ?? 'Tüm Destinasyonlar' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6>Seyahat Tipi</h6>
                            <p>{{ ucfirst($profitRule->trip_type) }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6>Yolculuk Tipi</h6>
                            <p>{{ $profitRule->travel_type === 'one_way' ? 'Tek Yön' : 'Gidiş-Dönüş' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <h6>Ücret Tipi</h6>
                            <p>
                                @if($profitRule->fee_type === 'percentage')
                                    Yüzde (%{{ $profitRule->fee_value }})
                                @elseif($profitRule->fee_type === 'fixed')
                                    Sabit (₺{{ $profitRule->fee_value }})
                                @else
                                    Katmanlı
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6>Minimum Ücret</h6>
                            <p>{{ $profitRule->min_fee ? '₺' . $profitRule->min_fee : '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6>Maksimum Ücret</h6>
                            <p>{{ $profitRule->max_fee ? '₺' . $profitRule->max_fee : '-' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Durum</h6>
                            <p>
                                @if($profitRule->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Oluşturulma Tarihi</h6>
                            <p>{{ $profitRule->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kural Açıklaması</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $profitRule->getDescription() }}</p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hızlı İşlemler</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.profits.edit', $profitRule) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <form action="{{ route('admin.profits.destroy', $profitRule) }}" method="POST" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bu kuralı silmek istediğinizden emin misiniz?')">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 