@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Firma Detayı</h2>
    <div>
        <a href="{{ route('admin.firms.edit', $firm) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <a href="{{ route('admin.firms.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building"></i> {{ $firm->name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Firma Adı:</th>
                                <td><strong>{{ $firm->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>İletişim Kişisi:</th>
                                <td>
                                    @if($firm->contact_person)
                                        {{ $firm->contact_person }}
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>E-posta:</th>
                                <td>
                                    @if($firm->email)
                                        <a href="mailto:{{ $firm->email }}">{{ $firm->email }}</a>
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Telefon:</th>
                                <td>
                                    @if($firm->phone)
                                        <a href="tel:{{ $firm->phone }}">{{ $firm->phone }}</a>
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>E-posta Domaini:</th>
                                <td>
                                    @if($firm->email_domain)
                                        <code>{{ $firm->email_domain }}</code>
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Vergi No:</th>
                                <td>
                                    @if($firm->tax_number)
                                        <code>{{ $firm->tax_number }}</code>
                                    @else
                                        <span class="text-muted">Belirtilmemiş</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Durum:</th>
                                <td>
                                    @if($firm->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Oluşturulma:</th>
                                <td>{{ $firm->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Güncellenme:</th>
                                <td>{{ $firm->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($firm->address)
                <div class="mt-4">
                    <h6>Adres:</h6>
                    <p class="text-muted">{{ $firm->address }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Hızlı İşlemler</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.credits.create', ['firm_id' => $firm->id]) }}" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Kredi Hesabı Oluştur
                    </a>
                    <a href="{{ route('admin.contracts.create', ['firm_id' => $firm->id]) }}" class="btn btn-success">
                        <i class="fas fa-file-contract"></i> Kontrat Oluştur
                    </a>
                    <form action="{{ route('admin.firms.destroy', $firm) }}" method="POST" onsubmit="return confirm('Bu firmayı silmek istediğinizden emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Firmayı Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">İstatistikler</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $firm->creditAccounts ? $firm->creditAccounts->count() : 0 }}</h4>
                        <small class="text-muted">Kredi Hesabı</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $firm->contracts ? $firm->contracts->count() : 0 }}</h4>
                        <small class="text-muted">Kontrat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 