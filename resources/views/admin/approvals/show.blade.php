@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Onay Senaryosu Detayları</h1>
        <div>
            <a href="{{ route('admin.approvals.edit', $scenario) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Düzenle
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

    <!-- Senaryo Bilgileri -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Senaryo Bilgileri</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="200"><strong>Senaryo Adı:</strong></td>
                            <td>{{ $scenario->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Firma:</strong></td>
                            <td>
                                <a href="{{ route('admin.firms.show', $scenario->firm) }}">
                                    {{ $scenario->firm->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Açıklama:</strong></td>
                            <td>{{ $scenario->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Onay Tipi:</strong></td>
                            <td>
                                @if($scenario->approval_type === 'single')
                                    <span class="badge bg-info">Tek Onay</span>
                                    <small class="text-muted d-block mt-1">Bir kişinin onayı yeterlidir</small>
                                @elseif($scenario->approval_type === 'multi_step')
                                    <span class="badge bg-warning">Çok Aşamalı</span>
                                    <small class="text-muted d-block mt-1">Onaylar sırayla yapılmalıdır</small>
                                @else
                                    <span class="badge bg-success">Paralel</span>
                                    <small class="text-muted d-block mt-1">Tüm onaylar aynı anda yapılabilir</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Maksimum Onay Süresi:</strong></td>
                            <td>{{ $scenario->max_approval_days }} gün</td>
                        </tr>
                        <tr>
                            <td><strong>Tüm Onaylayıcılar Gerekli:</strong></td>
                            <td>
                                @if($scenario->require_all_approvers)
                                    <span class="badge bg-danger">Evet</span>
                                    <small class="text-muted d-block mt-1">Tüm onaylayıcıların onayı gereklidir</small>
                                @else
                                    <span class="badge bg-success">Hayır</span>
                                    <small class="text-muted d-block mt-1">Belirli sayıda onay yeterlidir</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Durum:</strong></td>
                            <td>
                                @if($scenario->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Oluşturulma:</strong></td>
                            <td>{{ $scenario->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Son Güncelleme:</strong></td>
                            <td>{{ $scenario->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- İstatistikler -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">İstatistikler</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Toplam Onaylayıcı</span>
                            <span class="badge bg-primary">{{ $scenario->approvers->count() }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Aktif Onaylayıcı</span>
                            <span class="badge bg-success">{{ $scenario->approvers->where('is_active', true)->count() }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Toplam Kural</span>
                            <span class="badge bg-info">{{ $scenario->rules->count() }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Aktif Kural</span>
                            <span class="badge bg-success">{{ $scenario->rules->where('is_active', true)->count() }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Toplam İstek</span>
                            <span class="badge bg-secondary">{{ $scenario->requests->count() }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Bekleyen İstek</span>
                            <span class="badge bg-warning">{{ $scenario->requests->where('status', 'pending')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hızlı İşlemler</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.approvals.edit', $scenario) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Senaryoyu Düzenle
                        </a>
                        <a href="{{ route('admin.approvals.rules', $scenario) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-cog"></i> Kuralları Yönet
                        </a>
                        <form action="{{ route('admin.approvals.destroy', $scenario) }}" method="POST" onsubmit="return confirm('Bu senaryoyu silmek istediğinizden emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-trash"></i> Senaryoyu Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Onaylayıcılar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Onaylayıcılar</h6>
        </div>
        <div class="card-body">
            @if($scenario->approvers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="80">Sıra</th>
                                <th>Onaylayıcı</th>
                                <th>E-posta</th>
                                <th>Onay Tipi</th>
                                <th>Geçersiz Kılabilir</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scenario->approvers->sortBy('step_order') as $approver)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $approver->step_order }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.show', $approver->user) }}">
                                        <strong>{{ $approver->user->name }}</strong>
                                    </a>
                                </td>
                                <td>{{ $approver->user->email }}</td>
                                <td>
                                    @if($approver->approval_type === 'required')
                                        <span class="badge bg-danger">Zorunlu</span>
                                    @elseif($approver->approval_type === 'optional')
                                        <span class="badge bg-info">İsteğe Bağlı</span>
                                    @else
                                        <span class="badge bg-warning">Yedek</span>
                                    @endif
                                </td>
                                <td class="text-center">
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
                <div class="text-center py-4">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz onaylayıcı eklenmemiş</h5>
                    <p class="text-muted">Onaylayıcı eklemek için senaryoyu düzenleyin.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Kurallar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Onay Kuralları</h6>
            <a href="{{ route('admin.approvals.rules', $scenario) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Kural Ekle
            </a>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scenario->rules->sortBy('priority') as $rule)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $rule->priority }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $rule->rule_type)) }}</span>
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz kural eklenmemiş</h5>
                    <p class="text-muted">Bu senaryoya onay kuralları eklemek için yukarıdaki butonu kullanın.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Son Onay İstekleri -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Son Onay İstekleri</h6>
        </div>
        <div class="card-body">
            @if($scenario->requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İstek Yapan</th>
                                <th>İstek Tarihi</th>
                                <th>Durum</th>
                                <th>Onaylayan</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scenario->requests->take(10) as $request)
                            <tr>
                                <td>#{{ $request->id }}</td>
                                <td>{{ $request->requestedBy->name }}</td>
                                <td>{{ $request->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    @if($request->status === 'pending')
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @elseif($request->status === 'approved')
                                        <span class="badge bg-success">Onaylandı</span>
                                    @elseif($request->status === 'rejected')
                                        <span class="badge bg-danger">Reddedildi</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($request->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $request->approvedBy?->name ?? '-' }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($scenario->requests->count() > 10)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.approvals.requests') }}" class="btn btn-outline-primary">
                            Tüm İstekleri Görüntüle
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz onay isteği bulunmuyor</h5>
                    <p class="text-muted">Bu senaryoya ait onay istekleri burada görünecektir.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

