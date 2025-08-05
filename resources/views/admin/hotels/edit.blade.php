@extends('layouts.admin')

@section('title', 'Otel Düzenle')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Otel Düzenle: {{ $hotel->name }}</h2>
                <a href="{{ route('admin.hotels.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.hotels.update', $hotel) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Otel Adı *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $hotel->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="star_rating" class="form-label">Yıldız Sayısı *</label>
                                        <select class="form-select @error('star_rating') is-invalid @enderror" 
                                                id="star_rating" name="star_rating" required>
                                            <option value="">Seçiniz</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('star_rating', $hotel->star_rating) == $i ? 'selected' : '' }}>
                                                    {{ $i }} Yıldız
                                                </option>
                                            @endfor
                                        </select>
                                        @error('star_rating')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">Şehir *</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                               id="city" name="city" value="{{ old('city', $hotel->city) }}" required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="country" class="form-label">Ülke *</label>
                                        <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                               id="country" name="country" value="{{ old('country', $hotel->country) }}" required>
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Adres *</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3" required>{{ old('address', $hotel->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Açıklama</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description', $hotel->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="supplier_id" class="form-label">Tedarikçi</label>
                                    <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                            id="supplier_id" name="supplier_id">
                                        <option value="">Manuel (Tedarikçi Yok)</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $hotel->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="external_id" class="form-label">Tedarikçi ID</label>
                                    <input type="text" class="form-control @error('external_id') is-invalid @enderror" 
                                           id="external_id" name="external_id" value="{{ old('external_id', $hotel->external_id) }}">
                                    @error('external_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Otel Resmi</label>
                                    @if($hotel->image)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($hotel->image) }}" alt="{{ $hotel->name }}" 
                                                 class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Desteklenen formatlar: JPEG, PNG, JPG, GIF (Max: 2MB)
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               value="1" {{ old('is_active', $hotel->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.hotels.index') }}" class="btn btn-secondary me-2">İptal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 