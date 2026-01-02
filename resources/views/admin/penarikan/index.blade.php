@extends('layouts.app')

@section('title', 'Manajemen Penarikan Poin')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">
        <i class="fas fa-hand-holding-usd"></i> Manajemen Penarikan Poin
    </h1>
    <div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row mb-4">
    <!-- Stats Cards -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Penarikan</h6>
                        <h3 class="mb-0">{{ App\Models\PenarikanPoin::count() }}</h3>
                    </div>
                    <i class="fas fa-list fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Menunggu</h6>
                        <h3 class="mb-0">{{ App\Models\PenarikanPoin::where('status', 'pending')->count() }}</h3>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Selesai</h6>
                        <h3 class="mb-0">{{ App\Models\PenarikanPoin::where('status', 'completed')->count() }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Ditolak</h6>
                        <h3 class="mb-0">{{ App\Models\PenarikanPoin::where('status', 'rejected')->count() }}</h3>
                    </div>
                    <i class="fas fa-times-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/penarikan') && !request()->has('status') ? 'active' : '' }}" 
           href="{{ route('admin.penarikan.index') }}">
            Semua
            <span class="badge bg-secondary">{{ App\Models\PenarikanPoin::count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/penarikan/pending') || request('status') == 'pending' ? 'active' : '' }}" 
           href="{{ route('admin.penarikan.pending') }}">
            Menunggu
            <span class="badge bg-warning">{{ App\Models\PenarikanPoin::where('status', 'pending')->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/penarikan/approved') || request('status') == 'approved' ? 'active' : '' }}" 
           href="{{ route('admin.penarikan.approved') }}">
            Disetujui
            <span class="badge bg-info">{{ App\Models\PenarikanPoin::where('status', 'approved')->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/penarikan/completed') || request('status') == 'completed' ? 'active' : '' }}" 
           href="{{ route('admin.penarikan.completed') }}">
            Selesai
            <span class="badge bg-success">{{ App\Models\PenarikanPoin::where('status', 'completed')->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/penarikan/rejected') || request('status') == 'rejected' ? 'active' : '' }}" 
           href="{{ route('admin.penarikan.rejected') }}">
            Ditolak
            <span class="badge bg-danger">{{ App\Models\PenarikanPoin::where('status', 'rejected')->count() }}</span>
        </a>
    </li>
</ul>

<div class="card">
    <div class="card-body">
        @if($penarikan->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada penarikan poin</h5>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Warga</th>
                            <th>Jumlah Poin</th>
                            <th>Nilai Rupiah</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penarikan as $item)
                        <tr>
                            <td>#{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <strong>{{ $item->warga->name }}</strong>
                                <small class="d-block text-muted">{{ $item->warga->email }}</small>
                            </td>
                            <td>
                                <strong>{{ number_format($item->jumlah_poin, 0, ',', '.') }}</strong>
                                <small class="text-muted d-block">poin</small>
                            </td>
                            <td>
                                <strong class="text-success">Rp {{ number_format($item->jumlah_rupiah, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                <small class="text-muted d-block">{{ $item->created_at->format('d/m/Y') }}</small>
                                <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'completed' => 'success',
                                        'rejected' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'completed' => 'Selesai',
                                        'rejected' => 'Ditolak'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$item->status] ?? $item->status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.penarikan.show', $item->id) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($item->status == 'pending')
                                        <button type="button" 
                                                class="btn btn-outline-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal{{ $item->id }}"
                                                title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $item->id }}"
                                                title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    @if($item->status == 'approved')
                                        <button type="button" 
                                                class="btn btn-outline-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#completeModal{{ $item->id }}"
                                                title="Tandai Selesai">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Approve Modal -->
                        @if($item->status == 'pending')
                        <div class="modal fade" id="approveModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.penarikan.approve', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Setujui Penarikan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Setujui penarikan poin dari <strong>{{ $item->warga->name }}</strong>?</p>
                                            <div class="mb-3">
                                                <label for="catatan_admin" class="form-label">Catatan (opsional)</label>
                                                <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="3" placeholder="Berikan catatan jika diperlukan"></textarea>
                                            </div>
                                            <div class="alert alert-info">
                                                <p class="mb-1"><strong>Detail Penarikan:</strong></p>
                                                <p class="mb-1">Jumlah Poin: {{ number_format($item->jumlah_poin) }}</p>
                                                <p class="mb-0">Nilai Rupiah: Rp {{ number_format($item->jumlah_rupiah) }}</p>
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
                        @endif

                        <!-- Reject Modal -->
                        @if($item->status == 'pending')
                        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.penarikan.reject', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tolak Penarikan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Tolak penarikan poin dari <strong>{{ $item->warga->name }}</strong>?</p>
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

                        <!-- Complete Modal -->
                        @if($item->status == 'approved')
                        <div class="modal fade" id="completeModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.penarikan.complete', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tandai Sebagai Selesai</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Tandai penarikan dari <strong>{{ $item->warga->name }}</strong> sebagai selesai?</p>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Pastikan dana telah ditransfer ke rekening warga sebelum menandai sebagai selesai.
                                            </div>
                                            <div class="alert alert-info">
                                                <p class="mb-1"><strong>Detail Transfer:</strong></p>
                                                <p class="mb-1">Jumlah: Rp {{ number_format($item->jumlah_rupiah) }}</p>
                                                <p class="mb-0">Penerima: {{ $item->warga->name }}</p>
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

                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $penarikan->links() }}
            </div>
        @endif
    </div>
</div>
@endsection