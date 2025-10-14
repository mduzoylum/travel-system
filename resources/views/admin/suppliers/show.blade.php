@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tedarikçi Detayı</h2>
    <div>
        <button type="button" class="btn btn-success" onclick="testConnection({{ $supplier->id }})">
            <i class="fas fa-plug"></i> Bağlantı Testi
        </button>
        <button type="button" class="btn btn-primary" onclick="syncSupplier({{ $supplier->id }})">
            <i class="fas fa-sync"></i> Senkronize Et
        </button>
        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-truck"></i> {{ $supplier->name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Tedarikçi Adı:</th>
                                <td><strong>{{ $supplier->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Türler:</th>
                                <td>
                                    @if($supplier->types && count($supplier->types) > 0)
                                        @foreach($supplier->types as $type)
                                            <span class="badge bg-info me-1">{{ ucfirst($type) }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Tip belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>API URL:</th>
                                <td>
                                    @if($supplier->api_url)
                                        <code>{{ $supplier->api_url }}</code>
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>API Versiyonu:</th>
                                <td>
                                    @if($supplier->api_version)
                                        <code>{{ $supplier->api_version }}</code>
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Durum:</th>
                                <td>
                                    @if($supplier->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Senkronizasyon:</th>
                                <td>
                                    <span class="badge bg-info">{{ $supplier->sync_frequency }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Son Sync:</th>
                                <td>
                                    @if($supplier->last_sync_at)
                                        {{ $supplier->last_sync_at->format('d.m.Y H:i') }}
                                    @else
                                        <span class="text-muted">Hiç sync edilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Oluşturulma:</th>
                                <td>{{ $supplier->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($supplier->description)
                <div class="mt-4">
                    <h6>Açıklama:</h6>
                    <p class="text-muted">{{ $supplier->description }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">API Bilgileri</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="120">Kullanıcı Adı:</th>
                                <td>
                                    @if($supplier->api_username)
                                        <code>{{ $supplier->api_username }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>API Anahtarı:</th>
                                <td>
                                    @if($supplier->api_key)
                                        <code>{{ Str::limit($supplier->api_key, 20) }}...</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Güvenlik:</strong> API şifresi güvenlik nedeniyle gösterilmiyor.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Hızlı İşlemler</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success" onclick="testConnection({{ $supplier->id }})">
                        <i class="fas fa-plug"></i> Bağlantı Testi
                    </button>
                    <button type="button" class="btn btn-primary" onclick="syncSupplier({{ $supplier->id }})">
                        <i class="fas fa-sync"></i> Senkronize Et
                    </button>
                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Bu tedarikçiyi silmek istediğinizden emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Tedarikçiyi Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">İstatistikler</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12">
                        <h4 class="text-primary">{{ $supplier->hotels->count() ?? 0 }}</h4>
                        <small class="text-muted">Otel</small>
                    </div>
                </div>
            </div>
        </div>
        
        @if($supplier->hotels->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Son Eklenen Oteller</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($supplier->hotels->take(5) as $hotel)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $hotel->name }}</h6>
                            <small class="text-muted">{{ $hotel->city }}, {{ $hotel->country }}</small>
                        </div>
                        <span class="badge bg-warning rounded-pill">{{ $hotel->stars }} ★</span>
                    </div>
                    @endforeach
                </div>
            </div>
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