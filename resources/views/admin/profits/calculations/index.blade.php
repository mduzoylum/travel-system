@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kar Hesaplamaları</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#calculateModal">
            <i class="fas fa-calculator"></i> Hesapla
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            @if(session('calculation_id'))
                <a href="#" class="alert-link">Hesaplama detayını görüntüle</a>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Kar Hesaplamaları Listesi</h6>
        </div>
        <div class="card-body">
            @if($calculations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Firma</th>
                                <th>Tedarikçi</th>
                                <th>Temel Fiyat</th>
                                <th>Servis Ücreti</th>
                                <th>Komisyon</th>
                                <th>Satış Fiyatı</th>
                                <th>Kar Tutarı</th>
                                <th>Kar %</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calculations as $calculation)
                            <tr>
                                <td>{{ $calculation->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ $calculation->firm->name }}</td>
                                <td>{{ $calculation->supplier?->name ?? '-' }}</td>
                                <td>{{ number_format($calculation->base_price, 2) }} {{ $calculation->currency }}</td>
                                <td>{{ number_format($calculation->service_fee, 2) }} {{ $calculation->currency }}</td>
                                <td>{{ number_format($calculation->commission, 2) }} {{ $calculation->currency }}</td>
                                <td>{{ number_format($calculation->sale_price, 2) }} {{ $calculation->currency }}</td>
                                <td>{{ number_format($calculation->profit_amount, 2) }} {{ $calculation->currency }}</td>
                                <td>
                                    <span class="badge {{ $calculation->getProfitBadge() }}">
                                        %{{ number_format($calculation->profit_percentage, 1) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $calculation->getStatusBadge() }}">
                                        {{ $calculation->getStatusDescription() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profits.calculations.show', $calculation) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detay
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $calculations->links() }}
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz kar hesaplaması bulunmuyor</h5>
                    <p class="text-muted">İlk kar hesaplamanızı yapmak için yukarıdaki butonu kullanın.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hesaplama Modal -->
