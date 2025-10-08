@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kar Kuralını Düzenle</h1>
        <a href="{{ route('admin.profits.show', $profitRule) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6>Validation Hataları:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kar Kuralı Bilgileri</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profits.update', $profitRule) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Kural Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $profitRule->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="priority" class="form-label">Öncelik *</label>
                            <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                   id="priority" name="priority" value="{{ old('priority', $profitRule->priority) }}" min="0" required>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $profitRule->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="firm_id" class="form-label">Firma</label>
                            <select class="form-select @error('firm_id') is-invalid @enderror" id="firm_id" name="firm_id">
                                <option value="">Tüm Firmalar</option>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id', $profitRule->firm_id) == $firm->id ? 'selected' : '' }}>
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
                            <label for="supplier_id" class="form-label">Tedarikçi</label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id">
                                <option value="">Tüm Tedarikçiler</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $profitRule->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="destination" class="form-label">Destinasyon</label>
                            <input type="text" class="form-control @error('destination') is-invalid @enderror" 
                                   id="destination" name="destination" value="{{ old('destination', $profitRule->destination) }}" 
                                   placeholder="Örn: İstanbul, Antalya">
                            @error('destination')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="trip_type" class="form-label">Seyahat Tipi *</label>
                            <select class="form-select @error('trip_type') is-invalid @enderror" id="trip_type" name="trip_type" required>
                                <option value="domestic" {{ old('trip_type', $profitRule->trip_type) == 'domestic' ? 'selected' : '' }}>Yurtiçi</option>
                                <option value="international" {{ old('trip_type', $profitRule->trip_type) == 'international' ? 'selected' : '' }}>Yurtdışı</option>
                            </select>
                            @error('trip_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="travel_type" class="form-label">Yolculuk Tipi *</label>
                            <select class="form-select @error('travel_type') is-invalid @enderror" id="travel_type" name="travel_type" required>
                                <option value="one_way" {{ old('travel_type', $profitRule->travel_type) == 'one_way' ? 'selected' : '' }}>Tek Yön</option>
                                <option value="round_trip" {{ old('travel_type', $profitRule->travel_type) == 'round_trip' ? 'selected' : '' }}>Gidiş-Dönüş</option>
                            </select>
                            @error('travel_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fee_type" class="form-label">Ücret Tipi *</label>
                            <select class="form-select @error('fee_type') is-invalid @enderror" id="fee_type" name="fee_type" required>
                                <option value="percentage" {{ old('fee_type', $profitRule->fee_type) == 'percentage' ? 'selected' : '' }}>Yüzde</option>
                                <option value="fixed" {{ old('fee_type', $profitRule->fee_type) == 'fixed' ? 'selected' : '' }}>Sabit</option>
                                <option value="tiered" {{ old('fee_type', $profitRule->fee_type) == 'tiered' ? 'selected' : '' }}>Katmanlı</option>
                            </select>
                            @error('fee_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fee_value" class="form-label">Ücret Değeri *</label>
                            <input type="number" step="0.01" class="form-control @error('fee_value') is-invalid @enderror" 
                                   id="fee_value" name="fee_value" value="{{ old('fee_value', $profitRule->fee_value) }}" min="0" required>
                            @error('fee_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="min_fee" class="form-label">Minimum Ücret</label>
                            <input type="number" step="0.01" class="form-control @error('min_fee') is-invalid @enderror" 
                                   id="min_fee" name="min_fee" value="{{ old('min_fee', $profitRule->min_fee) }}" min="0">
                            @error('min_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_fee" class="form-label">Maksimum Ücret</label>
                            <input type="number" step="0.01" class="form-control @error('max_fee') is-invalid @enderror" 
                                   id="max_fee" name="max_fee" value="{{ old('max_fee', $profitRule->max_fee) }}" min="0">
                            @error('max_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active', $profitRule->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 