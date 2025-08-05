@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Onay Senaryoları</h1>
        <a href="{{ route('admin.approvals.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Senaryo
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
            <h6 class="m-0 font-weight-bold text-primary">Onay Senaryoları Listesi</h6>
        </div>
        <div class="card-body">
            @if($scenarios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Senaryo Adı</th>
                                <th>Firma</th>
                                <th>Onay Tipi</th>
                                <th>Onaylayıcı Sayısı</th>
                                <th>Kural Sayısı</th>
                                <th>Maksimum Gün</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scenarios as $scenario)
                            <tr>
                                <td>{{ $scenario->name }}</td>
                                <td>{{ $scenario->firm->name }}</td>
                                <td>
                                    @if($scenario->approval_type === 'single')
                                        <span class="badge bg-info">Tek Onay</span>
                                    @elseif($scenario->approval_type === 'multi_step')
                                        <span class="badge bg-warning">Çok Aşamalı</span>
                                    @else
                                        <span class="badge bg-success">Paralel</span>
                                    @endif
                                </td>
                                <td>{{ $scenario->approvers->count() }}</td>
                                <td>{{ $scenario->rules->count() }}</td>
                                <td>{{ $scenario->max_approval_days }} gün</td>
                                <td>
                                    @if($scenario->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.approvals.show', $scenario) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.approvals.edit', $scenario) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.approvals.rules', $scenario) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <form action="{{ route('admin.approvals.destroy', $scenario) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu senaryoyu silmek istediğinizden emin misiniz?')">
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
                {{ $scenarios->links() }}
            @else
                <div class="text-center py-4">
                    <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz onay senaryosu bulunmuyor</h5>
                    <p class="text-muted">İlk onay senaryonuzu oluşturmak için yukarıdaki butonu kullanın.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 