@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Kar Raporu</h1>
            @if(isset($report['supplier_group']))
                <p class="text-muted mb-0">
                    <i class="fas fa-layer-group"></i> 
                    Tedarikçi Grubu: 
                    <span class="badge" style="background-color: {{ $report['supplier_group']['color'] }}; color: white;">
                        {{ $report['supplier_group']['name'] }}
                    </span>
                </p>
            @endif
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Yazdır
            </button>
            <a href="{{ route('admin.profits.reports') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>
    </div>

    <!-- Rapor Özeti -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Toplam Satış
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($report['total_sales'] ?? 0, 2) }} TRY
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Toplam Kar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($report['total_profit'] ?? 0, 2) }} TRY
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Ortalama Kar %
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                %{{ number_format($report['average_profit_percentage'] ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Toplam İşlem
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $report['total_transactions'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rapor Detayları -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rapor Detayları</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Firma:</strong> {{ $report['firm_name'] ?? 'Tüm Firmalar' }}
                </div>
                <div class="col-md-4">
                    <strong>Başlangıç Tarihi:</strong> {{ $report['start_date'] ?? '-' }}
                </div>
                <div class="col-md-4">
                    <strong>Bitiş Tarihi:</strong> {{ $report['end_date'] ?? '-' }}
                </div>
            </div>

            @if(isset($report['calculations']) && count($report['calculations']) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Temel Fiyat</th>
                                <th>Servis Ücreti</th>
                                <th>Komisyon</th>
                                <th>Satış Fiyatı</th>
                                <th>Kar</th>
                                <th>Kar %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['calculations'] as $calc)
                            <tr>
                                <td>{{ $calc->created_at->format('d.m.Y') }}</td>
                                <td>{{ number_format($calc->base_price, 2) }} {{ $calc->currency }}</td>
                                <td>{{ number_format($calc->service_fee, 2) }} {{ $calc->currency }}</td>
                                <td>{{ number_format($calc->commission, 2) }} {{ $calc->currency }}</td>
                                <td>{{ number_format($calc->sale_price, 2) }} {{ $calc->currency }}</td>
                                <td class="text-success">{{ number_format($calc->profit_amount, 2) }} {{ $calc->currency }}</td>
                                <td>
                                    <span class="badge {{ $calc->getProfitBadge() }}">
                                        %{{ number_format($calc->profit_percentage, 1) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <th>Toplam</th>
                                <th>{{ number_format($report['total_base_price'] ?? 0, 2) }} TRY</th>
                                <th>{{ number_format($report['total_service_fee'] ?? 0, 2) }} TRY</th>
                                <th>{{ number_format($report['total_commission'] ?? 0, 2) }} TRY</th>
                                <th>{{ number_format($report['total_sales'] ?? 0, 2) }} TRY</th>
                                <th class="text-success">{{ number_format($report['total_profit'] ?? 0, 2) }} TRY</th>
                                <th>%{{ number_format($report['average_profit_percentage'] ?? 0, 1) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Bu tarih aralığında veri bulunamadı</h5>
                    <p class="text-muted">Farklı bir tarih aralığı deneyin.</p>
                </div>
            @endif
        </div>
    </div>

    @if(isset($report['calculations']) && count($report['calculations']) > 0)
    <!-- Grafik (Opsiyonel - Chart.js ile eklenebilir) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kar Trendi</h6>
        </div>
        <div class="card-body">
            <div class="chart-area">
                <canvas id="profitChart"></canvas>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
@media print {
    .btn, .sidebar, .navbar, .card-header {
        display: none !important;
    }
    .card {
        border: 1px solid #000 !important;
    }
}
</style>

@if(isset($report['calculations']) && count($report['calculations']) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('profitChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_map(function($calc) { 
                    return $calc->created_at->format('d.m.Y'); 
                }, $report['calculations']->toArray())) !!},
                datasets: [{
                    label: 'Kar Tutarı (TRY)',
                    data: {!! json_encode(array_map(function($calc) { 
                        return $calc->profit_amount; 
                    }, $report['calculations']->toArray())) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Kar Performansı'
                    }
                }
            }
        });
    }
});
</script>
@endif
@endsection

