@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Oteller</h2>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-upload"></i> Excel Import
        </button>
        <a href="{{ route('admin.hotels.export') }}" class="btn btn-info">
            <i class="fas fa-download"></i> Excel Export
        </a>
        <a href="{{ route('admin.hotels.template.download') }}" class="btn btn-secondary">
            <i class="fas fa-file-excel"></i> Template
        </a>
        <a href="{{ route('admin.hotels.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Otel Ekle
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($hotels->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Otel ID</th>
                            <th>Resim</th>
                            <th>Ad</th>
                            <th>Şehir</th>
                            <th>Ülke</th>
                            <th>Yıldız</th>
                            <th>Min Fiyat</th>
                            <th>Tedarikçi</th>
                            <th>Kontratlar</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hotels as $hotel)
                        <tr>
                            <td><strong>{{ $hotel->unique_id }}</strong></td>
                            <td>
                                @if($hotel->image)
                                    @if(str_starts_with($hotel->image, 'http'))
                                        <img src="{{ $hotel->image }}" alt="{{ $hotel->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    @endif
                                @else
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-hotel"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $hotel->name }}</td>
                            <td>{{ $hotel->city }}</td>
                            <td>{{ $hotel->country }}</td>
                            <td>
                                <span class="badge bg-warning">{{ $hotel->stars }} ★</span>
                            </td>
                            <td>{{ number_format($hotel->min_price, 2) }} ₺</td>
                            <td>
                                @if($hotel->supplier)
                                    <span class="badge bg-{{ $hotel->supplier->is_active ? 'info' : 'danger' }}">
                                        {{ $hotel->supplier->name }}
                                        @if(!$hotel->supplier->is_active)
                                            <i class="fas fa-exclamation-triangle ms-1" title="Tedarikçi Pasif - Satış kanalında gösterilmez"></i>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $hotel->contracts->count() }} Kontrat</span>
                            </td>
                            <td>
                                @if($hotel->is_contracted)
                                    <span class="badge bg-success">Kontratlı</span>
                                @else
                                    <span class="badge bg-secondary">Kontratsız</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.hotels.show', $hotel) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.hotels.edit', $hotel) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu oteli silmek istediğinizden emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
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
            
            <div class="d-flex justify-content-center">
                {{ $hotels->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-hotel fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Henüz otel eklenmemiş</h5>
                <p class="text-muted">İlk oteli eklemek için yukarıdaki "Yeni Otel Ekle" butonunu kullanın.</p>
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.hotels.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Excel Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Excel Dosyası *</label>
                        <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                               id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Desteklenen formatlar: .xlsx, .xls, .csv (Maksimum 5MB)</div>
                        @error('excel_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Tedarikçi (Opsiyonel)</label>
                        <select class="form-select" id="supplier_id" name="supplier_id">
                            <option value="">Tedarikçi Seçiniz</option>
                            @foreach(\App\DDD\Modules\Supplier\Domain\Entities\Supplier::where('is_active', true)->get() as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Tedarikçi seçilirse, oteller bu tedarikçiye atanacaktır.</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Excel Formatı</h6>
                        <p class="mb-2">Excel dosyanızda aşağıdaki sütunlar bulunmalıdır:</p>
                        <ul class="mb-0">
                            <li><strong>otel_adi</strong> - Otel adı (zorunlu)</li>
                            <li><strong>sehir</strong> - Şehir (zorunlu)</li>
                            <li><strong>ulke</strong> - Ülke (varsayılan: Turkey)</li>
                            <li><strong>yildiz</strong> - Yıldız sayısı (1-5)</li>
                            <li><strong>adres</strong> - Adres</li>
                            <li><strong>aciklama</strong> - Açıklama</li>
                            <li><strong>min_fiyat</strong> - Minimum fiyat</li>
                            <li><strong>external_id</strong> - Dış ID</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Import Et
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection