@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tedarikçi Grupları</h2>
    <a href="{{ route('admin.supplier-groups.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Grup Ekle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($groups->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Grup Adı</th>
                            <th>Grup Tipi</th>
                            <th>Açıklama</th>
                            <th>Renk</th>
                            <th>Tedarikçi Sayısı</th>
                            <th>Sıra</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groups as $group)
                        <tr>
                            <td>{{ $group->id }}</td>
                            <td>
                                <strong>{{ $group->name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $group->group_type_label }}</span>
                            </td>
                            <td>
                                @if($group->description)
                                    <small class="text-muted">{{ Str::limit($group->description, 50) }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $group->color }}">
                                    {{ $group->color }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $group->suppliers_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $group->sort_order }}</span>
                            </td>
                            <td>
                                @if($group->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Pasif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.supplier-groups.show', $group) }}" 
                                       class="btn btn-info btn-sm" title="Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.supplier-groups.edit', $group) }}" 
                                       class="btn btn-warning btn-sm" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-{{ $group->is_active ? 'secondary' : 'success' }} btn-sm" 
                                            onclick="toggleGroupStatus({{ $group->id }})" 
                                            title="{{ $group->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                        <i class="fas fa-{{ $group->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                    <form action="{{ route('admin.supplier-groups.destroy', $group) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Bu grubu silmek istediğinizden emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Sil">
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
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $groups->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Henüz grup eklenmemiş</h5>
                <p class="text-muted">İlk grubu eklemek için aşağıdaki butona tıklayın.</p>
                <a href="{{ route('admin.supplier-groups.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Yeni Grup Ekle
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleGroupStatus(groupId) {
    if (!confirm('Grup durumunu değiştirmek istediğinizden emin misiniz?')) {
        return;
    }
    
    fetch(`/admin/supplier-groups/${groupId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Bir hata oluştu: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu!');
    });
}
</script>
@endpush

