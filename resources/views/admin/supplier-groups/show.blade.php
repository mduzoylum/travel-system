@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tedarikçi Grubu Detayları</h2>
    <div>
        <a href="{{ route('admin.supplier-groups.edit', $supplierGroup) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <a href="{{ route('admin.supplier-groups.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Grup Bilgileri</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="120">ID:</th>
                        <td><strong>#{{ $supplierGroup->id }}</strong></td>
                    </tr>
                    <tr>
                        <th>Grup Adı:</th>
                        <td><strong>{{ $supplierGroup->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Renk:</th>
                        <td>
                            <span class="badge" style="background-color: {{ $supplierGroup->color }}">
                                {{ $supplierGroup->color }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Sıra:</th>
                        <td><span class="badge bg-secondary">{{ $supplierGroup->sort_order }}</span></td>
                    </tr>
                    <tr>
                        <th>Durum:</th>
                        <td>
                            @if($supplierGroup->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Pasif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tedarikçi Sayısı:</th>
                        <td><span class="badge bg-info">{{ $supplierGroup->suppliers->count() }}</span></td>
                    </tr>
                </table>
                
                @if($supplierGroup->description)
                <div class="mt-3">
                    <strong>Açıklama:</strong>
                    <p class="text-muted mt-2">{{ $supplierGroup->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Bu Gruptaki Tedarikçiler ({{ $supplierGroup->suppliers->count() }})</h6>
            </div>
            <div class="card-body">
                @if($supplierGroup->suppliers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ad</th>
                                    <th>Tür</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplierGroup->suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->id }}</td>
                                    <td>
                                        <strong>{{ $supplier->name }}</strong>
                                        @if($supplier->country || $supplier->city)
                                            <br><small class="text-muted">
                                                @if($supplier->city){{ $supplier->city }}@endif
                                                @if($supplier->country && $supplier->city), @endif
                                                @if($supplier->country){{ $supplier->country }}@endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($supplier->types)
                                            @foreach($supplier->types as $type)
                                                <span class="badge bg-info me-1">{{ ucfirst($type) }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if($supplier->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.suppliers.show', $supplier) }}" 
                                           class="btn btn-info btn-sm" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" 
                                           class="btn btn-warning btn-sm" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-truck fa-2x text-muted mb-3"></i>
                        <p class="text-muted">Bu grupta henüz tedarikçi bulunmamaktadır.</p>
                        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tedarikçi Ekle
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

