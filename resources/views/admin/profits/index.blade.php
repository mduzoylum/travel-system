@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kar Kuralları</h1>
        <a href="{{ route('admin.profits.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Kural
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kar Kuralları Listesi</h6>
        </div>
        <div class="card-body">
            @if($profitRules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Kural Adı</th>
                                <th>Firma</th>
                                <th>Tedarikçi</th>
                                <th>Destinasyon</th>
                                <th>Tip</th>
                                <th>Ücret</th>
                                <th>Öncelik</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($profitRules as $rule)
                            <tr>
                                <td>{{ $rule->name }}</td>
                                <td>{{ $rule->firm?->name ?? 'Tüm Firmalar' }}</td>
                                <td>{{ $rule->supplier?->name ?? 'Tüm Tedarikçiler' }}</td>
                                <td>{{ $rule->destination ?? 'Tüm Destinasyonlar' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($rule->trip_type) }}</span>
                                    <span class="badge bg-secondary">{{ $rule->travel_type === 'one_way' ? 'Tek Yön' : 'Gidiş-Dönüş' }}</span>
                                </td>
                                <td>
                                    @if($rule->fee_type === 'percentage')
                                        %{{ $rule->fee_value }}
                                    @elseif($rule->fee_type === 'fixed')
                                        ₺{{ $rule->fee_value }}
                                    @else
                                        Katmanlı
                                    @endif
                                </td>
                                <td>{{ $rule->priority }}</td>
                                <td>
                                    @if($rule->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.profits.show', $rule) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.profits.edit', $rule) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.profits.destroy', $rule) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu kuralı silmek istediğinizden emin misiniz?')">
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
                {{ $profitRules->links() }}
            @else
                <div class="text-center py-4">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz kar kuralı bulunmuyor</h5>
                    <p class="text-muted">İlk kar kuralınızı oluşturmak için yukarıdaki butonu kullanın.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 