<div class="modal fade" id="calculateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kar Hesaplama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.profits.calculate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Hesaplama Modu Seçimi -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label">Hesaplama Modu *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="calculation_mode" id="mode_estimated" value="estimated" checked>
                                <label class="btn btn-outline-primary" for="mode_estimated">Ön Görülen Fiyat</label>
                                
                                <input type="radio" class="btn-check" name="calculation_mode" id="mode_existing" value="existing">
                                <label class="btn btn-outline-primary" for="mode_existing">Mevcut Ürün</label>
                            </div>
                            <small class="form-text text-muted">Ön görülen fiyat: Girilen fiyata göre kar hesaplama | Mevcut ürün: Sistemdeki ürünlerden fiyat kontrolü</small>
                        </div>
                    </div>

                    <!-- Firma Seçimi (Her iki modda da zorunlu) -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="firm_id" class="form-label">Firma *</label>
                                <select class="form-select" id="firm_id" name="firm_id" required>
                                    <option value="">Firma Seçin</option>
                                    @foreach(\App\DDD\Modules\Firm\Models\Firm::where('is_active', true)->get() as $firm)
                                        <option value="{{ $firm->id }}">{{ $firm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Ön Görülen Fiyat Modu - Ürün Tipi -->
                        <div class="col-md-6" id="product_type_field">
                            <div class="mb-3">
                                <label for="product_type" class="form-label">Ürün Tipi *</label>
                                <select class="form-select" id="product_type" name="product_type">
                                    <option value="">Ürün Tipi Seçin</option>
                                    <option value="hotel">Otel</option>
                                    <option value="flight">Uçak</option>
                                    <option value="car">Araç Kiralama</option>
                                    <option value="activity">Aktivite</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Mevcut Ürün Modu - Destinasyon -->
                        <div class="col-md-6" id="destination_field" style="display: none;">
                            <div class="mb-3">
                                <label for="destination" class="form-label">Destinasyon *</label>
                                <select class="form-select" id="destination" name="destination">
                                    <option value="">Destinasyon Seçin</option>
                                    @php
                                        $destinations = \App\DDD\Modules\Contract\Models\Hotel::select('city', 'country')
                                            ->distinct()
                                            ->orderBy('city')
                                            ->get();
                                    @endphp
                                    @foreach($destinations as $dest)
                                        <option value="{{ $dest->city }}, {{ $dest->country }}">{{ $dest->city }}, {{ $dest->country }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mevcut Ürün Modu - Otel Seçimi -->
                    <div class="row" id="hotel_selection" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hotel_id" class="form-label">Otel *</label>
                                <select class="form-select" id="hotel_id" name="hotel_id">
                                    <option value="">Önce destinasyon seçin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contract_id" class="form-label">Kontrat</label>
                                <select class="form-select" id="contract_id" name="contract_id">
                                    <option value="">Önce otel seçin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarih ve Kişi Sayısı (Mevcut Ürün Modu) -->
                    <div class="row" id="date_person_fields" style="display: none;">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="check_in" class="form-label">Giriş Tarihi *</label>
                                <input type="date" class="form-control" id="check_in" name="check_in">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="check_out" class="form-label">Çıkış Tarihi *</label>
                                <input type="date" class="form-control" id="check_out" name="check_out">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="person_count" class="form-label">Kişi Sayısı *</label>
                                <input type="number" min="1" class="form-control" id="person_count" name="person_count">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ön Görülen Fiyat Modu - Temel Fiyat -->
                    <div class="row" id="base_price_field">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="base_price" class="form-label">Temel Fiyat *</label>
                                <input type="number" step="0.01" class="form-control" id="base_price" name="base_price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Para Birimi *</label>
                                <select class="form-select" id="currency" name="currency" required>
                                    <option value="TRY">TRY</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ortak Alanlar -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="service_type" class="form-label">Servis Tipi *</label>
                                <select class="form-select" id="service_type" name="service_type" required>
                                    <option value="reservation">Rezervasyon</option>
                                    <option value="cancellation">İptal</option>
                                    <option value="modification">Değişiklik</option>
                                    <option value="booking">Rezervasyon</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="trip_type" class="form-label">Seyahat Tipi *</label>
                                <select class="form-select" id="trip_type" name="trip_type" required>
                                    <option value="domestic">Yurtiçi</option>
                                    <option value="international">Yurtdışı</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="travel_type" class="form-label">Yolculuk Tipi *</label>
                                <select class="form-select" id="travel_type" name="travel_type" required>
                                    <option value="one_way">Tek Yön</option>
                                    <option value="round_trip">Gidiş-Dönüş</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Hesapla</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeEstimated = document.getElementById('mode_estimated');
    const modeExisting = document.getElementById('mode_existing');
    const productTypeField = document.getElementById('product_type_field');
    const destinationField = document.getElementById('destination_field');
    const hotelSelection = document.getElementById('hotel_selection');
    const datePersonFields = document.getElementById('date_person_fields');
    const basePriceField = document.getElementById('base_price_field');
    const destinationSelect = document.getElementById('destination');
    const hotelSelect = document.getElementById('hotel_id');
    const contractSelect = document.getElementById('contract_id');
    const productTypeSelect = document.getElementById('product_type');
    const basePriceInput = document.getElementById('base_price');

    // Mod değiştirme fonksiyonu
    function toggleMode() {
        if (modeEstimated.checked) {
            // Ön görülen fiyat modu
            productTypeField.style.display = 'block';
            destinationField.style.display = 'none';
            hotelSelection.style.display = 'none';
            datePersonFields.style.display = 'none';
            basePriceField.style.display = 'block';
            
            // Required attribute'ları ayarla
            productTypeSelect.required = true;
            basePriceInput.required = true;
            destinationSelect.required = false;
            hotelSelect.required = false;
            document.getElementById('check_in').required = false;
            document.getElementById('check_out').required = false;
            document.getElementById('person_count').required = false;
        } else {
            // Mevcut ürün modu
            productTypeField.style.display = 'none';
            destinationField.style.display = 'block';
            hotelSelection.style.display = 'block';
            datePersonFields.style.display = 'block';
            basePriceField.style.display = 'none';
            
            // Required attribute'ları ayarla
            productTypeSelect.required = false;
            basePriceInput.required = false;
            destinationSelect.required = true;
            hotelSelect.required = true;
            document.getElementById('check_in').required = true;
            document.getElementById('check_out').required = true;
            document.getElementById('person_count').required = true;
        }
    }

    // Event listeners
    modeEstimated.addEventListener('change', toggleMode);
    modeExisting.addEventListener('change', toggleMode);

    // Destinasyon değiştiğinde otelleri yükle
    destinationSelect.addEventListener('change', function() {
        const destination = this.value;
        hotelSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        
        if (destination) {
            fetch(`/api/hotels/by-destination?destination=${encodeURIComponent(destination)}`)
                .then(response => response.json())
                .then(data => {
                    hotelSelect.innerHTML = '<option value="">Otel Seçin</option>';
                    data.forEach(hotel => {
                        const option = document.createElement('option');
                        option.value = hotel.id;
                        option.textContent = hotel.name;
                        hotelSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    hotelSelect.innerHTML = '<option value="">Hata oluştu</option>';
                });
        } else {
            hotelSelect.innerHTML = '<option value="">Önce destinasyon seçin</option>';
            contractSelect.innerHTML = '<option value="">Önce otel seçin</option>';
        }
    });

    // Otel değiştiğinde kontratları yükle
    hotelSelect.addEventListener('change', function() {
        const hotelId = this.value;
        contractSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        
        if (hotelId) {
                fetch(`/api/contracts/by-hotel/${hotelId}`)
                .then(response => response.json())
                .then(data => {
                    contractSelect.innerHTML = '<option value="">Kontrat Seçin (Opsiyonel)</option>';
                    data.forEach(contract => {
                        const option = document.createElement('option');
                        option.value = contract.id;
                        option.textContent = `${contract.name} (${contract.currency} ${contract.base_price})`;
                        contractSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    contractSelect.innerHTML = '<option value="">Hata oluştu</option>';
                });
        } else {
            contractSelect.innerHTML = '<option value="">Önce otel seçin</option>';
        }
    });

    // İlk yükleme
    toggleMode();
});
</script>
@endsection 