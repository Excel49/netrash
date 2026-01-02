@extends('layouts.app')

@section('title', 'Daftar Penarikan Poin')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">
        <i class="fas fa-hand-holding-usd"></i> Daftar Penarikan Poin
    </h1>
    <div>
        <a href="{{ route('warga.penarikan.create') }}" class="btn btn-netra">
            <i class="fas fa-plus-circle me-1"></i> Ajukan Penarikan
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <!-- Stats Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <h6 class="card-title text-muted mb-2">
                    <i class="fas fa-coins me-1"></i> Poin Saat Ini
                </h6>
                <h2 class="text-netra mb-1">
                    {{ number_format(auth()->user()->total_points, 0, ',', '.') }}
                </h2>
                <small class="text-muted">pts</small>
                <div class="mt-3">
                    <a href="{{ route('warga.penarikan.create') }}" class="btn btn-sm btn-outline-netra w-100">
                        <i class="fas fa-hand-holding-usd me-1"></i> Tarik Poin
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i> Filter Status
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('warga.penarikan.index') }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->is('warga/penarikan') && !request()->has('status') ? 'active' : '' }}">
                        Semua
                        <span class="badge bg-netra rounded-pill">
                            {{ App\Models\PenarikanPoin::where('warga_id', auth()->id())->count() }}
                        </span>
                    </a>
                    <a href="{{ route('warga.penarikan.index', ['status' => 'pending']) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request('status') == 'pending' ? 'active' : '' }}">
                        Menunggu
                        <span class="badge bg-warning rounded-pill">
                            {{ App\Models\PenarikanPoin::where('warga_id', auth()->id())->where('status', 'pending')->count() }}
                        </span>
                    </a>
                    <a href="{{ route('warga.penarikan.index', ['status' => 'approved']) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request('status') == 'approved' ? 'active' : '' }}">
                        Disetujui
                        <span class="badge bg-info rounded-pill">
                            {{ App\Models\PenarikanPoin::where('warga_id', auth()->id())->where('status', 'approved')->count() }}
                        </span>
                    </a>
                    <a href="{{ route('warga.penarikan.index', ['status' => 'completed']) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request('status') == 'completed' ? 'active' : '' }}">
                        Selesai
                        <span class="badge bg-success rounded-pill">
                            {{ App\Models\PenarikanPoin::where('warga_id', auth()->id())->where('status', 'completed')->count() }}
                        </span>
                    </a>
                    <a href="{{ route('warga.penarikan.index', ['status' => 'rejected']) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request('status') == 'rejected' ? 'active' : '' }}">
                        Ditolak
                        <span class="badge bg-danger rounded-pill">
                            {{ App\Models\PenarikanPoin::where('warga_id', auth()->id())->where('status', 'rejected')->count() }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                @if($penarikan->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada penarikan poin</h5>
                        <p class="text-muted mb-4">Ajukan penarikan poin pertama Anda</p>
                        <a href="{{ route('warga.penarikan.create') }}" class="btn btn-netra">
                            <i class="fas fa-plus-circle me-1"></i> Ajukan Penarikan
                        </a>
                    </div>
                @else
                    <!-- Filter Status Info -->
                    @if(request('status'))
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Menampilkan penarikan dengan status: 
                            <strong>
                                @php
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'completed' => 'Selesai',
                                        'rejected' => 'Ditolak'
                                    ];
                                @endphp
                                {{ $statusLabels[request('status')] ?? request('status') }}
                            </strong>
                            <a href="{{ route('warga.penarikan.index') }}" class="float-end">
                                Tampilkan semua
                            </a>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Poin</th>
                                    <th>Nilai Rupiah</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penarikan as $item)
                                <tr>
                                    <td>
                                        <small class="text-muted d-block">{{ $item->created_at->format('d/m/Y') }}</small>
                                        <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($item->jumlah_poin, 0, ',', '.') }}</strong>
                                        <small class="text-muted d-block">poin</small>
                                    </td>
                                    <td>
                                        <strong class="text-success">Rp {{ number_format($item->jumlah_rupiah, 0, ',', '.') }}</strong>
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
                                        @if($item->admin && $item->status != 'pending')
                                            <small class="d-block text-muted mt-1">
                                                oleh {{ $item->admin->name }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('warga.penarikan.show', $item->id) }}" 
                                               class="btn btn-outline-primary" 
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($item->status == 'completed')
                                                <a href="{{ route('warga.penarikan.print', $item->id) }}" 
                                                   class="btn btn-outline-success" 
                                                   title="Print Receipt">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            @endif
                                            @if($item->status == 'pending')
                                                <form action="{{ route('warga.penarikan.destroy', $item->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Batalkan penarikan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger" 
                                                            title="Batalkan">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
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

        <!-- Stats Summary -->
        @if(!$penarikan->isEmpty())
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Penarikan</h6>
                        <h4 class="text-netra mb-0">
                            {{ number_format($penarikan->total(), 0, ',', '.') }}
                        </h4>
                        <small class="text-muted">transaksi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Poin</h6>
                        <h4 class="text-netra mb-0">
                            {{ number_format($penarikan->sum('jumlah_poin'), 0, ',', '.') }}
                        </h4>
                        <small class="text-muted">poin</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Rupiah</h6>
                        <h4 class="text-success mb-0">
                            Rp {{ number_format($penarikan->sum('jumlah_rupiah'), 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Selesai</h6>
                        <h4 class="text-success mb-0">
                            {{ App\Models\PenarikanPoin::where('warga_id', auth()->id())->where('status', 'completed')->count() }}
                        </h4>
                        <small class="text-muted">transaksi</small>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto refresh untuk pending status
        if (window.location.search.includes('status=pending')) {
            setTimeout(function() {
                window.location.reload();
            }, 30000); // Refresh setiap 30 detik untuk status pending
        }
        
        // Konfirmasi pembatalan
        const cancelForms = document.querySelectorAll('form[onsubmit]');
        cancelForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Yakin ingin membatalkan penarikan ini? Poin akan dikembalikan.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush