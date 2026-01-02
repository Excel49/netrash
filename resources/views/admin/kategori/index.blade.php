@extends('layouts.app')

@section('title', 'Kelola Kategori Sampah - Admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Kelola Kategori Sampah</h1>
        <a href="{{ route('admin.kategori.create') }}" class="btn btn-netra">
            <i class="fas fa-plus me-1"></i> Tambah Kategori
        </a>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @if($kategoris->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h5>Belum ada kategori sampah</h5>
                    <p class="text-muted">Mulai dengan menambahkan kategori pertama</p>
                    <a href="{{ route('admin.kategori.create') }}" class="btn btn-netra">
                        <i class="fas fa-plus me-1"></i> Tambah Kategori
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Kategori</th>
                                <th>Warna</th>
                                <th>Poin per Kg</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategoris as $kategori)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $kategori->nama_kategori }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-2" 
                                                 style="width: 20px; height: 20px; background-color: {{ $kategori->warna_label }}"></div>
                                            <span>{{ $kategori->warna_label }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ number_format($kategori->poin_per_kg, 1) }} pts/kg
                                        </span>
                                    </td>
                                    <td>
                                        @if($kategori->deskripsi)
                                            {{ Str::limit($kategori->deskripsi, 50) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.kategori.edit', $kategori->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete({{ $kategori->id }}, '{{ $kategori->nama_kategori }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <thead>
    <tr>
        <th>#</th>
        <th>Nama Kategori</th>
        <th>Jenis</th>
        <th>Warna</th>
        <th>Poin per Kg</th>
        <th>Deskripsi</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
    @foreach($kategoris as $kategori)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $kategori->nama_kategori }}</td>
            <td>
                @php
                    $jenisLabels = [
                        'organik' => ['badge' => 'success', 'label' => 'Organik'],
                        'anorganik' => ['badge' => 'info', 'label' => 'Anorganik'],
                        'berbahaya' => ['badge' => 'danger', 'label' => 'Berbahaya'],
                        'daur_ulang' => ['badge' => 'primary', 'label' => 'Daur Ulang'],
                        'lainnya' => ['badge' => 'secondary', 'label' => 'Lainnya'],
                    ];
                    $jenis = $jenisLabels[$kategori->jenis_sampah] ?? $jenisLabels['lainnya'];
                @endphp
                <span class="badge bg-{{ $jenis['badge'] }}">
                    {{ $jenis['label'] }}
                </span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="rounded-circle me-2" 
                         style="width: 20px; height: 20px; background-color: {{ $kategori->warna_label }}"></div>
                    <span>{{ $kategori->warna_label }}</span>
                </div>
            </td>
            <td>
                <span class="badge bg-success">
                    {{ number_format($kategori->poin_per_kg, 1) }} pts/kg
                </span>
            </td>
            <td>
                @if($kategori->deskripsi)
                    {{ Str::limit($kategori->deskripsi, 50) }}
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
            <td>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.kategori.edit', $kategori->id) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger" 
                            onclick="confirmDelete({{ $kategori->id }}, '{{ $kategori->nama_kategori }}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Delete Form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Kategori?',
            html: `Apakah Anda yakin ingin menghapus <strong>${name}</strong>?<br><small class="text-danger">Data yang sudah dihapus tidak dapat dikembalikan.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-form');
                form.action = `/admin/kategori/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush