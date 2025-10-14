@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tedarikçi Düzenle</h2>
    <div>
        <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> Görüntüle
        </a>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tedarikçi Adı *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $supplier->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="type" class="form-label">Tedarikçi Türü *</label>
                        <select class="form-select @error('type') is-invalid @enderror" 
                                id="type" name="type" required>
                            <option value="">Tür Seçiniz</option>
                            <option value="hotel" {{ old('type', $supplier->type) == 'hotel' ? 'selected' : '' }}>Otel</option>
                            <option value="flight" {{ old('type', $supplier->type) == 'flight' ? 'selected' : '' }}>Uçuş</option>
                            <option value="car" {{ old('type', $supplier->type) == 'car' ? 'selected' : '' }}>Araç Kiralama</option>
                            <option value="activity" {{ old('type', $supplier->type) == 'activity' ? 'selected' : '' }}>Aktivite</option>
                            <option value="transfer" {{ old('type', $supplier->type) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $supplier->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            @if(auth()->user()->isAdmin())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>API Entegrasyon Ayarları</strong> - Bu alanlar sadece admin kullanıcılar tarafından düzenlenebilir.
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="api_endpoint" class="form-label">API URL</label>
                        <input type="url" class="form-control @error('api_endpoint') is-invalid @enderror" 
                               id="api_endpoint" name="api_endpoint" value="{{ old('api_endpoint', $supplier->api_endpoint) }}" 
                               placeholder="https://api.example.com">
                        @error('api_endpoint')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="api_version" class="form-label">API Versiyonu</label>
                        <input type="text" class="form-control @error('api_version') is-invalid @enderror" 
                               id="api_version" name="api_version" value="{{ old('api_version', $supplier->api_version) }}" 
                               placeholder="v1">
                        @error('api_version')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="api_username" class="form-label">API Kullanıcı Adı</label>
                        <input type="text" class="form-control @error('api_username') is-invalid @enderror" 
                               id="api_username" name="api_username" value="{{ old('api_username', $supplier->api_credentials['username'] ?? '') }}">
                        @error('api_username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="api_password" class="form-label">API Şifresi</label>
                        <input type="password" class="form-control @error('api_password') is-invalid @enderror" 
                               id="api_password" name="api_password" value="{{ old('api_password') }}" 
                               placeholder="Değiştirmek için yeni şifre girin">
                        @error('api_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Boş bırakırsanız mevcut şifre korunur.</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="api_key" class="form-label">API Anahtarı</label>
                        <input type="text" class="form-control @error('api_key') is-invalid @enderror" 
                               id="api_key" name="api_key" value="{{ old('api_key', $supplier->api_credentials['api_key'] ?? '') }}">
                        @error('api_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
            </div>
            
            <div class="mb-3">
                <label for="sync_frequency" class="form-label">Senkronizasyon Sıklığı</label>
                <select class="form-select @error('sync_frequency') is-invalid @enderror" 
                        id="sync_frequency" name="sync_frequency">
                    <option value="hourly" {{ old('sync_frequency', $supplier->sync_frequency) == 'hourly' ? 'selected' : '' }}>Saatlik</option>
                    <option value="daily" {{ old('sync_frequency', $supplier->sync_frequency) == 'daily' ? 'selected' : '' }}>Günlük</option>
                    <option value="weekly" {{ old('sync_frequency', $supplier->sync_frequency) == 'weekly' ? 'selected' : '' }}>Haftalık</option>
                    <option value="manual" {{ old('sync_frequency', $supplier->sync_frequency) == 'manual' ? 'selected' : '' }}>Manuel</option>
                </select>
                @error('sync_frequency')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            @endif
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Aktif Durumda
                    </label>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="sync_enabled" name="sync_enabled" value="1"
                           {{ old('sync_enabled', $supplier->sync_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="sync_enabled">
                        Senkronizasyon Etkin
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
@endsection 