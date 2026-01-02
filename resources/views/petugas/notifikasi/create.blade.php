@extends('layouts.app')

@section('title', 'Kirim Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Kirim Notifikasi</h2>
                <a href="{{ route('notifikasi.index') }}" class="btn btn-netra-outline">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
            <p class="text-muted">Kirim notifikasi ke warga yang pernah Anda layani</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('notifikasi.store') }}">
                        @csrf

                        <!-- Pilih Warga -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pilih Warga <span class="text-danger">*</span></label>
                            <select name="warga_id[]" class="form-select select2" multiple="multiple" required>
                                @foreach($warga as $w)
                                <option value="{{ $w->id }}">{{ $w->name }} - {{ $w->email }} ({{ $w->total_points }} poin)</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih satu atau lebih warga</small>
                        </div>

                        <!-- Judul -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Judul Notifikasi <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" required 
                                   placeholder="Contoh: Promo Bulan Ini" value="{{ old('judul') }}">
                        </div>

                        <!-- Pesan -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pesan <span class="text-danger">*</span></label>
                            <textarea name="pesan" class="form-control" rows="5" required
                                      placeholder="Tulis pesan notifikasi di sini">{{ old('pesan') }}</textarea>
                        </div>

                        <!-- Tipe -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tipe Notifikasi</label>
                            <select name="tipe" class="form-select">
                                <option value="info">Info (Biru)</option>
                                <option value="warning">Peringatan (Kuning)</option>
                                <option value="important">Penting (Merah)</option>
                                <option value="transaction">Transaksi (Hijau)</option>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-netra">
                                <i class="bi bi-send me-2"></i>Kirim Notifikasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container .select2-selection--multiple {
    min-height: 42px;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Pilih warga...",
        allowClear: true
    });
});
</script>
@endpush