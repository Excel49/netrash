@extends('layouts.app')

@section('title', 'Notifikasi Terkirim')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Notifikasi Terkirim</h2>
                <div>
                    <a href="{{ route('notifikasi.create') }}" class="btn btn-netra me-2">
                        <i class="bi bi-send me-2"></i>Kirim Baru
                    </a>
                    <a href="{{ route('notifikasi.index') }}" class="btn btn-netra-outline">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
            <p class="text-muted">Daftar notifikasi yang telah Anda kirim</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Dikirim</h6>
                    <h3>{{ $notifikasi->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Telah Dibaca</h6>
                    <h3>{{ $notifikasi->where('is_read', true)->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Belum Dibaca</h6>
                    <h3>{{ $notifikasi->where('is_read', false)->count() }}</h3>
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
                            <th>Warga</th>
                            <th>Judul</th>
                            <th>Pesan</th>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifikasi as $notif)
                        <tr>
                            <td>{{ $notif->user->name ?? 'N/A' }}</td>
                            <td>{{ $notif->judul }}</td>
                            <td>{{ Str::limit($notif->pesan, 50) }}</td>
                            <td><span class="badge bg-{{ $notif->color }}">{{ ucfirst($notif->tipe) }}</span></td>
                            <td>{{ $notif->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($notif->is_read)
                                <span class="badge bg-success">Telah Dibaca</span>
                                @else
                                <span class="badge bg-warning">Belum Dibaca</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-send display-4 d-block mb-2"></i>
                                Belum ada notifikasi yang dikirim
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($notifikasi->hasPages())
        <div class="card-footer">
            {{ $notifikasi->links() }}
        </div>
        @endif
    </div>
</div>
@endsection