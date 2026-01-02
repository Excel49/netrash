@extends('layouts.app')

@section('title', 'Detail Penarikan Poin')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">
        <i class="fas fa-file-invoice-dollar"></i> Detail Penarikan Poin
    </h1>
    <div>
        <a href="{{ route('warga.penarikan.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        @if($penarikan->status == 'pending')
            <form action="{{ route('warga.penarikan.destroy', $penarikan->id) }}" 
                  method="POST" 
                  class="d-inline"
                  onsubmit="return confirm('Batalkan penarikan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-times me-1"></i> Batalkan
                </button>
            </form>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
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
                        <label class="form-label text-muted">Tanggal Pengajuan</label>
                        <p class="mb-0">
                            <strong>{{ $penarikan->created_at->format('d F Y, H:i') }}</strong>
                        </p>
                    </div>
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

                @if($penarikan->status != 'pending')
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-user-check me-2"></i> Proses Admin
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Admin:</strong> 
                                    {{ $penarikan->admin->name ?? '-' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Tanggal Approval:</strong> 
                                    {{ $penarikan->tanggal_approval ? $penarikan->tanggal_approval->format('d F Y, H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                        @if($penarikan->catatan_admin)
                            <div class="mt-2">
                                <strong>Catatan Admin:</strong>
                                <div class="border rounded p-2 mt-1 bg-white">
                                    {{ $penarikan->catatan_admin }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i> Timeline Proses
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item {{ $penarikan->status != 'pending' ? 'active' : '' }}">
                        <div class="timeline-icon bg-primary">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Diajukan</h6>
                            <p class="text-muted small mb-0">
                                {{ $penarikan->created_at->format('d F Y, H:i') }}
                            </p>
                            <p class="mt-1 mb-0">Penarikan diajukan oleh Anda</p>
                        </div>
                    </div>

                    @if($penarikan->status == 'approved' || $penarikan->status == 'completed')
                        <div class="timeline-item {{ $penarikan->status != 'rejected' ? 'active' : '' }}">
                            <div class="timeline-icon bg-info">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Disetujui</h6>
                                <p class="text-muted small mb-0">
                                    {{ $penarikan->tanggal_approval ? $penarikan->tanggal_approval->format('d F Y, H:i') : '-' }}
                                </p>
                                <p class="mt-1 mb-0">
                                    Disetujui oleh {{ $penarikan->admin->name ?? 'Admin' }}
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
                                    Dana telah ditransfer ke rekening Anda
                                </p>
                                <p class="mt-1 mb-0">
                                    Penarikan selesai dan dana telah diterima
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($penarikan->status == 'rejected')
                        <div class="timeline-item active">
                            <div class="timeline-icon bg-danger">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Ditolak</h6>
                                <p class="text-muted small mb-0">
                                    {{ $penarikan->tanggal_approval ? $penarikan->tanggal_approval->format('d F Y, H:i') : '-' }}
                                </p>
                                <p class="mt-1 mb-0">
                                    Ditolak oleh {{ $penarikan->admin->name ?? 'Admin' }}
                                    @if($penarikan->catatan_admin)
                                        <br>
                                        <em>"{{ $penarikan->catatan_admin }}"</em>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

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