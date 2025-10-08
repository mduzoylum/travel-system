@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Servis Ücretleri</h1>
        <a href="{{ route('admin.profits.service-fees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Ücret
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
            <h6 class="m-0 font-weight-bold text-primary">Servis Ücretleri Listesi</h6>
        </div>
        <div class="card-body">
            @if($serviceFees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Ücret Adı</th>
                                <th>Firma</th>
                                <th>Servis Tipi</th>
                                <th>Ücret Tipi</th>
                                <th>Değer</th>
                                <th>Para Birimi</th>
                                <th>Min/Max</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceFees as $fee)
                            <tr>
                                <td>{{ $fee->name }}</td>
                                <td>{{ $fee->firm?->name ?? 'Tüm Firmalar' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $fee->getServiceTypeDescription() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $fee->getFeeTypeDescription() }}</span>
                                </td>
                                <td>
                                    @if($fee->fee_type === 'percentage')
                                        %{{ $fee->fee_value }}
                                    @else
                                        {{ $fee->fee_value }}
                                    @endif
                                </td>
                                <td>{{ $fee->currency }}</td>
                                <td>
                                    @if($fee->min_amount || $fee->max_amount)
                                        @if($fee->min_amount)Min: {{ $fee->min_amount }}@endif
                                        @if($fee->max_amount)Max: {{ $fee->max_amount }}@endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($fee->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                    @if($fee->is_mandatory)
                                        <span class="badge bg-warning">Zorunlu</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.profits.service-fees.show', $fee) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.profits.service-fees.edit', $fee) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.profits.service-fees.destroy', $fee) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu ücreti silmek istediğinizden emin misiniz?')">
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
                {{ $serviceFees->links() }}
            @else
                <div class="text-center py-4">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz servis ücreti bulunmuyor</h5>
                    <p class="text-muted">İlk servis ücretinizi oluşturmak için yukarıdaki butonu kullanın.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 