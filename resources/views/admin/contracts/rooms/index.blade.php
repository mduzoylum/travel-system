@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kontrat Odaları</h2>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
            <i class="fas fa-plus"></i> Yeni Oda Ekle
        </button>
        <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kontrata Dön
        </a>
    </div>
</div>

<!-- Kontrat Bilgileri -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                                        <strong>Otel:</strong> {{ $contract->hotel->name ?? 'Otel bulunamadı' }}
            </div>
            <div class="col-md-3">
                <strong>Firma:</strong> {{ $contract->firm->name ?? 'Firma bulunamadı' }}
            </div>
            <div class="col-md-3">
                <strong>Tarih:</strong> {{ $contract->start_date->format('d.m.Y') }} - {{ $contract->end_date->format('d.m.Y') }}
            </div>
            <div class="col-md-3">
                <strong>Para Birimi:</strong> {{ $contract->currency }}
            </div>
        </div>
    </div>
</div>

<!-- Odalar Tablosu -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-bed"></i> Odalar ({{ $contract->rooms->count() }})
        </h5>
    </div>
    <div class="card-body">
        @if($contract->rooms->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Oda Tipi</th>
                            <th>Yemek Planı</th>
                            <th>Temel Fiyat</th>
                            <th>Satış Fiyatı</th>
                            <th>Kar Marjı</th>
                            <th>Kar Tutarı</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contract->rooms as $room)
                        <tr>
                            <td>{{ $room->id }}</td>
                            <td>
                                <strong>{{ $room->room_type }}</strong>
                                @if($room->description)
                                    <br><small class="text-muted">{{ $room->description }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $room->meal_plan }}</span>
                            </td>
                            <td>
                                {{ number_format($room->base_price, 2) }} {{ $contract->currency }}
                            </td>
                            <td>
                                <strong>{{ number_format($room->sale_price, 2) }} {{ $contract->currency }}</strong>
                            </td>
                            <td>
                                @php
                                    $profit = $room->sale_price - $room->base_price;
                                    $profitMargin = ($profit / $room->base_price) * 100;
                                @endphp
                                <span class="badge {{ $profitMargin > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ number_format($profitMargin, 1) }}%
                                </span>
                            </td>
                            <td>
                                <span class="{{ $profit > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($profit, 2) }} {{ $contract->currency }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            onclick="editRoom({{ $room->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.contracts.rooms.destroy', [$contract, $room]) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Bu odayı silmek istediğinizden emin misiniz?')">
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
            
            <!-- Özet Bilgiler -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5>{{ $contract->rooms->count() }}</h5>
                            <small>Toplam Oda</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5>{{ number_format($contract->getTotalValue(), 2) }} {{ $contract->currency }}</h5>
                            <small>Toplam Değer</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5>{{ number_format($contract->rooms->avg('sale_price'), 2) }} {{ $contract->currency }}</h5>
                            <small>Ortalama Fiyat</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5>{{ number_format($contract->getTotalCommission(), 2) }} {{ $contract->currency }}</h5>
                            <small>Toplam Komisyon</small>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-bed fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Henüz oda eklenmemiş</h5>
                <p class="text-muted">Bu kontrata oda eklemek için "Yeni Oda Ekle" butonunu kullanın.</p>
            </div>
        @endif
    </div>
</div>

<!-- Oda Ekleme Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Oda Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.contracts.rooms.store', $contract) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="room_type" class="form-label">Oda Tipi *</label>
                        <input type="text" class="form-control" id="room_type" name="room_type" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meal_plan" class="form-label">Yemek Planı *</label>
                        <select class="form-select" id="meal_plan" name="meal_plan" required>
                            <option value="">Seçiniz</option>
                            <option value="BB">BB (Bed & Breakfast)</option>
                            <option value="HB">HB (Half Board)</option>
                            <option value="FB">FB (Full Board)</option>
                            <option value="AI">AI (All Inclusive)</option>
                            <option value="RO">RO (Room Only)</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="base_price" class="form-label">Temel Fiyat *</label>
                                <input type="number" step="0.01" min="0" class="form-control" 
                                       id="base_price" name="base_price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_price" class="form-label">Satış Fiyatı *</label>
                                <input type="number" step="0.01" min="0" class="form-control" 
                                       id="sale_price" name="sale_price" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <!-- Fiyat Hesaplama -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-4">
                                <small>Temel Fiyat:</small><br>
                                <strong id="display_base_price">0.00 {{ $contract->currency }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small>Kar Marjı:</small><br>
                                <strong id="display_profit_margin">0.00%</strong>
                            </div>
                            <div class="col-md-4">
                                <small>Kar Tutarı:</small><br>
                                <strong id="display_profit_amount">0.00 {{ $contract->currency }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Oda Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const basePriceInput = document.getElementById('base_price');
    const salePriceInput = document.getElementById('sale_price');
    
    function calculateProfit() {
        const basePrice = parseFloat(basePriceInput.value) || 0;
        const salePrice = parseFloat(salePriceInput.value) || 0;
        
        const profit = salePrice - basePrice;
        const profitMargin = basePrice > 0 ? (profit / basePrice) * 100 : 0;
        
        document.getElementById('display_base_price').textContent = basePrice.toFixed(2) + ' {{ $contract->currency }}';
        document.getElementById('display_profit_margin').textContent = profitMargin.toFixed(2) + '%';
        document.getElementById('display_profit_amount').textContent = profit.toFixed(2) + ' {{ $contract->currency }}';
        
        // Renk değişimi
        const profitMarginEl = document.getElementById('display_profit_margin');
        const profitAmountEl = document.getElementById('display_profit_amount');
        
        if (profit > 0) {
            profitMarginEl.className = 'text-success';
            profitAmountEl.className = 'text-success';
        } else {
            profitMarginEl.className = 'text-danger';
            profitAmountEl.className = 'text-danger';
        }
    }
    
    basePriceInput.addEventListener('input', calculateProfit);
    salePriceInput.addEventListener('input', calculateProfit);
    
    // İlk hesaplama
    calculateProfit();
});

function editRoom(roomId) {
    // TODO: Oda düzenleme modal'ı eklenecek
    alert('Oda düzenleme özelliği yakında eklenecek.');
}
</script>
@endpush 