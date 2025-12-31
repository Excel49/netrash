@extends('layouts.app')

@section('title', 'Data Warga')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Data Warga</h2>
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-netra-outline">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <p class="text-muted">Daftar semua warga terdaftar</p>
    </div>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari nama warga..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-netra w-100">
                    <i class="bi bi-search me-2"></i>Cari
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Warga</h6>
                <h3>{{ $warga->total() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Poin Tertinggi</h6>
                <h3>
                    @if($warga->count() > 0)
                    {{ number_format($warga->sortByDesc('total_points')->first()->total_points, 0, ',', '.') }}
                    @else
                    0
                    @endif
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Rata-rata Poin</h6>
                <h3>
                    @if($warga->count() > 0)
                    {{ number_format($warga->avg('total_points'), 0, ',', '.') }}
                    @else
                    0
                    @endif
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Transaksi Hari Ini</h6>
                <h3>0</h3>
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
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Total Poin</th>
                        <th>Transaksi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warga as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <div style="width: 32px; height: 32px; background-color: #2E8B57; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        {{ substr($item->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $item->name }}</h6>
                                    <small class="text-muted">{{ $item->address ? Str::limit($item->address, 30) : '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->phone ?: '-' }}</td>
                        <td>
                            <span class="badge bg-netra">
                                {{ number_format($item->total_points, 0, ',', '.') }} poin
                            </span>
                        </td>
                        <td>{{ $item->transaksiSebagaiWarga->count() }}</td>
                        <td>
                            @if($item->total_points > 0)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-secondary">Baru</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('petugas.warga.show', $item->id) }}" 
                                   class="btn btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('petugas.transaksi.create') }}?warga_id={{ $item->id }}" 
                                   class="btn btn-outline-success" title="Transaksi Baru">
                                    <i class="bi bi-plus-circle"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-people display-4 d-block mb-2"></i>
                            Belum ada warga terdaftar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    @if($warga->hasPages())
    <div class="card-footer">
        {{ $warga->links() }}
    </div>
    @endif
</div>
@endsection