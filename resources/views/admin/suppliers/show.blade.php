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
                    @if($supplier->logo)
                        <img src="{{ Storage::url($supplier->logo) }}" alt="Logo" class="float-end" style="max-height: 40px;">
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <!-- Temel Bilgiler -->
                <h6 class="text-primary mb-3"><i class="fas fa-building"></i> Temel Bilgiler</h6>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Tedarikçi Adı:</th>
                                <td><strong>{{ $supplier->name }}</strong></td>
                            </tr>
                            @if(auth()->user()->isAdmin())
                                <tr>
                                    <th>Gruplar:</th>
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
                                            <span class="text-muted">Grup atanmamış</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th>Muhasebe Kodu:</th>
                                <td>{{ $supplier->accounting_code ?: 'Belirtilmemiş' }}</td>
                            </tr>
                            <tr>
                                <th>Ülke:</th>
                                <td>{{ $supplier->country ?: 'Belirtilmemiş' }}</td>
                            </tr>
                            <tr>
                                <th>Şehir:</th>
                                <td>{{ $supplier->city ?: 'Belirtilmemiş' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Türler:</th>
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
                                <th>Durum:</th>
                                <td>
                                    @if($supplier->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Kayıt Tarihi:</th>
                                <td>{{ $supplier->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($supplier->description)
                <div class="mb-3">
                    <h6 class="text-primary"><i class="fas fa-info-circle"></i> Açıklama</h6>
                    <p class="text-muted">{{ $supplier->description }}</p>
                </div>
                @endif
                
                <!-- Ödeme Bilgileri -->
                <hr class="my-4">
                <h6 class="text-primary mb-3"><i class="fas fa-credit-card"></i> Ödeme Bilgileri</h6>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Ödeme Tipi:</th>
                                <td>
                                    @if($supplier->payment_type == 'cari')
                                        <span class="badge bg-primary">Cari</span>
                                    @elseif($supplier->payment_type == 'credit_card')
                                        <span class="badge bg-success">Kredi Kartı</span>
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Vergi Oranı:</th>
                                <td>{{ $supplier->tax_rate ?? 0 }}%</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($supplier->payment_periods)
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Ödeme Periyodu:</th>
                                <td>
                                    @if($supplier->payment_periods['type'] == 'days')
                                        <span class="badge bg-info">
                                            Rezervasyon Öncesi: {{ $supplier->payment_periods['before_booking'] ?? 0 }} gün<br>
                                            Rezervasyon Sonrası: {{ $supplier->payment_periods['after_booking'] ?? 0 }} gün
                                        </span>
                                    @elseif($supplier->payment_periods['type'] == 'monthly')
                                        <span class="badge bg-info">
                                            Ayın {{ implode(', ', $supplier->payment_periods['days'] ?? []) }}. günleri
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        @endif
                    </div>
                </div>
                
                <!-- İletişim Bilgileri -->
                <hr class="my-4">
                <h6 class="text-primary mb-3"><i class="fas fa-phone"></i> İletişim Bilgileri</h6>
                
                @if($supplier->contact_persons && count($supplier->contact_persons) > 0)
                <div class="mb-3">
                    <h6 class="text-secondary">İletişim Yetkilileri</h6>
                    <div class="row">
                        @foreach($supplier->contact_persons as $person)
                        <div class="col-md-4 mb-2">
                            <div class="card border">
                                <div class="card-body p-2">
                                    <strong>{{ $person['name'] ?? 'Ad belirtilmemiş' }}</strong><br>
                                    <small class="text-muted">
                                        <i class="fas fa-phone"></i> {{ $person['phone'] ?? 'Telefon belirtilmemiş' }}<br>
                                        @if($person['email'])
                                        <i class="fas fa-envelope"></i> {{ $person['email'] }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                @if($supplier->emails && count($supplier->emails) > 0)
                <div class="mb-3">
                    <h6 class="text-secondary">Tedarikçi E-postaları</h6>
                    <div class="row">
                        @foreach($supplier->emails as $email)
                        <div class="col-md-6 mb-2">
                            <div class="card border {{ $email['is_primary'] ? 'border-primary' : '' }}">
                                <div class="card-body p-2">
                                    <strong>{{ $email['name'] ?? 'Ad belirtilmemiş' }}</strong>
                                    @if($email['is_primary'])
                                        <span class="badge bg-primary ms-1">Ana E-posta</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope"></i> {{ $email['email'] ?? 'E-posta belirtilmemiş' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                @if($supplier->address)
                <div class="mb-3">
                    <h6 class="text-secondary">Adres</h6>
                    <p class="text-muted">{{ $supplier->address }}</p>
                </div>
                @endif
                
                @if(auth()->user()->isAdmin() && ($supplier->api_endpoint || $supplier->api_credentials))
                <!-- API Entegrasyon Bilgileri -->
                <hr class="my-4">
                <h6 class="text-primary mb-3"><i class="fas fa-plug"></i> API Entegrasyon Bilgileri</h6>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">API URL:</th>
                                <td>
                                    @if($supplier->api_endpoint)
                                        <a href="{{ $supplier->api_endpoint }}" target="_blank" class="text-decoration-none">
                                            {{ $supplier->api_endpoint }}
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>API Versiyonu:</th>
                                <td>{{ $supplier->api_version ?: 'Belirtilmemiş' }}</td>
                            </tr>
                            <tr>
                                <th>Senkronizasyon:</th>
                                <td>
                                    @if($supplier->sync_enabled)
                                        <span class="badge bg-success">Aktif</span>
                                        @if($supplier->sync_frequency)
                                            ({{ ucfirst($supplier->sync_frequency) }})
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">API Kullanıcı Adı:</th>
                                <td>
                                    @if($supplier->api_credentials && isset($supplier->api_credentials['username']))
                                        {{ $supplier->api_credentials['username'] }}
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>API Anahtarı:</th>
                                <td>
                                    @if($supplier->api_credentials && isset($supplier->api_credentials['api_key']))
                                        <code>{{ Str::limit($supplier->api_credentials['api_key'], 20) }}</code>
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Son Senkronizasyon:</th>
                                <td>
                                    @if($supplier->last_sync_at)
                                        {{ $supplier->last_sync_at->format('d.m.Y H:i') }}
                                    @else
                                        <span class="text-muted">Hiç yapılmamış</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Hızlı İşlemler -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Hızlı İşlemler</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Düzenle
                    </a>
                    
                    @if(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-success" onclick="testConnection({{ $supplier->id }})">
                        <i class="fas fa-plug"></i> Bağlantı Testi
                    </button>
                    
                    <button type="button" class="btn btn-primary" onclick="syncSupplier({{ $supplier->id }})">
                        <i class="fas fa-sync"></i> Senkronize Et
                    </button>
                    @endif
                    
                    <button type="button" class="btn btn-{{ $supplier->is_active ? 'secondary' : 'success' }}" 
                            onclick="toggleSupplierStatus({{ $supplier->id }})">
                        <i class="fas fa-{{ $supplier->is_active ? 'pause' : 'play' }}"></i> 
                        {{ $supplier->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                    </button>
                    
                    @if(auth()->user()->isAdmin())
                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" 
onsubmit="return confirm('Bu tedarikçiyi arşivlemek istediğinizden emin misiniz? Tedarikçi arşivlenecek ancak bağlı rezervasyon kayıtları korunacaktır.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Tedarikçiyi Sil
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- İstatistikler -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">İstatistikler</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Toplam Otel:</th>
                        <td><span class="badge bg-info">{{ $supplier->hotels->count() }}</span></td>
                    </tr>
                    <tr>
                        <th>Son Güncelleme:</th>
                        <td>{{ $supplier->updated_at->format('d.m.Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testConnection(supplierId) {
    // Bağlantı testi fonksiyonu
    alert('Bağlantı testi özelliği yakında eklenecek!');
}

function syncSupplier(supplierId) {
    // Senkronizasyon fonksiyonu
    alert('Senkronizasyon özelliği yakında eklenecek!');
}

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