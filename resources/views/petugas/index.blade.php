@extends('layouts.app')

@section('title', 'Data Warga')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Data Warga</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Data Warga</li>
                    </ol>
                </div>
            </div>
            <p class="text-muted">Daftar warga yang pernah bertransaksi dengan Anda</p>
        </div>
    </div>

    <!-- Search and Actions -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('petugas.warga.search') }}" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Cari warga (nama, email, telepon, NIK)..."
                           value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('petugas.warga.create') }}" class="btn btn-success me-2">
                <i class="bi bi-person-plus me-2"></i>Tambah Warga
            </a>
            <a href="{{ route('petugas.warga.export') }}" class="btn btn-outline-secondary">
                <i class="bi bi-download me-2"></i>Export
            </a>
        </div>
    </div>

    <!-- Warga Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Kontak</th>
                                    <th>Alamat</th>
                                    <th>Total Poin</th>
                                    <th>Transaksi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warga as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->profile_photo_url)
                                            <img src="{{ $user->profile_photo_url }}" 
                                                 alt="Foto profil" 
                                                 class="rounded-circle me-2" 
                                                 width="32" height="32">
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">NIK: {{ $user->nik ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="text-muted d-block">{{ $user->email }}</small>
                                            <small class="text-muted">{{ $user->phone ?? '-' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $user->address ?? '-' }}</small>
                                        <br>
                                        <small class="text-muted">RT/RW: {{ $user->rt_rw ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success">
                                            {{ number_format($user->total_points ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $user->total_transactions ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('petugas.warga.show', $user) }}" 
                                               class="btn btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('petugas.warga.edit', $user) }}" 
                                               class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('petugas.warga.transaksi', $user) }}" 
                                               class="btn btn-outline-info" title="Transaksi">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-people fs-4 mb-2 d-block"></i>
                                            @if(request('search'))
                                                Tidak ditemukan warga dengan pencarian "{{ request('search') }}"
                                            @else
                                                Belum ada data warga
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($warga->hasPages())
                <div class="card-footer">
                    {{ $warga->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection