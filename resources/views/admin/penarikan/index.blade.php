@extends('layouts.app')

@section('title', 'Management Penarikan Poin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Management Penarikan Poin</h2>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Approve atau tolak pengajuan penarikan poin dari warga</p>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ request('completed') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-netra me-2">
                    <i class="bi bi-filter me-2"></i>Filter
                </button>
                <a href="{{ route('admin.penarikan.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Pending</h6>
                <h3>{{ $penarikan->where('status', 'pending')->count() }}</h3>
                <small>Menunggu approval</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Approved</h6>
                <h3>{{ $penarikan->where('status', 'approved')->count() }}</h3>
                <small>Disetujui</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Completed</h6>
                <h3>{{ $penarikan->where('status', 'completed')->count() }}</h3>
                <small>Selesai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Rejected</h6>
                <h3>{{ $penarikan->where('status', 'rejected')->count() }}</h3>
                <small>Ditolak</small>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Warga</th>
                        <th>Jumlah Poin</th>
                        <th>Nilai Rupiah</th>
                        <th>Status</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penarikan as $item)
                    <tr>
                        <td>#{{ $item->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <div style="width: 32px; height: 32px; background-color: #2E8B57; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        {{ substr($item->warga->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $item->warga->name }}</h6>
                                    <small class="text-muted">{{ $item->warga->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ number_format($item->jumlah_poin, 0, ',', '.') }} poin</td>
                        <td>Rp {{ number_format($item->jumlah_rupiah, 0, ',', '.') }}</td>
                        <td>
                            @if($item->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($item->status == 'approved')
                            <span class="badge bg-info">Approved</span>
                            @elseif($item->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                            @else
                            <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $item->tanggal_pengajuan->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($item->admin)
                            <span class="badge bg-secondary">{{ $item->admin->name }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.penarikan.show', $item->id) }}" 
                                   class="btn btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($item->status == 'pending')
                                <!-- Modal Trigger for Approve -->
                                <button type="button" class="btn btn-outline-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#approveModal{{ $item->id }}"
                                        title="Approve">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <!-- Modal Trigger for Reject -->
                                <button type="button" class="btn btn-outline-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#rejectModal{{ $item->id }}"
                                        title="Reject">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                @endif
                                @if($item->status == 'approved')
                                <form action="{{ route('admin.penarikan.complete', $item->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" 
                                            onclick="return confirm('Tandai sebagai selesai? Dana sudah ditransfer?')"
                                            title="Complete">
                                        <i class="bi bi-check2-all"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                            
                            <!-- Approve Modal -->
                            <div class="modal fade" id="approveModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.penarikan.approve', $item->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Approve Penarikan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Approve penarikan dari <strong>{{ $item->warga->name }}</strong>?</p>
                                                <p><strong>{{ number_format($item->jumlah_poin, 0, ',', '.') }} poin</strong> (Rp {{ number_format($item->jumlah_rupiah, 0, ',', '.') }})</p>
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
                            <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.penarikan.reject', $item->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reject Penarikan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Tolak penarikan dari <strong>{{ $item->warga->name }}</strong>?</p>
                                                <p><strong>{{ number_format($item->jumlah_poin, 0, ',', '.') }} poin</strong> (Rp {{ number_format($item->jumlah_rupiah, 0, ',', '.') }})</p>
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-cash-coin display-4 d-block mb-2"></i>
                            Belum ada pengajuan penarikan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    @if($penarikan->hasPages())
    <div class="card-footer">
        {{ $penarikan->links() }}
    </div>
    @endif
</div>
@endsection