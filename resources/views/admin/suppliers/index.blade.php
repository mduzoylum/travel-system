@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tedarikçiler</h2>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Tedarikçi Ekle
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($suppliers->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ad</th>
                            @if(auth()->user()->isAdmin())
                                <th>Grup</th>
                            @endif
                            <th>Ülke/Şehir</th>
                            <th>Tür</th>
                            <th>Muhasebe Kodu</th>
                            <th>Ödeme Tipi</th>
                            <th>Durum</th>
                            <th>Otel Sayısı</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($supplier->logo)
                                        <img src="{{ Storage::url($supplier->logo) }}" alt="Logo" class="me-2" style="max-height: 30px;">
                                    @endif
                                    <div>
                                        <strong>{{ $supplier->name }}</strong>
                                        @if($supplier->description)
                                            <br><small class="text-muted">{{ Str::limit($supplier->description, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td>
                                    @if($supplier->groups->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($supplier->groups as $group)
                                                <span class="badge" style="background-color: {{ $group->color }}; color: white;" 
                                                      title="{{ $group->group_type_label }}">
                                                    {{ $group->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">Grup yok</span>
                                    @endif
                                </td>
                            @endif
                            <td>
                                @if($supplier->country || $supplier->city)
                                    <small>
                                        @if($supplier->country)
                                            <i class="fas fa-flag"></i> {{ $supplier->country }}
                                        @endif
                                        @if($supplier->country && $supplier->city)
                                            <br>
                                        @endif
                                        @if($supplier->city)
                                            <i class="fas fa-map-marker-alt"></i> {{ $supplier->city }}
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">Belirtilmemiş</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->types && count($supplier->types) > 0)
                                    @foreach($supplier->types as $type)
                                        <span class="badge bg-info me-1">{{ ucfirst($type) }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Tip belirtilmemiş</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->accounting_code)
                                    <code>{{ $supplier->accounting_code }}</code>
                                @else
                                    <span class="text-muted">Belirtilmemiş</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->payment_type == 'cari')
                                    <span class="badge bg-primary">Cari</span>
                                @elseif($supplier->payment_type == 'credit_card')
                                    <span class="badge bg-success">Kredi Kartı</span>
                                @else
                                    <span class="text-muted">Belirtilmemiş</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">
                                        Pasif
                                        <i class="fas fa-exclamation-triangle ms-1" title="Satış kanalında gösterilmez"></i>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $supplier->hotels->count() }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}" 
                                       class="btn btn-info btn-sm" title="Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" 
                                       class="btn btn-warning btn-sm" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-{{ $supplier->is_active ? 'secondary' : 'success' }} btn-sm" 
                                            onclick="toggleSupplierStatus({{ $supplier->id }})" 
                                            title="{{ $supplier->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                        <i class="fas fa-{{ $supplier->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                    @if(auth()->user()->isAdmin())
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Bu tedarikçiyi arşivlemek istediğinizden emin misiniz? Tedarikçi arşivlenecek ancak bağlı rezervasyon kayıtları korunacaktır.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $suppliers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Henüz tedarikçi eklenmemiş</h5>
                <p class="text-muted">İlk tedarikçinizi eklemek için aşağıdaki butona tıklayın.</p>
                <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Yeni Tedarikçi Ekle
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSupplierStatus(supplierId) {
    if (confirm('Tedarikçi durumunu değiştirmek istediğinizden emin misiniz?')) {
        fetch(`/admin/suppliers/${supplierId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu!');
        });
    }
}
</script>
@endpush