@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Yeni Onay Senaryosu</h1>
        <a href="{{ route('admin.approvals.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Onay Senaryosu Bilgileri</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.approvals.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Senaryo Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
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
                            <label for="approval_type" class="form-label">Onay Tipi *</label>
                            <select class="form-select @error('approval_type') is-invalid @enderror" id="approval_type" name="approval_type" required>
                                <option value="single" {{ old('approval_type') == 'single' ? 'selected' : '' }}>Tek Onay</option>
                                <option value="multi_step" {{ old('approval_type') == 'multi_step' ? 'selected' : '' }}>Çok Aşamalı</option>
                                <option value="parallel" {{ old('approval_type') == 'parallel' ? 'selected' : '' }}>Paralel</option>
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
                                   id="max_approval_days" name="max_approval_days" value="{{ old('max_approval_days', 7) }}" min="1" max="30" required>
                            @error('max_approval_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="require_all_approvers" name="require_all_approvers" 
                                       {{ old('require_all_approvers') ? 'checked' : '' }}>
                                <label class="form-check-label" for="require_all_approvers">
                                    Tüm Onaylayıcılar Gerekli
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <h5>Onaylayıcılar</h5>
                <div id="approvers-container">
                    <div class="approver-row row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Onaylayıcı *</label>
                            <select class="form-select" name="approvers[0][user_id]" required>
                                <option value="">Kullanıcı Seçin</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sıra *</label>
                            <input type="number" class="form-control" name="approvers[0][step_order]" value="1" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Onay Tipi *</label>
                            <select class="form-select" name="approvers[0][approval_type]" required>
                                <option value="required">Zorunlu</option>
                                <option value="optional">İsteğe Bağlı</option>
                                <option value="backup">Yedek</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="approvers[0][can_override]">
                                <label class="form-check-label">
                                    Geçersiz Kılabilir
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addApprover()">
                        <i class="fas fa-plus"></i> Onaylayıcı Ekle
                    </button>
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

<script>
let approverIndex = 1;

function addApprover() {
    const container = document.getElementById('approvers-container');
    const newRow = document.createElement('div');
    newRow.className = 'approver-row row mb-3';
    newRow.innerHTML = `
        <div class="col-md-3">
            <label class="form-label">Onaylayıcı *</label>
            <select class="form-select" name="approvers[${approverIndex}][user_id]" required>
                <option value="">Kullanıcı Seçin</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Sıra *</label>
            <input type="number" class="form-control" name="approvers[${approverIndex}][step_order]" value="${approverIndex + 1}" min="1" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Onay Tipi *</label>
            <select class="form-select" name="approvers[${approverIndex}][approval_type]" required>
                <option value="required">Zorunlu</option>
                <option value="optional">İsteğe Bağlı</option>
                <option value="backup">Yedek</option>
            </select>
        </div>
        <div class="col-md-2">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" name="approvers[${approverIndex}][can_override]">
                <label class="form-check-label">
                    Geçersiz Kılabilir
                </label>
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-danger mt-4" onclick="removeApprover(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    approverIndex++;
}

function removeApprover(button) {
    button.closest('.approver-row').remove();
}
</script>
@endsection 