@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Firmalar</h2>
    <a href="{{ route('admin.firms.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Firma Ekle
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($firms->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ad</th>
                            <th>E-posta</th>
                            <th>Telefon</th>
                            <th>Adres</th>
                            <th>Vergi No</th>
                            <th>Durum</th>
                            <th>Oluşturulma</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($firms as $firm)
                        <tr>
                            <td>{{ $firm->id }}</td>
                            <td>
                                <strong>{{ $firm->name }}</strong>
                                @if($firm->contact_person)
                                    <br><small class="text-muted">İletişim: {{ $firm->contact_person }}</small>
                                @endif
                            </td>
                            <td>
                                @if($firm->email)
                                    <a href="mailto:{{ $firm->email }}">{{ $firm->email }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($firm->phone)
                                    <a href="tel:{{ $firm->phone }}">{{ $firm->phone }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($firm->address)
                                    <small>{{ Str::limit($firm->address, 50) }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($firm->tax_number)
                                    <code>{{ $firm->tax_number }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($firm->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $firm->created_at->format('d.m.Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.firms.show', $firm) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.firms.edit', $firm) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.firms.destroy', $firm) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu firmayı silmek istediğinizden emin misiniz?')">
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
            
            <div class="d-flex justify-content-center mt-4">
                {{ $firms->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Henüz firma eklenmemiş</h5>
                <p class="text-muted">İlk firmayı eklemek için yukarıdaki butonu kullanın.</p>
            </div>
        @endif
    </div>
</div>
@endsection 