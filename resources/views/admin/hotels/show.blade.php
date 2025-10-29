@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Otel Detayı</h2>
    <div>
        <a href="{{ route('admin.hotels.edit', $hotel) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <a href="{{ route('admin.hotels.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Otel Bilgileri</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Otel Adı:</th>
                                <td>{{ $hotel->name }}</td>
                            </tr>
                            <tr>
                                <th>Şehir:</th>
                                <td>{{ $hotel->city }}</td>
                            </tr>
                            <tr>
                                <th>Ülke:</th>
                                <td>{{ $hotel->country }}</td>
                            </tr>
                            <tr>
                                <th>Yıldız:</th>
                                <td>
                                    <span class="badge bg-warning">{{ $hotel->stars }} ★</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Min Fiyat:</th>
                                <td>{{ number_format($hotel->min_price, 2) }} ₺</td>
                            </tr>
                            <tr>
                                <th>Tedarikçi:</th>
                                <td>
                                    @if($hotel->supplier)
                                        <span class="badge bg-info">{{ $hotel->supplier->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Durum:</th>
                                <td>
                                    @if($hotel->is_contracted)
                                        <span class="badge bg-success">Kontratlı</span>
                                    @else
                                        <span class="badge bg-secondary">Kontratsız</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Kontrat Sayısı:</th>
                                <td>
                                    <span class="badge bg-primary">{{ isset($contracts) ? $contracts->total() : ($hotel->contracts->count() ?? 0) }} Kontrat</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Kontratlar</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Durum</label>
                        <select name="status" class="form-select">
                            <option value="">Tümü</option>
                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Aktif</option>
                            <option value="passive" {{ request('status')=='passive' ? 'selected' : '' }}>Pasif</option>
                            <option value="expired" {{ request('status')=='expired' ? 'selected' : '' }}>Süresi Dolmuş</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Para Birimi</label>
                        <select name="currency" class="form-select">
                            <option value="">Tümü</option>
                            <option value="TRY" {{ request('currency')=='TRY' ? 'selected' : '' }}>TRY</option>
                            <option value="USD" {{ request('currency')=='USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ request('currency')=='EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ request('currency')=='GBP' ? 'selected' : '' }}>GBP</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Başlangıç ≥</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bitiş ≤</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" />
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-outline-primary w-100" type="submit">Filtrele</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Firma</th>
                                <th>Başlangıç</th>
                                <th>Bitiş</th>
                                <th>Para Birimi</th>
                                <th>Odalar</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($contracts ?? collect()) as $contract)
                            <tr>
                                <td>{{ $contract->id }}</td>
                                <td>
                                    @if($contract->firm)
                                        <span class="badge bg-primary">{{ $contract->firm->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d.m.Y') : '-' }}</td>
                                <td>{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d.m.Y') : '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $contract->currency ?? 'TRY' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $contract->rooms->count() }} Oda</span>
                                </td>
                                <td>
                                    @if($contract->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Kontrat bulunamadı</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($contracts))
                    <div class="d-flex justify-content-center mt-2">{{ $contracts->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Otel Resmi</h5>
            </div>
            <div class="card-body text-center">
                @if($hotel->image)
                    @if(str_starts_with($hotel->image, 'http'))
                        <img src="{{ $hotel->image }}" alt="{{ $hotel->name }}" class="img-fluid rounded">
                    @else
                        <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" class="img-fluid rounded">
                    @endif
                @else
                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-hotel fa-3x"></i>
                    </div>
                    <p class="text-muted mt-2">Resim yok</p>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Hızlı İşlemler</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.contracts.create') }}?hotel_id={{ $hotel->id }}" class="btn btn-success btn-sm w-100 mb-2">
                    <i class="fas fa-plus"></i> Bu Otel İçin Kontrat Ekle
                </a>
                <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" onsubmit="return confirm('Bu oteli silmek istediğinizden emin misiniz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="fas fa-trash"></i> Oteli Sil
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 