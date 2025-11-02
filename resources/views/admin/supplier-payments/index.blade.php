@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tedarikçi Ödemeler</h2>
</div>

<!-- Filtreler -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.supplier-payments.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Durum</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tümü</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Bekleyen</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Ödendi</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="hotel_id" class="form-label">Otel</label>
                <select class="form-select" id="hotel_id" name="hotel_id">
                    <option value="">Tüm Oteller</option>
                    @foreach($hotels as $hotel)
                        <option value="{{ $hotel->id }}" {{ request('hotel_id') == $hotel->id ? 'selected' : '' }}>
                            {{ $hotel->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="payment_type" class="form-label">Ödeme Tipi</label>
                <select class="form-select" id="payment_type" name="payment_type">
                    <option value="">Tümü</option>
                    <option value="cari" {{ request('payment_type') == 'cari' ? 'selected' : '' }}>Cari</option>
                    <option value="credit_card" {{ request('payment_type') == 'credit_card' ? 'selected' : '' }}>Kredi Kartı</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="date_from" class="form-label">Başlangıç Tarihi</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-3">
                <label for="date_to" class="form-label">Bitiş Tarihi</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrele
                </button>
                <a href="{{ route('admin.supplier-payments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Otel</th>
                            <th>Rezervasyon</th>
                            <th>Tutar</th>
                            <th>Ödeme Tipi</th>
                            <th>Vade Tarihi</th>
                            <th>Durum</th>
                            <th>Notlar</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>
                                <strong>{{ $payment->hotel->name ?? 'Bilinmeyen Otel' }}</strong>
                                @if($payment->hotel)
                                    <br><small class="text-muted">{{ $payment->hotel->city }}</small>
                                @endif
                            </td>
                            <td>
                                @if($payment->reservation_id)
                                    <span class="badge bg-info">Rezervasyon #{{ $payment->reservation_id }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ number_format($payment->amount, 2) }}</strong> {{ $payment->currency }}
                            </td>
                            <td>
                                @if($payment->payment_type === 'cari')
                                    <span class="badge bg-primary">Cari</span>
                                @elseif($payment->payment_type === 'credit_card')
                                    <span class="badge bg-info">Kredi Kartı</span>
                                @else
                                    <span class="badge bg-secondary">{{ $payment->payment_type }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $payment->due_date->format('d.m.Y') }}</strong>
                                @if($payment->due_date->isPast() && $payment->status === 'pending')
                                    <br><small class="text-danger">Gecikmiş!</small>
                                @endif
                            </td>
                            <td>
                                @if($payment->status === 'pending')
                                    <span class="badge bg-warning">Bekleyen</span>
                                @elseif($payment->status === 'paid')
                                    <span class="badge bg-success">Ödendi</span>
                                    @if($payment->paid_at)
                                        <br><small>{{ $payment->paid_at->format('d.m.Y') }}</small>
                                    @endif
                                @elseif($payment->status === 'cancelled')
                                    <span class="badge bg-secondary">İptal</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->notes)
                                    <small>{{ Str::limit($payment->notes, 50) }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->status === 'pending')
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-success" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#markPaidModal{{ $payment->id }}"
                                            title="Ödendi İşaretle">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#cancelModal{{ $payment->id }}"
                                            title="İptal Et">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Ödendi Modal -->
                                <div class="modal fade" id="markPaidModal{{ $payment->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.supplier-payments.update-status', $payment) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Ödeme Onayla</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Bu ödemeyi ödendi olarak işaretlemek istediğinizden emin misiniz?</p>
                                                    <div class="mb-3">
                                                        <label for="notes{{ $payment->id }}" class="form-label">Notlar (Opsiyonel)</label>
                                                        <textarea class="form-control" id="notes{{ $payment->id }}" name="notes" rows="3"></textarea>
                                                    </div>
                                                    <input type="hidden" name="status" value="paid">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                    <button type="submit" class="btn btn-success">Ödendi İşaretle</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- İptal Modal -->
                                <div class="modal fade" id="cancelModal{{ $payment->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.supplier-payments.update-status', $payment) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Ödeme İptal Et</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Bu ödemeyi iptal etmek istediğinizden emin misiniz?</p>
                                                    <div class="mb-3">
                                                        <label for="cancel_notes{{ $payment->id }}" class="form-label">İptal Sebebi (Opsiyonel)</label>
                                                        <textarea class="form-control" id="cancel_notes{{ $payment->id }}" name="notes" rows="3"></textarea>
                                                    </div>
                                                    <input type="hidden" name="status" value="cancelled">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                    <button type="submit" class="btn btn-danger">İptal Et</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $payments->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Ödeme bekleyen kayıt yok</h5>
                <p class="text-muted">Tüm ödemeleriniz güncel görünüyor.</p>
            </div>
        @endif
    </div>
</div>

<!-- Quick Stats -->
@if($payments->count() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Bekleyen Ödemeler</h5>
                        <h3 class="mb-0">{{ $payments->where('status', 'pending')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Ödenen Toplam</h5>
                        <h3 class="mb-0">{{ number_format($payments->where('status', 'paid')->sum('amount'), 2) }} ₺</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Bekleyen Tutar</h5>
                        <h3 class="mb-0">{{ number_format($payments->where('status', 'pending')->sum('amount'), 2) }} ₺</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Gecikmiş Ödemeler</h5>
                        <h3 class="mb-0">{{ $payments->filter(function($p) { return $p->status === 'pending' && $p->due_date->isPast(); })->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-times fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
