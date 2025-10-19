@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sistem Ayarları</h1>
        <div class="text-muted">
            <i class="fas fa-cog"></i> Admin Paneli
        </div>
    </div>

    <!-- Sistem Ayarları -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-server"></i> Genel Sistem Ayarları
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.system.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="system_name" class="form-label">Sistem Adı *</label>
                            <input type="text" class="form-control" id="system_name" name="system_name" 
                                   value="{{ session('system_settings.system_name', 'Motif Travel System') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="system_email" class="form-label">Sistem E-postası *</label>
                            <input type="email" class="form-control" id="system_email" name="system_email" 
                                   value="{{ session('system_settings.system_email', 'admin@motif.com') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="backup_frequency" class="form-label">Yedekleme Sıklığı *</label>
                            <select class="form-select" id="backup_frequency" name="backup_frequency" required>
                                <option value="daily" {{ session('system_settings.backup_frequency') == 'daily' ? 'selected' : '' }}>Günlük</option>
                                <option value="weekly" {{ session('system_settings.backup_frequency') == 'weekly' ? 'selected' : '' }}>Haftalık</option>
                                <option value="monthly" {{ session('system_settings.backup_frequency') == 'monthly' ? 'selected' : '' }}>Aylık</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="session_timeout" class="form-label">Oturum Zaman Aşımı (dakika) *</label>
                            <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                   value="{{ session('system_settings.session_timeout', 120) }}" min="15" max="480" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="max_file_upload_size" class="form-label">Maksimum Dosya Boyutu (MB) *</label>
                            <input type="number" class="form-control" id="max_file_upload_size" name="max_file_upload_size" 
                                   value="{{ session('system_settings.max_file_upload_size', 10) }}" min="1" max="100" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                   {{ session('system_settings.maintenance_mode') ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenance_mode">
                                Bakım Modu
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="debug_mode" name="debug_mode" 
                                   {{ session('system_settings.debug_mode') ? 'checked' : '' }}>
                            <label class="form-check-label" for="debug_mode">
                                Debug Modu
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Sistem Ayarlarını Kaydet
                </button>
            </form>
        </div>
    </div>

    <!-- Firma Ayarları -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-building"></i> Firma Ayarları
            </h6>
        </div>
        <div class="card-body">
            @foreach($firms as $firm)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">{{ $firm->name }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.firm.update', $firm) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="supplier_management_{{ $firm->id }}" 
                                           name="supplier_management_enabled" 
                                           {{ ($firm->settings['supplier_management_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="supplier_management_{{ $firm->id }}">
                                        Tedarikçi Yönetimi Etkin
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="auto_approval_{{ $firm->id }}" 
                                           name="auto_approval_enabled" 
                                           {{ ($firm->settings['auto_approval_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_approval_{{ $firm->id }}">
                                        Otomatik Onay Etkin
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="domestic_search_{{ $firm->id }}" 
                                           name="domestic_search_enabled" 
                                           {{ ($firm->settings['domestic_search_enabled'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="domestic_search_{{ $firm->id }}">
                                        Yurtiçi Arama Etkin
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="international_search_{{ $firm->id }}" 
                                           name="international_search_enabled" 
                                           {{ ($firm->settings['international_search_enabled'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="international_search_{{ $firm->id }}">
                                        Yurtdışı Arama Etkin
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="notification_email_{{ $firm->id }}" class="form-label">Bildirim E-postası</label>
                                    <input type="email" class="form-control" id="notification_email_{{ $firm->id }}" 
                                           name="notification_email" value="{{ $firm->settings['notification_email'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_credit_limit_{{ $firm->id }}" class="form-label">Maksimum Kredi Limiti</label>
                                    <input type="number" class="form-control" id="max_credit_limit_{{ $firm->id }}" 
                                           name="max_credit_limit" value="{{ $firm->settings['max_credit_limit'] ?? '' }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="default_currency_{{ $firm->id }}" class="form-label">Varsayılan Para Birimi *</label>
                                    <select class="form-select" id="default_currency_{{ $firm->id }}" name="default_currency" required>
                                        <option value="TRY" {{ ($firm->settings['default_currency'] ?? 'TRY') == 'TRY' ? 'selected' : '' }}>TRY</option>
                                        <option value="USD" {{ ($firm->settings['default_currency'] ?? 'TRY') == 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ ($firm->settings['default_currency'] ?? 'TRY') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Tedarikçi Ayarları -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-truck"></i> Tedarikçi Ayarları
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tedarikçi</th>
                            <th>Durum</th>
                            <th>Otomatik Senkronizasyon</th>
                            <th>Bildirim</th>
                            <th>Öncelik</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                        <tr>
                            <td>
                                <strong>{{ $supplier->name }}</strong>
                                @if($supplier->group)
                                    <br><small class="text-muted">{{ $supplier->group->name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $supplier->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $supplier->auto_sync_enabled ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $supplier->auto_sync_enabled ? 'Etkin' : 'Devre Dışı' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $supplier->notification_enabled ? 'bg-warning' : 'bg-secondary' }}">
                                    {{ $supplier->notification_enabled ? 'Etkin' : 'Devre Dışı' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $supplier->priority_level ?? 1 }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" 
                                        data-bs-target="#supplierSettingsModal{{ $supplier->id }}">
                                    <i class="fas fa-cog"></i> Ayarlar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tedarikçi Ayarları Modal -->
@foreach($suppliers as $supplier)
<div class="modal fade" id="supplierSettingsModal{{ $supplier->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $supplier->name }} - Ayarlar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.settings.supplier.update', $supplier) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_active_{{ $supplier->id }}" 
                                       name="is_active" {{ $supplier->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active_{{ $supplier->id }}">
                                    Aktif
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="auto_sync_{{ $supplier->id }}" 
                                       name="auto_sync_enabled" {{ $supplier->auto_sync_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto_sync_{{ $supplier->id }}">
                                    Otomatik Senkronizasyon
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="notification_{{ $supplier->id }}" 
                                       name="notification_enabled" {{ $supplier->notification_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="notification_{{ $supplier->id }}">
                                    Bildirim Etkin
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority_{{ $supplier->id }}" class="form-label">Öncelik Seviyesi</label>
                                <select class="form-select" id="priority_{{ $supplier->id }}" name="priority_level">
                                    <option value="1" {{ ($supplier->priority_level ?? 1) == 1 ? 'selected' : '' }}>1 - En Düşük</option>
                                    <option value="2" {{ ($supplier->priority_level ?? 1) == 2 ? 'selected' : '' }}>2 - Düşük</option>
                                    <option value="3" {{ ($supplier->priority_level ?? 1) == 3 ? 'selected' : '' }}>3 - Orta</option>
                                    <option value="4" {{ ($supplier->priority_level ?? 1) == 4 ? 'selected' : '' }}>4 - Yüksek</option>
                                    <option value="5" {{ ($supplier->priority_level ?? 1) == 5 ? 'selected' : '' }}>5 - En Yüksek</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sync_frequency_{{ $supplier->id }}" class="form-label">Senkronizasyon Sıklığı (dakika)</label>
                                <input type="number" class="form-control" id="sync_frequency_{{ $supplier->id }}" 
                                       name="sync_frequency" value="{{ $supplier->sync_frequency ?? 60 }}" min="1" max="1440">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_daily_{{ $supplier->id }}" class="form-label">Günlük Maksimum Rezervasyon</label>
                                <input type="number" class="form-control" id="max_daily_{{ $supplier->id }}" 
                                       name="max_daily_bookings" value="{{ $supplier->max_daily_bookings ?? '' }}" min="1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
