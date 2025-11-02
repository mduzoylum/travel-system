@extends('layouts.admin')

@section('title', 'Yeni Otel Ekle')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Yeni Otel Ekle</h2>
                <a href="{{ route('admin.hotels.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Lütfen aşağıdaki hataları düzeltin:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.hotels.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Otel Adı *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="stars" class="form-label">Yıldız Sayısı *</label>
                                        <select class="form-select @error('stars') is-invalid @enderror" 
                                                id="stars" name="stars" required>
                                            <option value="">Seçiniz</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('stars') == $i ? 'selected' : '' }}>
                                                    {{ $i }} Yıldız
                                                </option>
                                            @endfor
                                        </select>
                                        @error('stars')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="country_id" class="form-label">Ülke *</label>
                                        <select class="form-select @error('country_id') is-invalid @enderror" 
                                                id="country_id" name="country_id" required>
                                            <option value="">Ülke Seçiniz</option>
                                            @foreach($countries ?? [] as $country)
                                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="city_id" class="form-label">Şehir *</label>
                                        <select class="form-select @error('city_id') is-invalid @enderror" 
                                                id="city_id" name="city_id" required>
                                            <option value="">Önce ülke seçiniz</option>
                                        </select>
                                        @error('city_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="sub_destination_id" class="form-label">Alt Destinasyon</label>
                                        <select class="form-select @error('sub_destination_id') is-invalid @enderror" 
                                                id="sub_destination_id" name="sub_destination_id">
                                            <option value="">Önce şehir seçiniz (Opsiyonel)</option>
                                        </select>
                                        @error('sub_destination_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Adres *</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="latitude" class="form-label">Enlem (Latitude)</label>
                                        <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                               id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="41.0082">
                                        @error('latitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Örn: 41.0082 (İstanbul merkez)</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="longitude" class="form-label">Boylam (Longitude)</label>
                                        <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                               id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="28.9784">
                                        @error('longitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Örn: 28.9784 (İstanbul merkez)</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="min_price" class="form-label">Minimum Fiyat (₺) *</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('min_price') is-invalid @enderror" 
                                           id="min_price" name="min_price" value="{{ old('min_price') }}" required>
                                    @error('min_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Açıklama</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
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
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                           id="external_id" name="external_id" value="{{ old('external_id') }}">
                                    @error('external_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Otel Resmi</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Desteklenen formatlar: JPEG, PNG, JPG, GIF (Max: 5MB)
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               value="1" {{ old('is_active') ? 'checked' : '' }}>
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
                                <i class="fas fa-save"></i> Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country_id');
    const citySelect = document.getElementById('city_id');
    const subDestinationSelect = document.getElementById('sub_destination_id');
    
    // Ülke değiştiğinde şehirleri yükle
    countrySelect.addEventListener('change', function() {
        const countryId = this.value;
        citySelect.innerHTML = '<option value="">Yükleniyor...</option>';
        subDestinationSelect.innerHTML = '<option value="">Önce şehir seçiniz (Opsiyonel)</option>';
        
        if (countryId) {
            fetch(`/api/destinations/cities?country_id=${countryId}`)
                .then(response => response.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">Şehir Seçiniz</option>';
                    data.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    citySelect.innerHTML = '<option value="">Hata oluştu</option>';
                });
        } else {
            citySelect.innerHTML = '<option value="">Önce ülke seçiniz</option>';
        }
    });
    
    // Şehir değiştiğinde alt destinasyonları yükle
    citySelect.addEventListener('change', function() {
        const cityId = this.value;
        subDestinationSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        
        if (cityId) {
            fetch(`/api/destinations/sub-destinations?city_id=${cityId}`)
                .then(response => response.json())
                .then(data => {
                    subDestinationSelect.innerHTML = '<option value="">Alt Destinasyon (Opsiyonel)</option>';
                    data.forEach(sub => {
                        const option = document.createElement('option');
                        option.value = sub.id;
                        option.textContent = sub.name;
                        subDestinationSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    subDestinationSelect.innerHTML = '<option value="">Hata oluştu</option>';
                });
        } else {
            subDestinationSelect.innerHTML = '<option value="">Önce şehir seçiniz (Opsiyonel)</option>';
        }
    });
    
    // Old values için restore
    @if(old('country_id'))
        countrySelect.dispatchEvent(new Event('change'));
        setTimeout(() => {
            citySelect.value = {{ old('city_id', 'null') }};
            if (citySelect.value) {
                citySelect.dispatchEvent(new Event('change'));
                setTimeout(() => {
                    subDestinationSelect.value = {{ old('sub_destination_id', 'null') }};
                }, 100);
            }
        }, 100);
    @endif
});
</script>
@endpush
@endsection 