@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2>Çoklu Periyot Fiyat Hesaplama Test Sayfası</h2>

    @if($errors->any())
    <div class="alert alert-danger mt-4">
        <h4>Hata!</h4>
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(isset($result))
    <div class="alert alert-success mt-4">
        <h4>Hesaplama Sonucu</h4>
        <div class="row">
            <div class="col-md-6">
                <strong>Gece Sayısı:</strong> {{ $result['nights'] ?? 0 }}<br>
                @if(isset($result['base_price']))
                <strong>Toplam Maliyet:</strong> {{ number_format($result['base_price'], 2) }} {{ $result['currency'] ?? 'TRY' }}<br>
                @endif
                @if(isset($result['sale_price']))
                <strong>Toplam Satış Fiyatı:</strong> {{ number_format($result['sale_price'], 2) }} {{ $result['currency'] ?? 'TRY' }}<br>
                @endif
                @if(isset($result['service_fee']))
                <strong>Servis Bedeli:</strong> {{ number_format($result['service_fee'], 2) }} {{ $result['currency'] ?? 'TRY' }}<br>
                @endif
                @if(isset($result['total_service_fee']))
                <strong>Toplam Servis Bedeli:</strong> {{ number_format($result['total_service_fee'], 2) }} {{ $result['currency'] ?? 'TRY' }}<br>
                @endif
                <strong class="text-primary">GENEL TOPLAM:</strong> {{ number_format($result['grand_total'] ?? $result['total_room_price'] ?? 0, 2) }} {{ $result['currency'] ?? 'TRY' }}<br>
            </div>
        </div>
        
        @if(isset($result['nightly_breakdown']) && count($result['nightly_breakdown']) > 0)
        <h5 class="mt-4">Gece Gece Detay</h5>
        <table class="table table-sm mt-2">
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Maliyet</th>
                    <th>Satış Fiyatı</th>
                    <th>Orijinal Para Birimi</th>
                    <th>Dönüştürülmüş Para Birimi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result['nightly_breakdown'] as $night)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($night['date'])->format('d.m.Y') }}</td>
                    <td>{{ number_format($night['base_price'], 2) }}</td>
                    <td>{{ number_format($night['sale_price'], 2) }}</td>
                    <td>{{ $night['period_currency'] }}</td>
                    <td>{{ $night['currency'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    <form method="POST" action="{{ route('admin.test.pricing.calculate') }}" class="mt-4">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Test Parametreleri</div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label>Oda Seçin</label>
                            <select name="room_id" class="form-control" required>
                                <option value="">Seçin...</option>
                                @foreach(\App\DDD\Modules\Contract\Models\ContractRoom::with('contract.hotel', 'periods')->get() as $r)
                                <option value="{{ $r->id }}" {{ ((isset($room) && $room->id == $r->id) || request('room_id') == $r->id || (isset($selectedRoom) && $selectedRoom && $selectedRoom->id == $r->id)) ? 'selected' : '' }}>
                                    {{ $r->contract->hotel->name ?? 'N/A' }} - {{ $r->room_type }} ({{ $r->periods->count() }} periyot)
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label>Giriş Tarihi</label>
                            <input type="date" name="checkin_date" class="form-control" value="{{ request('checkin_date', '2025-04-07') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Çıkış Tarihi</label>
                            <input type="date" name="checkout_date" class="form-control" value="{{ request('checkout_date', '2025-04-11') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Para Birimi</label>
                            <select name="currency" class="form-control">
                                <option value="TRY" {{ (request('currency') == 'TRY') ? 'selected' : '' }}>TRY</option>
                                <option value="EUR" {{ (request('currency') == 'EUR') ? 'selected' : '' }}>EUR</option>
                                <option value="USD" {{ (request('currency') == 'USD') ? 'selected' : '' }}>USD</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label>Misafir Sayısı</label>
                            <input type="number" name="guest_count" class="form-control" value="{{ request('guest_count', 1) }}" min="1" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Fiyat Hesapla</button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Oda Periyotları</div>
                    <div class="card-body">
                        @php
                            $displayRoom = $room ?? $selectedRoom;
                        @endphp
                        @if(isset($displayRoom) && $displayRoom->periods->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Başlangıç</th>
                                    <th>Bitiş</th>
                                    <th>Para Birimi</th>
                                    <th>Maliyet</th>
                                    <th>Satış Fiyatı</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($displayRoom->periods as $period)
                                <tr>
                                    <td>{{ $period->start_date->format('d.m.Y') }}</td>
                                    <td>{{ $period->end_date->format('d.m.Y') }}</td>
                                    <td>{{ $period->currency }}</td>
                                    <td>{{ number_format($period->base_price, 2) }}</td>
                                    <td>{{ number_format($period->sale_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted">Bu odada henüz periyot tanımlanmamış.</p>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">Döviz Kurları</div>
                    <div class="card-body">
                        @php
                            $rates = \App\DDD\Modules\Contract\Models\ExchangeRate::where('is_active', true)->get();
                        @endphp
                        @if($rates->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kaynak</th>
                                    <th>Hedef</th>
                                    <th>Kur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rates as $rate)
                                <tr>
                                    <td>{{ $rate->from_currency }}</td>
                                    <td>{{ $rate->to_currency }}</td>
                                    <td>{{ $rate->rate }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted">Döviz kuru bulunamadı.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
