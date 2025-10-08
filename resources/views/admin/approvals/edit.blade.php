@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Onay Senaryosu Düzenle</h1>
        <div>
            <a href="{{ route('admin.approvals.show', $scenario) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Görüntüle
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Onay Senaryosu Bilgileri</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.approvals.update', $scenario) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Senaryo Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $scenario->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="firm_id" class="form-label">Firma *</label>
                            <select class="form-select @error('firm_id') is-invalid @enderror" id="firm_id" name="firm_id" required>
                                <option value="">Firma Seçin</option>
                                @foreach($firms as $firm)
                                    <option value="{{ $firm->id }}" {{ old('firm_id', $scenario->firm_id) == $firm->id ? 'selected' : '' }}>
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
                              id="description" name="description" rows="3">{{ old('description', $scenario->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="approval_type" class="form-label">Onay Tipi *</label>
                            <select class="form-select @error('approval_type') is-invalid @enderror" id="approval_type" name="approval_type" required>
                                <option value="single" {{ old('approval_type', $scenario->approval_type) == 'single' ? 'selected' : '' }}>Tek Onay</option>
                                <option value="multi_step" {{ old('approval_type', $scenario->approval_type) == 'multi_step' ? 'selected' : '' }}>Çok Aşamalı</option>
                                <option value="parallel" {{ old('approval_type', $scenario->approval_type) == 'parallel' ? 'selected' : '' }}>Paralel</option>
                            </select>
                            @error('approval_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="max_approval_days" class="form-label">Maksimum Onay Günü *</label>
                            <input type="number" class="form-control @error('max_approval_days') is-invalid @enderror" 
                                   id="max_approval_days" name="max_approval_days" 
                                   value="{{ old('max_approval_days', $scenario->max_approval_days) }}" 
                                   min="1" max="30" required>
                            @error('max_approval_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="require_all_approvers" name="require_all_approvers" 
                                       {{ old('require_all_approvers', $scenario->require_all_approvers) ? 'checked' : '' }}>
                                <label class="form-check-label" for="require_all_approvers">
                                    Tüm Onaylayıcılar Gerekli
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active', $scenario->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Aktif</strong>
                                </label>
                                <small class="form-text text-muted d-block">
                                    Pasif senaryolar rezervasyonlarda kullanılmaz
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Not:</strong> Onaylayıcıları ve kuralları düzenlemek için senaryoyu görüntüleyin veya ilgili bölümleri kullanın.
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.approvals.show', $scenario) }}" class="btn btn-outline-primary">
                        <i class="fas fa-users"></i> Onaylayıcıları Yönet
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mevcut Onaylayıcılar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mevcut Onaylayıcılar</h6>
        </div>
        <div class="card-body">
            @if($scenario->approvers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sıra</th>
                                <th>Onaylayıcı</th>
                                <th>Onay Tipi</th>
                                <th>Geçersiz Kılabilir</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scenario->approvers->sortBy('step_order') as $approver)
                            <tr>
                                <td>{{ $approver->step_order }}</td>
                                <td>
                                    <strong>{{ $approver->user->name }}</strong><br>
                                    <small class="text-muted">{{ $approver->user->email }}</small>
                                </td>
                                <td>
                                    @if($approver->approval_type === 'required')
                                        <span class="badge bg-danger">Zorunlu</span>
                                    @elseif($approver->approval_type === 'optional')
                                        <span class="badge bg-info">İsteğe Bağlı</span>
                                    @else
                                        <span class="badge bg-warning">Yedek</span>
                                    @endif
                                </td>
                                <td>
                                    @if($approver->can_override)
                                        <span class="badge bg-success">Evet</span>
                                    @else
                                        <span class="badge bg-secondary">Hayır</span>
                                    @endif
                                </td>
                                <td>
                                    @if($approver->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-3">
                    <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                    <p class="text-muted">Henüz onaylayıcı eklenmemiş</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Mevcut Kurallar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Mevcut Kurallar</h6>
            <a href="{{ route('admin.approvals.rules', $scenario) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-cog"></i> Kuralları Yönet
            </a>
        </div>
        <div class="card-body">
            @if($scenario->rules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Öncelik</th>
                                <th>Kural Tipi</th>
                                <th>Alan Adı</th>
                                <th>Operatör</th>
                                <th>Değer</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scenario->rules->sortBy('priority') as $rule)
                            <tr>
                                <td>{{ $rule->priority }}</td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $rule->rule_type)) }}</span>
                                </td>
                                <td>{{ $rule->field_name }}</td>
                                <td>
                                    <code>{{ $rule->operator }}</code>
                                </td>
                                <td>{{ is_array($rule->value) ? json_encode($rule->value) : $rule->value }}</td>
                                <td>
                                    @if($rule->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-3">
                    <i class="fas fa-cog fa-2x text-muted mb-2"></i>
                    <p class="text-muted">Henüz kural eklenmemiş</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validasyonu
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const maxDays = document.getElementById('max_approval_days').value;
        if (maxDays < 1 || maxDays > 30) {
            e.preventDefault();
            alert('Maksimum onay günü 1 ile 30 arasında olmalıdır.');
        }
    });
});
</script>
@endsection

