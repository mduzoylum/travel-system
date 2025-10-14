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
                            <th>Tür</th>
                            <th>API URL</th>
                            <th>Durum</th>
                            <th>Son Sync</th>
                            <th>Otel Sayısı</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td>
                                <strong>{{ $supplier->name }}</strong>
                                @if($supplier->description)
                                    <br><small class="text-muted">{{ Str::limit($supplier->description, 50) }}</small>
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
                                @if($supplier->api_url)
                                    <code>{{ Str::limit($supplier->api_url, 30) }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->last_sync_at)
                                    <small>{{ $supplier->last_sync_at->format('d.m.Y H:i') }}</small>
                                @else
                                    <span class="text-muted">Hiç sync edilmemiş</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $supplier->hotels->count() }} Otel</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success" onclick="testConnection({{ $supplier->id }})">
                                        <i class="fas fa-plug"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="syncSupplier({{ $supplier->id }})">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu tedarikçiyi silmek istediğinizden emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
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
            
            <div class="d-flex justify-content-center mt-4">
                {{ $suppliers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Henüz tedarikçi eklenmemiş</h5>
                <p class="text-muted">İlk tedarikçiyi eklemek için yukarıdaki butonu kullanın.</p>
            </div>
        @endif
    </div>
</div>

<!-- Sync Modal -->
<div class="modal fade" id="syncModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tedarikçi Senkronizasyonu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="syncProgress" class="d-none">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Senkronize ediliyor...</span>
                        </div>
                        <p class="mt-2">Tedarikçi verileri senkronize ediliyor...</p>
                    </div>
                </div>
                <div id="syncResult"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testConnection(supplierId) {
    fetch(`/admin/suppliers/${supplierId}/test-connection`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Bağlantı başarılı!');
        } else {
            alert('Bağlantı hatası: ' + data.message);
        }
    })
    .catch(error => {
        alert('Bağlantı testi sırasında hata oluştu.');
    });
}

function syncSupplier(supplierId) {
    const modal = new bootstrap.Modal(document.getElementById('syncModal'));
    const progress = document.getElementById('syncProgress');
    const result = document.getElementById('syncResult');
    
    modal.show();
    progress.classList.remove('d-none');
    result.innerHTML = '';
    
    fetch(`/admin/suppliers/${supplierId}/sync`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        progress.classList.add('d-none');
        if (data.success) {
            result.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Senkronizasyon başarılı!
                    <br>${data.message}
                </div>
            `;
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            result.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Senkronizasyon hatası!
                    <br>${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        progress.classList.add('d-none');
        result.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Senkronizasyon sırasında hata oluştu.
            </div>
        `;
    });
}
</script>
@endpush 