@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kar Hesaplama Detayı</h1>
        <a href="{{ route('admin.profits.calculations') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hesaplama Bilgileri</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="200"><strong>Hesaplama Tarihi:</strong></td>
                            <td>{{ $calculation->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Firma:</strong></td>
                            <td>
                                <a href="{{ route('admin.firms.show', $calculation->firm) }}">
                                    {{ $calculation->firm->name }}
                                </a>
                            </td>
                        </tr>
                        @if($calculation->supplier)
                        <tr>
                            <td><strong>Tedarikçi:</strong></td>
                            <td>
                                @if($calculation->supplier->trashed())
                                    <span class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $calculation->supplier->name }} (Silinmiş)
                                    </span>
                                @else
                                    <a href="{{ route('admin.suppliers.show', $calculation->supplier) }}">
                                        {{ $calculation->supplier->name }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if($calculation->contract)
                        <tr>
                            <td><strong>Kontrat:</strong></td>
                            <td>
                                <a href="{{ route('admin.contracts.show', $calculation->contract) }}">
                                    Kontrat #{{ $calculation->contract->id }}
                                </a>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Para Birimi:</strong></td>
                            <td>{{ $calculation->currency }}</td>
                        </tr>
                        <tr>
                            <td><strong>Durum:</strong></td>
                            <td>
                                <span class="badge {{ $calculation->getStatusBadge() }}">
                                    {{ $calculation->getStatusDescription() }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Fiyat Detayları</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <td><strong>Temel Fiyat</strong></td>
                            <td class="text-end">{{ number_format($calculation->base_price, 2) }} {{ $calculation->currency }}</td>
                        </tr>
                        <tr>
                            <td><strong>Servis Ücreti</strong></td>
                            <td class="text-end">{{ number_format($calculation->service_fee, 2) }} {{ $calculation->currency }}</td>
                        </tr>
                        <tr>
                            <td><strong>Komisyon</strong></td>
                            <td class="text-end">{{ number_format($calculation->commission, 2) }} {{ $calculation->currency }}</td>
                        </tr>
                        <tr class="table-primary">
                            <td><strong>Satış Fiyatı</strong></td>
                            <td class="text-end"><strong>{{ number_format($calculation->sale_price, 2) }} {{ $calculation->currency }}</strong></td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>Kar Tutarı</strong></td>
                            <td class="text-end"><strong>{{ number_format($calculation->profit_amount, 2) }} {{ $calculation->currency }}</strong></td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>Kar Yüzdesi</strong></td>
                            <td class="text-end">
                                <span class="badge {{ $calculation->getProfitBadge() }} fs-6">
                                    %{{ number_format($calculation->profit_percentage, 2) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Özet</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <h5 class="text-muted">Toplam Kar</h5>
                        <h2 class="text-success">{{ number_format($calculation->profit_amount, 2) }}</h2>
                        <p class="text-muted mb-0">{{ $calculation->currency }}</p>
                    </div>
                    <hr>
                    <div class="mb-4">
                        <h5 class="text-muted">Kar Oranı</h5>
                        <h2 class="text-info">%{{ number_format($calculation->profit_percentage, 1) }}</h2>
                    </div>
                    <hr>
                    <div>
                        <h5 class="text-muted">Satış Fiyatı</h5>
                        <h2 class="text-primary">{{ number_format($calculation->sale_price, 2) }}</h2>
                        <p class="text-muted mb-0">{{ $calculation->currency }}</p>
                    </div>
                </div>
            </div>

            @if($calculation->notes)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notlar</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $calculation->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

