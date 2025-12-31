@extends('layouts.app')

@section('title', 'Riwayat Penarikan Poin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Riwayat Penarikan Poin</h2>
            <div>
                <a href="{{ route('warga.penarikan.create') }}" class="btn btn-netra me-2">
                    <i class="bi bi-plus-circle me-2"></i>Ajukan Penarikan
                </a>
                <a href="{{ route('warga.dashboard') }}" class="btn btn-netra-outline">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
        <p class="text-muted">Daftar pengajuan penarikan poin Anda</p>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Penarikan</h6>
                <h3>{{ $penarikan->total() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Selesai</h6>
                <h3>{{ $penarikan->where('status', 'completed')->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Pending</h6>
                <h3>{{ $penarikan->where('status', 'pending')->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Ditolak</h6>
                <h3>{{ $penarikan->where('status', 'rejected')->count() }}</h3>
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
                        <th>Tanggal</th>
                        <th>Jumlah Poin</th>
                        <th>Nilai Rupiah</th>
                        <th>Status</th>
                        <th>Tanggal Approval</th>
                        <th>Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penarikan as $item)
                    <tr>
                        <td>{{ $item->tanggal_pengajuan->format('d/m/Y H:i') }}</td>
                        <td>{{ number_format($item->jumlah_poin, 0, ',', '.') }} poin</td>
                        <td>Rp {{ number_format($item->jumlah_rupiah, 0, ',', '.') }}</td>
                        <td>
                            @if($item->status == 'pending')
                            <span class="badge bg-warning">Menunggu</span>
                            @elseif($item->status == 'approved')
                            <span class="badge bg-info">Disetujui</span>
                            @elseif($item->status == 'completed')
                            <span class="badge bg-success">Selesai</span>
                            @else
                            <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>
                        <td>
                            @if($item->tanggal_approval)
                            {{ $item->tanggal_approval->format('d/m/Y H:i') }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if($item->admin)
                            {{ $item->admin->name }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('warga.penarikan.show', $item->id) }}" 
                                   class="btn btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($item->status == 'pending')
                                <form action="{{ route('warga.penarikan.destroy', $item->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Batalkan penarikan?')"
                                            title="Batalkan">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
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