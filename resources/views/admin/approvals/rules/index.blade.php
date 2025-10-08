@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Onay Kuralları: {{ $scenario->name }}</h1>
        <div>
            <a href="{{ route('admin.approvals.show', $scenario) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Senaryoyu Görüntüle
            </a>
            <a href="{{ route('admin.approvals.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>
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

    <!-- Yeni Kural Ekle -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Yeni Kural Ekle</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.approvals.rules.store', $scenario) }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="rule_type" class="form-label">Kural Tipi *</label>
                            <select class="form-select @error('rule_type') is-invalid @enderror" 
                                    id="rule_type" name="rule_type" required>
                                <option value="">Seçin</option>
                                <option value="price_range" {{ old('rule_type') == 'price_range' ? 'selected' : '' }}>
                                    Fiyat Aralığı
                                </option>
                                <option value="destination" {{ old('rule_type') == 'destination' ? 'selected' : '' }}>
                                    Destinasyon
                                </option>
                                <option value="duration" {{ old('rule_type') == 'duration' ? 'selected' : '' }}>
                                    Süre
                                </option>
                                <option value="amount" {{ old('rule_type') == 'amount' ? 'selected' : '' }}>
                                    Tutar
                                </option>
                                <option value="custom" {{ old('rule_type') == 'custom' ? 'selected' : '' }}>
                                    Özel
                                </option>
                            </select>
                            @error('rule_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="field_name" class="form-label">Alan Adı *</label>
                            <input type="text" class="form-control @error('field_name') is-invalid @enderror" 
                                   id="field_name" name="field_name" value="{{ old('field_name') }}" 
                                   placeholder="total_price" required>
                            <small class="form-text text-muted">Örn: total_price, destination, duration_days</small>
                            @error('field_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="operator" class="form-label">Operatör *</label>
                            <select class="form-select @error('operator') is-invalid @enderror" 
                                    id="operator" name="operator" required>
                                <option value="equals" {{ old('operator') == 'equals' ? 'selected' : '' }}>
                                    Eşittir (=)
                                </option>
                                <option value="not_equals" {{ old('operator') == 'not_equals' ? 'selected' : '' }}>
                                    Eşit Değil (≠)
                                </option>
                                <option value="greater_than" {{ old('operator') == 'greater_than' ? 'selected' : '' }}>
                                    Büyüktür (&gt;)
                                </option>
                                <option value="less_than" {{ old('operator') == 'less_than' ? 'selected' : '' }}>
                                    Küçüktür (&lt;)
                                </option>
                                <option value="between" {{ old('operator') == 'between' ? 'selected' : '' }}>
                                    Arasında
                                </option>
                                <option value="in" {{ old('operator') == 'in' ? 'selected' : '' }}>
                                    İçinde
                                </option>
                                <option value="not_in" {{ old('operator') == 'not_in' ? 'selected' : '' }}>
                                    İçinde Değil
                                </option>
                            </select>
                            @error('operator')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="value" class="form-label">Değer *</label>
                            <input type="text" class="form-control @error('value') is-invalid @enderror" 
                                   id="value" name="value" value="{{ old('value') }}" 
                                   placeholder="1000" required>
                            <small class="form-text text-muted">JSON için array kullanın</small>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="priority" class="form-label">Öncelik *</label>
                            <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                   id="priority" name="priority" value="{{ old('priority', 0) }}" 
                                   min="0" required>
                            <small class="form-text text-muted">0 = En yüksek</small>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" 
                                   name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                    <div class="col-md-10 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Kural Ekle
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Mevcut Kurallar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mevcut Kurallar</h6>
        </div>
        <div class="card-body">
            @if($scenario->rules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="80">Öncelik</th>
                                <th>Kural Tipi</th>
                                <th>Alan Adı</th>
                                <th>Operatör</th>
                                <th>Değer</th>
                                <th>Durum</th>
                                <th width="120">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scenario->rules->sortBy('priority') as $rule)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $rule->priority }}</span>
                                </td>
                                <td>
                                    @if($rule->rule_type === 'price_range')
                                        <span class="badge bg-success">Fiyat Aralığı</span>
                                    @elseif($rule->rule_type === 'destination')
                                        <span class="badge bg-info">Destinasyon</span>
                                    @elseif($rule->rule_type === 'duration')
                                        <span class="badge bg-warning">Süre</span>
                                    @elseif($rule->rule_type === 'amount')
                                        <span class="badge bg-primary">Tutar</span>
                                    @else
                                        <span class="badge bg-secondary">Özel</span>
                                    @endif
                                </td>
                                <td><code>{{ $rule->field_name }}</code></td>
                                <td>
                                    @if($rule->operator === 'equals')
                                        <span class="badge bg-secondary">=</span> Eşittir
                                    @elseif($rule->operator === 'not_equals')
                                        <span class="badge bg-secondary">≠</span> Eşit Değil
                                    @elseif($rule->operator === 'greater_than')
                                        <span class="badge bg-secondary">&gt;</span> Büyüktür
                                    @elseif($rule->operator === 'less_than')
                                        <span class="badge bg-secondary">&lt;</span> Küçüktür
                                    @elseif($rule->operator === 'between')
                                        <span class="badge bg-secondary">↔</span> Arasında
                                    @elseif($rule->operator === 'in')
                                        <span class="badge bg-secondary">∈</span> İçinde
                                    @else
                                        <span class="badge bg-secondary">∉</span> İçinde Değil
                                    @endif
                                </td>
                                <td>
                                    @if(is_array($rule->value))
                                        <code>{{ json_encode($rule->value) }}</code>
                                    @else
                                        <strong>{{ $rule->value }}</strong>
                                    @endif
                                </td>
                                <td>
                                    @if($rule->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                onclick="editRule({{ $rule->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.approvals.rules.destroy', [$scenario, $rule]) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Bu kuralı silmek istediğinizden emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz kural eklenmemiş</h5>
                    <p class="text-muted">Yukarıdaki formu kullanarak yeni kurallar ekleyebilirsiniz.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Kural Açıklamaları -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kural Tipleri Açıklaması</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h6><span class="badge bg-success">Fiyat Aralığı</span></h6>
                    <p class="small text-muted">
                        Rezervasyon toplam fiyatına göre onay kontrolü yapar.
                        <br><strong>Örnek:</strong> total_price > 5000
                    </p>
                </div>
                <div class="col-md-4">
                    <h6><span class="badge bg-info">Destinasyon</span></h6>
                    <p class="small text-muted">
                        Otel veya destinasyon bilgisine göre kontrol yapar.
                        <br><strong>Örnek:</strong> destination in ['Antalya', 'İstanbul']
                    </p>
                </div>
                <div class="col-md-4">
                    <h6><span class="badge bg-warning">Süre</span></h6>
                    <p class="small text-muted">
                        Rezervasyon süresine göre kontrol yapar.
                        <br><strong>Örnek:</strong> duration_days > 7
                    </p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <h6><span class="badge bg-primary">Tutar</span></h6>
                    <p class="small text-muted">
                        Herhangi bir tutar alanına göre kontrol yapar.
                        <br><strong>Örnek:</strong> commission between [100, 1000]
                    </p>
                </div>
                <div class="col-md-4">
                    <h6><span class="badge bg-secondary">Özel</span></h6>
                    <p class="small text-muted">
                        Özel alanlar için kullanılır. Alan adını kendiniz belirleyin.
                        <br><strong>Örnek:</strong> custom_field equals 'value'
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editRule(ruleId) {
    // Bu fonksiyon ileride implement edilebilir
    alert('Kural düzenleme özelliği yakında eklenecek. Şimdilik silip yeniden oluşturabilirsiniz.');
}
</script>
@endsection

