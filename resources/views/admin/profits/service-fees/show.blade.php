@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Servis Ücreti Detayları</h1>
        <div>
            <a href="{{ route('admin.profits.service-fees.edit', $serviceFee) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Düzenle
            </a>
            <a href="{{ route('admin.profits.service-fees') }}" class="btn btn-secondary">
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Servis Ücreti Bilgileri</h6>
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <td width="200"><strong>Ücret Adı:</strong></td>
                    <td>{{ $serviceFee->name }}</td>
                </tr>
                <tr>
                    <td><strong>Açıklama:</strong></td>
                    <td>{{ $serviceFee->description ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Firma:</strong></td>
                    <td>
                        @if($serviceFee->firm)
                            <a href="{{ route('admin.firms.show', $serviceFee->firm) }}">
                                {{ $serviceFee->firm->name }}
                            </a>
                        @else
                            <span class="text-muted">Tüm Firmalar</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Servis Tipi:</strong></td>
                    <td><span class="badge bg-info">{{ $serviceFee->getServiceTypeDescription() }}</span></td>
                </tr>
                <tr>
                    <td><strong>Ücret Tipi:</strong></td>
                    <td><span class="badge bg-secondary">{{ $serviceFee->getFeeTypeDescription() }}</span></td>
                </tr>
                <tr>
                    <td><strong>Ücret Değeri:</strong></td>
                    <td>
                        @if($serviceFee->fee_type === 'percentage')
                            <strong>%{{ $serviceFee->fee_value }}</strong>
                        @else
                            <strong>{{ $serviceFee->fee_value }} {{ $serviceFee->currency }}</strong>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Para Birimi:</strong></td>
                    <td>{{ $serviceFee->currency }}</td>
                </tr>
                <tr>
                    <td><strong>Minimum Tutar:</strong></td>
                    <td>{{ $serviceFee->min_amount ? $serviceFee->min_amount . ' ' . $serviceFee->currency : '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Maksimum Tutar:</strong></td>
                    <td>{{ $serviceFee->max_amount ? $serviceFee->max_amount . ' ' . $serviceFee->currency : '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Durum:</strong></td>
                    <td>
                        @if($serviceFee->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Pasif</span>
                        @endif
                        @if($serviceFee->is_mandatory)
                            <span class="badge bg-warning">Zorunlu</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Oluşturulma:</strong></td>
                    <td>{{ $serviceFee->created_at->format('d.m.Y H:i') }}</td>
                </tr>
                <tr>
                    <td><strong>Son Güncelleme:</strong></td>
                    <td>{{ $serviceFee->updated_at->format('d.m.Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- İşlemler -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">İşlemler</h6>
        </div>
        <div class="card-body">
            <div class="d-grid gap-2">
                <a href="{{ route('admin.profits.service-fees.edit', $serviceFee) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Servis Ücretini Düzenle
                </a>
                <form action="{{ route('admin.profits.service-fees.destroy', $serviceFee) }}" method="POST" onsubmit="return confirm('Bu servis ücretini silmek istediğinizden emin misiniz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="fas fa-trash"></i> Servis Ücretini Sil
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

