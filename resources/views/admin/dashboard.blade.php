@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Admin Dashboard</h2>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Toplam Otel</h5>
                            <h3 class="mb-0">{{ \App\DDD\Modules\Contract\Models\Hotel::count() ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hotel fa-2x"></i>
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
                            <h5 class="card-title">Aktif Kontrat</h5>
                            <h3 class="mb-0">{{ \App\DDD\Modules\Contract\Models\Contract::where('is_active', true)->count() ?? 0 }}</h3>
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
                            <h5 class="card-title">Kredi Hesapları</h5>
                            <h3 class="mb-0">{{ \App\DDD\Modules\Credit\Domain\Entities\CreditAccount::count() ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-credit-card fa-2x"></i>
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
                            <h5 class="card-title">Tedarikçiler</h5>
                            <h3 class="mb-0">{{ \App\DDD\Modules\Supplier\Domain\Entities\Supplier::count() ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hızlı İşlemler -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Hızlı İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.hotels.create') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus"></i> Yeni Otel Ekle
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.contracts.create') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-plus"></i> Yeni Kontrat Oluştur
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.credits.create') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-plus"></i> Kredi Hesabı Aç
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-plus"></i> Tedarikçi Ekle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Son İşlemler -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Son Eklenen Oteller</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach(\App\DDD\Modules\Contract\Models\Hotel::latest()->limit(5)->get() ?? [] as $hotel)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $hotel->name }}</h6>
                                <small class="text-muted">{{ $hotel->city }}, {{ $hotel->country }}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $hotel->star_rating }} ★</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Son Kredi İşlemleri</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach(\App\DDD\Modules\Credit\Domain\Entities\CreditTransaction::with('creditAccount.firm')->latest()->limit(5)->get() ?? [] as $transaction)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $transaction->creditAccount->firm->name ?? 'Bilinmeyen Firma' }}</h6>
                                <small class="text-muted">{{ $transaction->description }}</small>
                            </div>
                            <span class="badge {{ $transaction->type === 'credit' ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} {{ $transaction->creditAccount->currency }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 