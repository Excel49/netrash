@extends('layouts.app')
@section('title', 'Kelola Kategori Sampah - Admin')
@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Kelola Kategori Sampah</h1>
        <a href="{{ route('admin.kategori.create') }}" class="btn btn-netra">
            <i class="fas fa-plus me-1"></i> Tambah Item Spesifik
        </a>
    </div>
@endsection
@section('content')
    <!-- KATEGORI UTAMA (LOCKED) -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-lock me-2"></i> Kategori Utama</h5>
                <span class="badge bg-light text-dark">{{ $kategorisUtama->count() }} kategori</span>
            </div>
        </div>
        <div class="card-body">
            @if($kategorisUtama->isEmpty())
                <div class="text-center py-3">
                    <p class="text-muted">Belum ada kategori utama</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Kategori</th>
                                <th>Jenis</th>
                                <th>Poin per Kg</th>
                                <th>Status Sistem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategorisUtama as $kategori)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-2" 
                                                 style="width: 16px; height: 16px; background-color: {{ $kategori->warna_label ?? '#3b82f6' }}"></div>
                                            <strong>{{ $kategori->nama_kategori }}</strong>
                                            <i class="fas fa-lock text-muted ms-2" title="Kategori Terkunci"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $kategori->jenis_badge }}">
                                            {{ $kategori->jenis_label }}
                                        </span>
                                    </td>
                                    <td>{{ $kategori->poin_formatted }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-ban me-1"></i> Terkunci
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- ITEM SPESIFIK (UNLOCKED) -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Item Spesifik</h5>
                <span class="badge bg-light text-dark">{{ $kategorisBiasa->count() }} item</span>
            </div>
        </div>
        <div class="card-body">
            @if($kategorisBiasa->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5>Belum ada item spesifik</h5>
                    <p class="text-muted">Tambahkan item spesifik seperti botol plastik, besi, dll.</p>
                    <a href="{{ route('admin.kategori.create') }}" class="btn btn-netra">
                        <i class="fas fa-plus me-1"></i> Tambah Item Spesifik
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Item</th>
                                <th>Kategori Utama</th>
                                <th>Poin per Kg</th>
                                <th class="text-end">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategorisBiasa as $kategori)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-2" 
                                                 style="width: 16px; height: 16px; background-color: {{ $kategori->warna_label ?? '#3b82f6' }}"></div>
                                            {{ $kategori->nama_kategori }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $kategori->jenis_badge }}">
                                            {{ $kategori->jenis_label }}
                                        </span>
                                    </td>
                                    <td>{{ $kategori->poin_formatted }}</td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.kategori.edit', $kategori->id) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-item"
                                                    data-id="{{ $kategori->id }}"
                                                    data-name="{{ $kategori->nama_kategori }}"
                                                    data-used="{{ $kategori->isUsedInTransactions ? 'true' : 'false' }}"
                                                    title="Hapus">
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
    
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteMessage">Apakah Anda yakin ingin menghapus item ini?</p>
                    <div id="usedWarning" class="alert alert-warning d-none">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Item ini telah digunakan dalam transaksi. 
                        <strong>Hapus tidak diperbolehkan.</strong> 
                        Anda dapat menonaktifkan item ini agar tidak muncul di transaksi baru.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="force_delete" id="forceDelete" value="0">
                    </form>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let deleteUrl = '';
        let itemName = '';
        let isUsed = false;
        
        // Event listener untuk tombol delete
        document.querySelectorAll('.delete-item').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                itemName = this.getAttribute('data-name');
                isUsed = this.getAttribute('data-used') === 'true';
                
                deleteUrl = `/admin/kategori/${itemId}`;
                
                // Update modal content
                if (isUsed) {
                    document.getElementById('deleteMessage').textContent = `Item "${itemName}"`;
                    document.getElementById('usedWarning').classList.remove('d-none');
                    document.getElementById('confirmDeleteBtn').classList.add('d-none');
                } else {
                    document.getElementById('deleteMessage').innerHTML = 
                        `Apakah Anda yakin ingin menghapus <strong>${itemName}</strong>?<br>
                         <small class="text-danger">Data yang sudah dihapus tidak dapat dikembalikan.</small>`;
                    document.getElementById('usedWarning').classList.add('d-none');
                    document.getElementById('confirmDeleteBtn').classList.remove('d-none');
                }
                
                // Show modal
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
        });
        
        // Konfirmasi delete
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const form = document.getElementById('deleteForm');
            form.action = deleteUrl;
            form.submit();
        });
        
        // SweetAlert untuk success/error messages dari session
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session("error") }}',
                timer: 4000,
                showConfirmButton: true
            });
        @endif
        
        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: '{{ session("warning") }}',
                timer: 4000,
                showConfirmButton: true
            });
        @endif
    });
</script>
@endpush