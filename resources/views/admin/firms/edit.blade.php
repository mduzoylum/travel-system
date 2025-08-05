@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Firma Düzenle</h2>
    <div>
        <a href="{{ route('admin.firms.show', $firm) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> Görüntüle
        </a>
        <a href="{{ route('admin.firms.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.firms.update', $firm) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Firma Adı *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $firm->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="contact_person" class="form-label">İletişim Kişisi</label>
                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                               id="contact_person" name="contact_person" value="{{ old('contact_person', $firm->contact_person) }}">
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $firm->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $firm->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Adres</label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="3">{{ old('address', $firm->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tax_number" class="form-label">Vergi Numarası</label>
                        <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                               id="tax_number" name="tax_number" value="{{ old('tax_number', $firm->tax_number) }}">
                        @error('tax_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Durum</label>
                        <select class="form-select @error('is_active') is-invalid @enderror" 
                                id="is_active" name="is_active">
                            <option value="1" {{ old('is_active', $firm->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $firm->is_active) == '0' ? 'selected' : '' }}>Pasif</option>
                        </select>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
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