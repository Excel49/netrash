@extends('layouts.app')

@section('title', 'Detail Penarikan Poin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Detail Penarikan Poin</h2>
            <a href="{{ route('warga.penarikan.index') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">ID: #{{ $penarikan->id }}</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Status Penarikan</h6>
                        @if($penarikan->status == 'pending')
                        <div class="alert alert-warning mb-0">
                            <h5 class="alert-heading">
                                <i class="bi bi-clock-history me-2"></i>Menunggu Approval
                            </h5>
                            <p class="mb-0">Pengajuan Anda sedang menunggu persetujuan admin.</p>
                        </div>
                        @elseif($penarikan->status == 'approved')
                        <div class="alert alert-info mb-0">
                            <h5 class="alert-heading">
                                <i class="bi bi-check-circle me-2"></i>Disetujui
                            </h5>
                            <p class="mb-0">Penarikan telah disetujui. Dana sedang diproses.</p>
                        </div>
                        @elseif($penarikan->status == 'completed')
                        <div class="alert alert-success mb-0">
                            <h5 class="alert-heading">
                                <i class="bi bi-check2-circle me-2"></i>Selesai
                            </h5>
                            <p class="mb-0">Dana telah ditransfer ke rekening Anda.</p>
                        </div>
                        @else
                        <div class="alert alert-danger mb-0">
                            <h5 class="alert-heading">
                                <i class="bi bi-x-circle me-2"></i>Ditolak
                            </h5>
                            <p class="mb-0">Pengajuan penarikan ditolak oleh admin.</p>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6 text-end">
                        <h6 class="text-muted">Jumlah</h6>
                        <h2 class="text-netra">{{ number_format($penarikan->jumlah_poin, 0, ',', '.') }} Poin</h2>
                        <h4>Rp {{ number_format($penarikan->jumlah_rupiah, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detail Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Detail Penarikan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Tanggal Pengajuan</th>
                        <td>{{ $penarikan->tanggal_pengajuan->format('d F Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Approval</th>
                        <td>
                            @if($penarikan->tanggal_approval)
                            {{ $penarikan->tanggal_approval->format('d F Y H:i') }}
                            @else
                            <span class="text-muted">Belum di-approve</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Alasan Penarikan</th>
                        <td>{{ $penarikan->alasan_penarikan }}</td>
                    </tr>
                    @if($penarikan->catatan_admin)
                    <tr>
                        <th>Catatan Admin</th>
                        <td>{{ $penarikan->catatan_admin }}</td>
                    </tr>
                    @endif
                    @if($penarikan->admin)
                    <tr>
                        <th>Admin Penyetuju</th>
                        <td>{{ $penarikan->admin->name }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        
        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Timeline Proses</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item {{ $penarikan->status != 'pending' ? 'completed' : '' }}">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6>Pengajuan Dikirim</h6>
                            <p class="text-muted mb-0">{{ $penarikan->tanggal_pengajuan->format('d F Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item {{ in_array($penarikan->status, ['approved', 'completed', 'rejected']) ? 'completed' : '' }}">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6>Proses Review Admin</h6>
                            <p class="text-muted mb-0">
                                @if($penarikan->tanggal_approval)
                                Selesai: {{ $penarikan->tanggal_approval->format('d F Y H:i') }}
                                @else
                                Sedang diproses
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="timeline-item {{ in_array($penarikan->status, ['completed']) ? 'completed' : ($penarikan->status == 'rejected' ? 'cancelled' : '') }}">
                        <div class="timeline-marker {{ $penarikan->status == 'completed' ? 'bg-success' : ($penarikan->status == 'rejected' ? 'bg-danger' : 'bg-secondary') }}"></div>
                        <div class="timeline-content">
                            <h6>
                                @if($penarikan->status == 'completed')
                                Dana Ditransfer
                                @elseif($penarikan->status == 'rejected')
                                Penarikan Ditolak
                                @else
                                Transfer Dana
                                @endif
                            </h6>
                            <p class="text-muted mb-0">
                                @if($penarikan->status == 'completed')
                                Dana telah dikirim ke rekening Anda
                                @elseif($penarikan->status == 'rejected')
                                Pengajuan tidak disetujui
                                @else
                                Menunggu proses transfer
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-item.completed .timeline-marker {
    box-shadow: 0 0 0 2px var(--primary-color);
}

.timeline-content {
    padding-left: 20px;
}

.timeline-item.cancelled .timeline-marker {
    background-color: #dc3545 !important;
}
</style>
@endsection