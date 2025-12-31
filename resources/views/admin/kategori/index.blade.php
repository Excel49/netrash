@extends('layouts.app')

@section('title', 'Management Kategori Sampah')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Management Kategori Sampah</h2>
            <a href="{{ route('admin.kategori.create') }}" class="btn btn-netra">
                <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
            </a>
        </div>
        <p class="text-muted">Kelola kategori sampah dan harga poin</p>
    </div>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Kategori</h6>
                <h3>{{ $kategoris->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Aktif</h6>
                <h3>{{ $kategoris->where('status', true)->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Nonaktif</h6>
                <h3>{{ $kategoris->where('status', false)->count() }}</h3>
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
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th>Harga/kg</th>
                        <th>Poin/kg</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kategoris as $kategori)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="color-indicator me-2" 
                                     style="width: 20px; height: 20px; background-color: #{{ $kategori->warna_label ?? '2E8B57' }}; border-radius: 3px;"></div>
                                <div>
                                    <h6 class="mb-0">{{ $kategori->nama_kategori }}</h6>
                                    <small class="text-muted">{{ Str::limit($kategori->deskripsi, 30) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $kategori->jenis_sampah }}</td>
                        <td>Rp {{ number_format($kategori->harga_per_kg, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-netra">{{ $kategori->poin_per_kg }} poin</span>
                        </td>
                        <td>
                            @if($kategori->status)
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>{{ $kategori->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.kategori.edit', $kategori->id) }}" 
                                   class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.kategori.destroy', $kategori->id) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection