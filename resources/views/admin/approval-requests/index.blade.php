@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Onay İstekleri</h1>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Onay İstekleri Listesi</h6>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Senaryo</th>
                                <th>Firma</th>
                                <th>İsteyen</th>
                                <th>İstek Tipi</th>
                                <th>Durum</th>
                                <th>Kalan Süre</th>
                                <th>Onaylayan</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td>{{ $request->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ $request->scenario->name }}</td>
                                <td>{{ $request->scenario->firm->name }}</td>
                                <td>{{ $request->requestedBy->name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($request->request_type) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $request->getStatusBadge() }}">
                                        @if($request->status === 'pending')
                                            Bekliyor
                                        @elseif($request->status === 'approved')
                                            Onaylandı
                                        @elseif($request->status === 'rejected')
                                            Reddedildi
                                        @else
                                            {{ ucfirst($request->status) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if($request->status === 'pending')
                                        {{ $request->getRemainingTime() }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($request->approvedBy)
                                        {{ $request->approvedBy->name }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $request->approved_at?->format('d.m.Y H:i') }}
                                        </small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($request->status === 'pending')
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="approveRequest({{ $request->id }})">
                                                <i class="fas fa-check"></i> Onayla
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="rejectRequest({{ $request->id }})">
                                                <i class="fas fa-times"></i> Reddet
                                            </button>
                                        </div>
                                    @else
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detay
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $requests->links() }}
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz onay isteği bulunmuyor</h5>
                    <p class="text-muted">Bekleyen onay istekleri burada görüntülenecek.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Onay Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Onay İsteği</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notlar (İsteğe Bağlı)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Onay notunuzu buraya yazabilirsiniz..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">Onayla</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Red Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">İsteği Reddet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Red Nedeni *</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Red nedeninizi buraya yazın..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">Reddet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveRequest(requestId) {
    const form = document.getElementById('approveForm');
    form.action = `/admin/approval-requests/${requestId}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function rejectRequest(requestId) {
    const form = document.getElementById('rejectForm');
    form.action = `/admin/approval-requests/${requestId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection 