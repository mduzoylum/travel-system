@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Yeni Servis Ücreti</h1>
        <a href="{{ route('admin.profits.service-fees') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Servis Ücreti Bilgileri</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profits.service-fees.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Ücret Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="firm_id" class="form-label">Firma</label>
                            <select class="form-select @error('firm_id') is-invalid @enderror" id="firm_id" name="firm_id">
                                <option value="">Tüm Firmalar</option>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>
                                        {{ $firm->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('firm_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="service_type" class="form-label">Servis Tipi *</label>
                            <select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
                                <option value="reservation" {{ old('service_type') == 'reservation' ? 'selected' : '' }}>Rezervasyon</option>
                                <option value="cancellation" {{ old('service_type') == 'cancellation' ? 'selected' : '' }}>İptal</option>
                                <option value="modification" {{ old('service_type') == 'modification' ? 'selected' : '' }}>Değişiklik</option>
                                <option value="booking" {{ old('service_type') == 'booking' ? 'selected' : '' }}>Rezervasyon</option>
                            </select>
                            @error('service_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="product_type" class="form-label">Ürün Tipi *</label>
                            <select class="form-select @error('product_type') is-invalid @enderror" id="product_type" name="product_type" required>
                                <option value="all" {{ old('product_type') == 'all' ? 'selected' : '' }}>Tüm Ürünler</option>
                                <option value="hotel" {{ old('product_type') == 'hotel' ? 'selected' : '' }}>Otel</option>
                                <option value="flight" {{ old('product_type') == 'flight' ? 'selected' : '' }}>Uçak</option>
                                <option value="car" {{ old('product_type') == 'car' ? 'selected' : '' }}>Araç Kiralama</option>
                                <option value="activity" {{ old('product_type') == 'activity' ? 'selected' : '' }}>Aktivite</option>
                                <option value="transfer" {{ old('product_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                            @error('product_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fee_type" class="form-label">Ücret Tipi *</label>
                            <select class="form-select @error('fee_type') is-invalid @enderror" id="fee_type" name="fee_type" required>
                                <option value="percentage" {{ old('fee_type') == 'percentage' ? 'selected' : '' }}>Yüzde</option>
                                <option value="fixed" {{ old('fee_type') == 'fixed' ? 'selected' : '' }}>Sabit</option>
                            </select>
                            @error('fee_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fee_value" class="form-label">Ücret Değeri *</label>
                            <input type="number" step="0.01" class="form-control @error('fee_value') is-invalid @enderror" 
                                   id="fee_value" name="fee_value" value="{{ old('fee_value') }}" min="0" required>
                            @error('fee_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="min_amount" class="form-label">Minimum Tutar</label>
                            <input type="number" step="0.01" class="form-control @error('min_amount') is-invalid @enderror" 
                                   id="min_amount" name="min_amount" value="{{ old('min_amount') }}" min="0">
                            @error('min_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="max_amount" class="form-label">Maksimum Tutar</label>
                            <input type="number" step="0.01" class="form-control @error('max_amount') is-invalid @enderror" 
                                   id="max_amount" name="max_amount" value="{{ old('max_amount') }}" min="0">
                            @error('max_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="priority" class="form-label">Öncelik</label>
                            <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                   id="priority" name="priority" value="{{ old('priority', 0) }}" min="0">
                            <small class="form-text text-muted">Yüksek öncelik değeri önce uygulanır</small>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="is_mandatory" name="is_mandatory" 
                                       {{ old('is_mandatory') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_mandatory">
                                    Zorunlu
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 