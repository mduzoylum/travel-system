@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tedarikçi Grubu Düzenle</h2>
    <a href="{{ route('admin.supplier-groups.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Geri Dön
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.supplier-groups.update', $supplierGroup) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Grup Adı *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $supplierGroup->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="color" class="form-label">Renk *</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                   id="color" name="color" value="{{ old('color', $supplierGroup->color) }}" 
                                   title="Renk seçin" style="width: 60px;">
                            <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                   id="color_text" value="{{ old('color', $supplierGroup->color) }}" 
                                   placeholder="#007bff" readonly>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Hızlı seçim:</small>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                <span class="badge" style="background-color: #007bff; cursor: pointer;" onclick="setColor('#007bff')">Mavi</span>
                                <span class="badge" style="background-color: #28a745; cursor: pointer;" onclick="setColor('#28a745')">Yeşil</span>
                                <span class="badge" style="background-color: #dc3545; cursor: pointer;" onclick="setColor('#dc3545')">Kırmızı</span>
                                <span class="badge" style="background-color: #ffc107; cursor: pointer;" onclick="setColor('#ffc107')">Sarı</span>
                                <span class="badge" style="background-color: #6f42c1; cursor: pointer;" onclick="setColor('#6f42c1')">Mor</span>
                                <span class="badge" style="background-color: #fd7e14; cursor: pointer;" onclick="setColor('#fd7e14')">Turuncu</span>
                                <span class="badge" style="background-color: #20c997; cursor: pointer;" onclick="setColor('#20c997')">Turkuaz</span>
                                <span class="badge" style="background-color: #6c757d; cursor: pointer;" onclick="setColor('#6c757d')">Gri</span>
                            </div>
                        </div>
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="group_type" class="form-label">Grup Tipi *</label>
                        <select class="form-select @error('group_type') is-invalid @enderror" 
                                id="group_type" name="group_type" required>
                            <option value="">Grup Tipi Seçin</option>
                            <option value="report" {{ old('group_type', $supplierGroup->group_type) == 'report' ? 'selected' : '' }}>Rapor Grubu</option>
                            <option value="profit" {{ old('group_type', $supplierGroup->group_type) == 'profit' ? 'selected' : '' }}>Kar Grubu</option>
                            <option value="xml" {{ old('group_type', $supplierGroup->group_type) == 'xml' ? 'selected' : '' }}>XML Tedarikçi</option>
                            <option value="manual" {{ old('group_type', $supplierGroup->group_type) == 'manual' ? 'selected' : '' }}>Manuel Tedarikçi</option>
                        </select>
                        <small class="form-text text-muted">
                            Rapor: Çoklu atama | Kar: Tek atama | XML/Manuel: Tedarikçi tipi
                        </small>
                        @error('group_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sıra</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $supplierGroup->sort_order) }}" min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $supplierGroup->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                           {{ old('is_active', $supplierGroup->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Aktif
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

@push('scripts')
<script>
function setColor(color) {
    document.getElementById('color').value = color;
    document.getElementById('color_text').value = color;
}

// Color picker değiştiğinde text input'u güncelle
document.getElementById('color').addEventListener('change', function() {
    document.getElementById('color_text').value = this.value;
});
</script>
@endpush
@endsection

