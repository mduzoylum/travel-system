@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kullanıcı Detayları</h2>
    <div>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-user"></i> Kullanıcı Bilgileri
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="120">ID:</th>
                                <td><strong>#{{ $user->id }}</strong></td>
                            </tr>
                            <tr>
                                <th>Ad Soyad:</th>
                                <td><strong>{{ $user->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>E-posta:</th>
                                <td>
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check"></i> Doğrulanmış
                                        </span>
                                    @else
                                        <span class="badge bg-warning ms-2">
                                            <i class="fas fa-clock"></i> Beklemede
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Telefon:</th>
                                <td>
                                    @if($user->phone)
                                        <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="120">Rol:</th>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->role === 'manager')
                                        <span class="badge bg-warning">Manager</span>
                                    @else
                                        <span class="badge bg-secondary">User</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Durum:</th>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Firma:</th>
                                <td>
                                    @if($user->firmUser && $user->firmUser->firm)
                                        <span class="badge bg-primary">{{ $user->firmUser->firm->name }}</span>
                                    @else
                                        <span class="text-muted">Firma atanmamış</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Kayıt Tarihi:</th>
                                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($user->firmUser && $user->firmUser->firm)
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-building"></i> Firma Bilgileri
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="120">Firma Adı:</th>
                                <td><strong>{{ $user->firmUser->firm->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>İletişim Kişisi:</th>
                                <td>{{ $user->firmUser->firm->contact_person ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>E-posta:</th>
                                <td>
                                    @if($user->firmUser->firm->email)
                                        <a href="mailto:{{ $user->firmUser->firm->email }}">{{ $user->firmUser->firm->email }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="120">Telefon:</th>
                                <td>
                                    @if($user->firmUser->firm->phone)
                                        <a href="tel:{{ $user->firmUser->firm->phone }}">{{ $user->firmUser->firm->phone }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Vergi No:</th>
                                <td>{{ $user->firmUser->firm->tax_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Firma Durumu:</th>
                                <td>
                                    @if($user->firmUser->firm->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Hızlı İşlemler</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(!$user->email_verified_at)
                        <form action="{{ route('admin.users.resend-verification', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-envelope"></i> E-posta Doğrula
                            </button>
                        </form>
                    @endif
                    
                    <button type="button" class="btn btn-{{ $user->is_active ? 'secondary' : 'success' }} w-100" 
                            onclick="toggleUserStatus({{ $user->id }})">
                        <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }}"></i> 
                        {{ $user->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                    </button>
                    
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Kullanıcıyı Sil
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">İstatistikler</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $user->firmUser && $user->firmUser->firm ? $user->firmUser->firm->creditAccounts->count() : 0 }}</h4>
                        <small class="text-muted">Kredi Hesabı</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $user->firmUser && $user->firmUser->firm ? $user->firmUser->firm->contracts->count() : 0 }}</h4>
                        <small class="text-muted">Kontrat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleUserStatus(userId) {
    fetch(`/admin/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        alert('İşlem sırasında hata oluştu.');
    });
}
</script>
@endpush 