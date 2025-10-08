@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kar Raporları</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rapor Oluştur</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profits.reports.generate') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-4">
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
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Başlangıç Tarihi *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Bitiş Tarihi *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-chart-bar"></i> Rapor Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hızlı Raporlar -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Bu Ay Toplam Kar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₺0.00</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Bu Ay Ortalama Kar %
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">%0.0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Bu Ay İşlem Sayısı
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Aktif Firmalar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $firms->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Örnek Raporlar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Örnek Raporlar</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Aylık Kar Raporu</h6>
                            <p class="card-text">Bu ay için tüm firmaların kar performansını görüntüleyin.</p>
                            <button type="button" class="btn btn-sm btn-primary" onclick="generateMonthlyReport()">
                                <i class="fas fa-chart-line"></i> Görüntüle
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Tedarikçi Bazlı Rapor</h6>
                            <p class="card-text">Tedarikçilere göre kar dağılımını analiz edin.</p>
                            <button type="button" class="btn btn-sm btn-primary" onclick="generateSupplierReport()">
                                <i class="fas fa-chart-pie"></i> Görüntüle
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateMonthlyReport() {
    // Bu ay için otomatik tarih aralığı belirle
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    // Form alanlarını otomatik doldur
    document.getElementById('start_date').value = firstDay.toISOString().split('T')[0];
    document.getElementById('end_date').value = lastDay.toISOString().split('T')[0];
    
    // İlk firmayı seç (eğer varsa)
    const firmSelect = document.getElementById('firm_id');
    if (firmSelect.options.length > 1) {
        firmSelect.selectedIndex = 1; // İlk firma (index 0 boş seçenek)
    }
    
    // CSRF token'ını al ve formu oluştur
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.profits.reports.generate") }}';
    
    // CSRF token ekle
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Form verilerini ekle
    const firmId = document.createElement('input');
    firmId.type = 'hidden';
    firmId.name = 'firm_id';
    firmId.value = firmSelect.value;
    form.appendChild(firmId);
    
    const startDate = document.createElement('input');
    startDate.type = 'hidden';
    startDate.name = 'start_date';
    startDate.value = firstDay.toISOString().split('T')[0];
    form.appendChild(startDate);
    
    const endDate = document.createElement('input');
    endDate.type = 'hidden';
    endDate.name = 'end_date';
    endDate.value = lastDay.toISOString().split('T')[0];
    form.appendChild(endDate);
    
    // Formu sayfaya ekle ve gönder
    document.body.appendChild(form);
    form.submit();
}

function generateSupplierReport() {
    // Son 30 gün için otomatik tarih aralığı belirle
    const now = new Date();
    const thirtyDaysAgo = new Date(now.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    // İlk firmayı seç (eğer varsa)
    const firmSelect = document.getElementById('firm_id');
    if (firmSelect.options.length > 1) {
        firmSelect.selectedIndex = 1; // İlk firma (index 0 boş seçenek)
    }
    
    // CSRF token'ını al ve formu oluştur
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.profits.reports.generate") }}';
    
    // CSRF token ekle
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Form verilerini ekle
    const firmId = document.createElement('input');
    firmId.type = 'hidden';
    firmId.name = 'firm_id';
    firmId.value = firmSelect.value;
    form.appendChild(firmId);
    
    const startDate = document.createElement('input');
    startDate.type = 'hidden';
    startDate.name = 'start_date';
    startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
    form.appendChild(startDate);
    
    const endDate = document.createElement('input');
    endDate.type = 'hidden';
    endDate.name = 'end_date';
    endDate.value = now.toISOString().split('T')[0];
    form.appendChild(endDate);
    
    // Formu sayfaya ekle ve gönder
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection 