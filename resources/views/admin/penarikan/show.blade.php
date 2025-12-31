@extends('layouts.app')

@section('title', 'Detail Penarikan Poin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Detail Penarikan Poin</h2>
            <div>
                <a href="{{ route('admin.penarikan.index') }}" class="btn btn-netra-outline me-2">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
                @if($penarikan->status == 'pending')
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="bi bi-check-circle me-2"></i>Approve
                </button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle me-2"></i>Reject
                </button>
                @elseif($penarikan->status == 'approved')
                <form action="{{ route('admin.penarikan.complete', $penarikan->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Tandai sebagai selesai? Dana sudah ditransfer?')">
                        <i class="bi bi-check2-all me-2"></i>Mark as Completed
                    </button>
                </form>
                @endif
            </div>
        </div>
        <p class="text-muted">ID: #{{ $penarikan->id }}</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="text-muted mb-2">Status</h6>
                        @if($penarikan->status == 'pending')
                        <h4 class="text-warning">
                            <i class="bi bi-clock-history me-2"></i>Pending - Menunggu Approval
                        </h4>
                        <p class="text-muted mb-0">Pengajuan ini menunggu persetujuan Anda.</p>
                        @elseif($penarikan->status == 'approved')
                        <h4 class="text-info">
                            <i class="bi bi-check-circle me-2"></i>Approved - Disetujui
                        </h4>
                        <p class="text-muted mb-0">Penarikan telah disetujui, menunggu transfer dana.</p>
                        @elseif($penarikan->status == 'completed')
                        <h4 class="text-success">
                            <i class="bi bi-check2-circle me-2"></i>Completed - Selesai
                        </h4>
                        <p class="text-muted mb-0">Dana telah ditransfer ke warga.</p>
                        @else
                        <h4 class="text-danger">
                            <i class="bi bi-x-circle me-2"></i>Rejected - Ditolak
                        </h4>
                        <p class="text-muted mb-0">Pengajuan telah ditolak.</p>
                        @endif
                    </div>
                    <div class="col-md-4 text-end">
                        <h6 class="text-muted mb-2">Jumlah</h6>
                        <h2 class="text-netra">{{ number_format($penarikan->jumlah_poin, 0, ',', '.') }} Poin</h2>
                        <h4>Rp {{ number_format($penarikan->jumlah_rupiah, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detail Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Detail Pengajuan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">ID Penarikan</th>
                                <td>#{{ $penarikan->id }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Pengajuan</th>
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
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            @if($penarikan->catatan_admin)
                            <tr>
                                <th width="40%">Catatan Admin</th>
                                <td>{{ $penarikan->catatan_admin }}</td>
                            </tr>
                            @endif
                            @if($penarikan->admin)
                            <tr>
                                <th>Admin Penyetuju</th>
                                <td>{{ $penarikan->admin->name }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Konversi</th>
                                <td>100 poin = Rp 10.000</td>
                            </tr>
                        </table>
                    </div>
                </div>
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
                            <p class="mb-0">Warga: {{ $penarikan->warga->name }}</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item {{ in_array($penarikan->status, ['approved', 'completed', 'rejected']) ? 'completed' : '' }}">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6>Review Admin</h6>
                            <p class="text-muted mb-0">
                                @if($penarikan->tanggal_approval)
                                {{ $penarikan->tanggal_approval->format('d F Y H:i') }}
                                @if($penarikan->admin)
                                <br>Oleh: {{ $penarikan->admin->name }}
                                @endif
                                @else
                                Menunggu review
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
                                Dana telah dikirim ke rekening warga
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
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Info Warga -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Warga</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-lg mx-auto mb-3">
                        <div style="width: 80px; height: 80px; background-color: #2E8B57; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: bold;">
                            {{ substr($penarikan->warga->name, 0, 1) }}
                        </div>
                    </div>
                    <h5>{{ $penarikan->warga->name }}</h5>
                    <p class="text-muted mb-0">{{ $penarikan->warga->email }}</p>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Telepon</th>
                        <td>{{ $penarikan->warga->phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $penarikan->warga->address ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Total Poin</th>
                        <td>{{ number_format($penarikan->warga->total_points, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Transaksi</th>
                        <td>{{ $penarikan->warga->transaksiSebagaiWarga->count() }}</td>
                    </tr>
                </table>
                
                <div class="d-grid gap-2">
                    <a href="mailto:{{ $penarikan->warga->email }}" class="btn btn-outline-netra">
                        <i class="bi bi-envelope me-2"></i>Kirim Email
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="bi bi-telephone me-2"></i>Hubungi
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.penarikan.index') }}" class="btn btn-netra-outline">
                        <i class="bi bi-list-ul me-2"></i>Semua Penarikan
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-netra-outline">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
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
                    <h5 class="modal-title">Approve Penarikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Approve penarikan dari <strong>{{ $penarikan->warga->name }}</strong>?</p>
                    <p><strong>{{ number_format($penarikan->jumlah_poin, 0, ',', '.') }} poin</strong> (Rp {{ number_format($penarikan->jumlah_rupiah, 0, ',', '.') }})</p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Catatan untuk warga..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Approve</button>
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
                    <h5 class="modal-title">Reject Penarikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tolak penarikan dari <strong>{{ $penarikan->warga->name }}</strong>?</p>
                    <p><strong>{{ number_format($penarikan->jumlah_poin, 0, ',', '.') }} poin</strong> (Rp {{ number_format($penarikan->jumlah_rupiah, 0, ',', '.') }})</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Berikan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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

.avatar-lg {
    width: 80px;
    height: 80px;
}
</style>
@endsection