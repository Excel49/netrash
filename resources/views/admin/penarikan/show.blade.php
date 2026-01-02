@extends('layouts.app')

@section('title', 'Detail Penarikan Poin Admin')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">
        <i class="fas fa-file-invoice-dollar"></i> Detail Penarikan Poin
    </h1>
    <div>
        <a href="{{ route('admin.penarikan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-netra text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i> Informasi Penarikan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">ID Penarikan</label>
                        <p class="mb-0">
                            <strong>#{{ str_pad($penarikan->id, 6, '0', STR_PAD_LEFT) }}</strong>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Tanggal Pengajuan</label>
                        <p class="mb-0">
                            <strong>{{ $penarikan->created_at->format('d F Y, H:i') }}</strong>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Status</label>
                        <p class="mb-0">
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'approved' => 'info',
                                    'completed' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $statusLabels = [
                                    'pending' => 'Menunggu Approval',
                                    'approved' => 'Disetujui',
                                    'completed' => 'Selesai',
                                    'rejected' => 'Ditolak'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$penarikan->status] ?? 'secondary' }} p-2">
                                {{ $statusLabels[$penarikan->status] ?? $penarikan->status }}
                            </span>
                        </p>
                    </div>
                    @if($penarikan->tanggal_approval)
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Tanggal Approval</label>
                        <p class="mb-0">
                            <strong>{{ $penarikan->tanggal_approval->format('d F Y, H:i') }}</strong>
                        </p>
                    </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Jumlah Poin</label>
                        <h3 class="text-netra mb-0">
                            {{ number_format($penarikan->jumlah_poin, 0, ',', '.') }} pts
                        </h3>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Nilai Rupiah</label>
                        <h3 class="text-success mb-0">
                            Rp {{ number_format($penarikan->jumlah_rupiah, 0, ',', '.') }}
                        </h3>
                        <small class="text-muted">Kurs: 100 poin = Rp 10.000</small>
                    </div>
                </div>

                @if($penarikan->catatan_admin)
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-sticky-note me-2"></i> Catatan Admin
                    </h6>
                    <p class="mb-0">{{ $penarikan->catatan_admin }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Warga Information -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i> Informasi Warga
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Nama Warga</label>
                        <p class="mb-0">
                            <strong>{{ $penarikan->warga->name }}</strong>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Email</label>
                        <p class="mb-0">
                            <strong>{{ $penarikan->warga->email }}</strong>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Total Poin Saat Ini</label>
                        <p class="mb-0">
                            <strong>{{ number_format($penarikan->warga->total_points, 0, ',', '.') }} pts</strong>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Total Penarikan</label>
                        <p class="mb-0">
                            <strong>{{ App\Models\PenarikanPoin::where('warga_id', $penarikan->warga_id)->count() }} kali</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Admin Actions -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i> Aksi Admin
                </h5>
            </div>
            <div class="card-body">
                @if($penarikan->status == 'pending')
                    <div class="d-grid gap-2">
                        <button type="button" 
                                class="btn btn-success mb-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#approveModal">
                            <i class="fas fa-check me-2"></i> Setujui Penarikan
                        </button>
                        <button type="button" 
                                class="btn btn-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#rejectModal">
                            <i class="fas fa-times me-2"></i> Tolak Penarikan
                        </button>
                    </div>
                @endif

                @if($penarikan->status == 'approved')
                    <div class="d-grid gap-2">
                        <button type="button" 
                                class="btn btn-success" 
                                data-bs-toggle="modal" 
                                data-bs-target="#completeModal">
                            <i class="fas fa-money-bill-wave me-2"></i> Tandai Selesai
                        </button>
                    </div>
                @endif

                @if($penarikan->status == 'completed')
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Penarikan telah selesai dan dana telah ditransfer.
                    </div>
                @endif

                @if($penarikan->status == 'rejected')
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        Penarikan telah ditolak.
                    </div>
                @endif
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i> Timeline Status
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item active">
                        <div class="timeline-icon bg-primary">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Diajukan</h6>
                            <p class="text-muted small mb-0">
                                {{ $penarikan->created_at->format('d F Y, H:i') }}
                            </p>
                        </div>
                    </div>

                    @if($penarikan->status == 'approved' || $penarikan->status == 'completed' || $penarikan->status == 'rejected')
                        <div class="timeline-item active">
                            <div class="timeline-icon bg-info">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Diproses Admin</h6>
                                <p class="text-muted small mb-0">
                                    {{ $penarikan->tanggal_approval->format('d F Y, H:i') }}
                                </p>
                                <p class="mt-1 mb-0">
                                    oleh {{ $penarikan->admin->name ?? 'Admin' }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($penarikan->status == 'completed')
                        <div class="timeline-item active">
                            <div class="timeline-icon bg-success">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Dana Ditransfer</h6>
                                <p class="text-muted small mb-0">
                                    Selesai
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@if($penarikan->status == 'pending')
<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.penarikan.approve', $penarikan->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Setujui Penarikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Setujui penarikan poin dari <strong>{{ $penarikan->warga->name }}</strong>?</p>
                    <div class="mb-3">
                        <label for="catatan_admin" class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="3" placeholder="Berikan catatan jika diperlukan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.penarikan.reject', $penarikan->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Penarikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tolak penarikan poin dari <strong>{{ $penarikan->warga->name }}</strong>?</p>
                    <div class="mb-3">
                        <label for="catatan_admin" class="form-label">Alasan Penolakan *</label>
                        <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="3" placeholder="Harap berikan alasan penolakan" required></textarea>
                        <div class="form-text">Poin akan dikembalikan ke warga setelah ditolak.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($penarikan->status == 'approved')
<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.penarikan.complete', $penarikan->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tandai Sebagai Selesai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tandai penarikan dari <strong>{{ $penarikan->warga->name }}</strong> sebagai selesai?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pastikan dana telah ditransfer ke rekening warga sebelum menandai sebagai selesai.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Tandai Selesai</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 50px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 18px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}
.timeline-item {
    position: relative;
    margin-bottom: 30px;
}
.timeline-item:last-child {
    margin-bottom: 0;
}
.timeline-item.active .timeline-icon {
    background-color: var(--netra-primary) !important;
}
.timeline-icon {
    position: absolute;
    left: -50px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    background-color: #dee2e6;
}
.timeline-content {
    background: white;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}
</style>
@endsection