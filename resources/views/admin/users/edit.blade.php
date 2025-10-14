@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kullanıcı Düzenle</h2>
    <div>
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> Görüntüle
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Ad Soyad *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-posta *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Rol *</label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" name="role" required>
                                    <option value="">Rol Seçiniz</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>👨‍💼 Admin</option>
                                    <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>👔 Manager</option>
                                    <option value="supplier" {{ old('role', $user->role) == 'supplier' ? 'selected' : '' }}>🚚 Tedarikçi</option>
                                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>👤 Kullanıcı</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Yeni Şifre</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Yeni Şifre Tekrar</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" placeholder="Şifre tekrarı">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="firm_id" class="form-label">Firma</label>
                                <select class="form-select @error('firm_id') is-invalid @enderror" 
                                        id="firm_id" name="firm_id">
                                    <option value="">Firma Seçiniz (Opsiyonel)</option>
                                    @foreach($firms as $firm)
                                        <option value="{{ $firm->id }}" 
                                                {{ old('firm_id', $user->firmUser?->firm_id) == $firm->id ? 'selected' : '' }}>
                                            {{ $firm->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('firm_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Durum</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Kullanıcı aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">E-posta Doğrulama</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="email_verified_at" name="email_verified_at" 
                                   {{ old('email_verified_at', $user->email_verified_at) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_verified_at">
                                E-posta doğrulanmış olarak işaretle
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Değişiklikleri Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Kullanıcı Özeti</h6>
            </div>
            <div class="card-body">
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
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Rol:</th>
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
        
        <div class="card mt-3">
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