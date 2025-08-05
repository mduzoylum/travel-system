@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kar Hesaplamaları</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#calculateModal">
            <i class="fas fa-calculator"></i> Hesapla
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            @if(session('calculation_id'))
                <a href="#" class="alert-link">Hesaplama detayını görüntüle</a>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kar Hesaplamaları Listesi</h6>
        </div>
        <div class="card-body">
            @if($calculations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Firma</th>
                                <th>Tedarikçi</th>
                                <th>Temel Fiyat</th>
                                <th>Servis Ücreti</th>
                                <th>Komisyon</th>
                                <th>Satış Fiyatı</th>
                                <th>Kar Tutarı</th>
                                <th>Kar %</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calculations as $calculation)
                            <tr>
                                <td>{{ $calculation->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ $calculation->firm->name }}</td>
                                <td>{{ $calculation->supplier?->name ?? '-' }}</td>
                                <td>{{ number_format($calculation->base_price, 2) }} {{ $calculation->currency }}</td>
                                <td>{{ number_format($calculation->service_fee, 2) }} {{ $calculation->currency }}</td>
                                <td>{{ number_format($calculation->commission, 2) }} {{ $calculation->currency }}</td>
                                <td>{{ number_format($calculation->sale_price, 2) }} {{ $calculation->currency }}</td>
                                <td>{{ number_format($calculation->profit_amount, 2) }} {{ $calculation->currency }}</td>
                                <td>
                                    <span class="badge {{ $calculation->getProfitBadge() }}">
                                        %{{ number_format($calculation->profit_percentage, 1) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $calculation->getStatusBadge() }}">
                                        {{ $calculation->getStatusDescription() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $calculations->links() }}
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz kar hesaplaması bulunmuyor</h5>
                    <p class="text-muted">İlk kar hesaplamanızı yapmak için yukarıdaki butonu kullanın.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hesaplama Modal -->
<div class="modal fade" id="calculateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kar Hesaplama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.profits.calculate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="firm_id" class="form-label">Firma *</label>
                                <select class="form-select" id="firm_id" name="firm_id" required>
                                    <option value="">Firma Seçin</option>
                                    @foreach(\App\DDD\Modules\Firm\Models\Firm::where('is_active', true)->get() as $firm)
                                        <option value="{{ $firm->id }}">{{ $firm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Tedarikçi</label>
                                <select class="form-select" id="supplier_id" name="supplier_id">
                                    <option value="">Tedarikçi Seçin</option>
                                    @foreach(\App\DDD\Modules\Supplier\Domain\Entities\Supplier::where('is_active', true)->get() as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="base_price" class="form-label">Temel Fiyat *</label>
                                <input type="number" step="0.01" class="form-control" id="base_price" name="base_price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Para Birimi *</label>
                                <select class="form-select" id="currency" name="currency" required>
                                    <option value="TRY">TRY</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="service_type" class="form-label">Servis Tipi *</label>
                                <select class="form-select" id="service_type" name="service_type" required>
                                    <option value="reservation">Rezervasyon</option>
                                    <option value="cancellation">İptal</option>
                                    <option value="modification">Değişiklik</option>
                                    <option value="booking">Rezervasyon</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="trip_type" class="form-label">Seyahat Tipi *</label>
                                <select class="form-select" id="trip_type" name="trip_type" required>
                                    <option value="domestic">Yurtiçi</option>
                                    <option value="international">Yurtdışı</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="travel_type" class="form-label">Yolculuk Tipi *</label>
                                <select class="form-select" id="travel_type" name="travel_type" required>
                                    <option value="one_way">Tek Yön</option>
                                    <option value="round_trip">Gidiş-Dönüş</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="destination" class="form-label">Destinasyon</label>
                        <input type="text" class="form-control" id="destination" name="destination" placeholder="Örn: İstanbul">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Hesapla</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 