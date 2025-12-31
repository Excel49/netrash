@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Dashboard Petugas</h2>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-primary h-100">
            <div class="card-body stat-card">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h6 class="text-uppercase mb-0">Transaksi Hari Ini</h6>
                        <div class="stat-number">{{ $transaksiHariIni }}</div>
                        <small class="text-muted">{{ number_format($beratHariIni, 1) }} kg</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-receipt stat-icon text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-success h-100">
            <div class="card-body stat-card">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h6 class="text-uppercase mb-0">Total Warga</h6>
                        <div class="stat-number">{{ $totalWarga }}</div>
                        <small class="text-muted">Warga terdaftar</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-people-fill stat-icon text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-warning h-100">
            <div class="card-body stat-card">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h6 class="text-uppercase mb-0">Transaksi Bulan Ini</h6>
                        <div class="stat-number">{{ $transaksiBulanIni }}</div>
                        <small class="text-muted">{{ number_format($beratBulanIni, 1) }} kg</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-calendar-check stat-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-info h-100">
            <div class="card-body stat-card">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h6 class="text-uppercase mb-0">Poin Diberikan</h6>
                        <div class="stat-number">{{ number_format($totalPoinDiberikan, 0, ',', '.') }}</div>
                        <small class="text-muted">Poin bulan ini</small>
                    </div>
                    <div class="col-4 text-end">
                        <i class="bi bi-coin stat-icon text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- QR Scanner Card -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">QR Code Scanner</h6>
            </div>
            <div class="card-body text-center">
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    Gunakan fitur scan untuk membaca QR Code warga
                </div>
                
                <a href="{{ route('petugas.scan') }}" class="btn btn-netra btn-lg px-5 mb-3">
                    <i class="bi bi-qr-code-scan me-2"></i>Buka QR Scanner
                </a>
                
                <div class="mt-3">
                    <p class="text-muted">Atau input transaksi manual:</p>
                    <a href="{{ route('petugas.transaksi.create') }}" class="btn btn-netra-outline">
                        <i class="bi bi-plus-circle me-2"></i>Input Transaksi Manual
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Transaksi Hari Ini</h6>
                <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-sm btn-netra">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Warga</th>
                                <th>Berat (kg)</th>
                                <th>Total Poin</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiToday as $transaksi)
                            <tr>
                                <td>{{ $transaksi->kode_transaksi }}</td>
                                <td>{{ $transaksi->warga->name }}</td>
                                <td>{{ number_format($transaksi->total_berat, 1) }}</td>
                                <td>{{ number_format($transaksi->total_poin, 0, ',', '.') }}</td>
                                <td>{{ $transaksi->created_at->format('H:i') }}</td>
                                <td>
                                    <a href="{{ route('petugas.transaksi.show', $transaksi->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Belum ada transaksi hari ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Kategori Sampah -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Kategori Sampah</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($kategoriSampah as $kategori)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $kategori->nama_kategori }}</h6>
                            <span class="badge bg-netra">Rp {{ number_format($kategori->harga_per_kg, 0, ',', '.') }}/kg</span>
                        </div>
                        <p class="mb-1 small">{{ $kategori->poin_per_kg }} poin/kg</p>
                        <small class="text-muted">{{ $kategori->jenis_sampah }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Top Warga -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Top 5 Warga</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($topWarga as $index => $warga)
                    <div class="list-group-item">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-netra me-2">{{ $index + 1 }}</span>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $warga->name }}</h6>
                                <small class="text-muted">{{ number_format($warga->total_points, 0, ',', '.') }} poin</small>
                            </div>
                            <a href="{{ route('petugas.warga.show', $warga->id) }}" 
                               class="btn btn-sm btn-outline-netra">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Quick Guide -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Petunjuk Cepat</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Scan QR Code warga</li>
                    <li class="mb-2">Input berat per kategori</li>
                    <li class="mb-2">Konfirmasi transaksi</li>
                    <li class="mb-2">Berikan struk/notifikasi</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection