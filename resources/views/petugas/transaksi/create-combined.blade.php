@extends('layouts.app')

@section('title', 'Buat Transaksi Gabungan')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">Transaksi Gabungan</h4>
                    <p class="text-muted mb-0">Input sampah kategori utama dan item spesifik</p>
                </div>
                <div>
                    <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Warga Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>{{ $warga->name }}</h5>
                    <p class="mb-1"><strong>Email:</strong> {{ $warga->email }}</p>
                    <p class="mb-1"><strong>Telepon:</strong> {{ $warga->phone ?? '-' }}</p>
                    <p class="mb-1"><strong>Poin Saat Ini:</strong> 
                        <span class="badge bg-success">{{ number_format($warga->total_points, 0, ',', '.') }} poin</span>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    @if($warga->qr_code)
                    <img src="{{ asset('storage/' . $warga->qr_code) }}" alt="QR Code" width="80" class="img-thumbnail">
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Form Transaksi -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Input Transaksi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('petugas.transaksi.store.combined') }}" method="POST" id="transaksi-form">
                @csrf
                <input type="hidden" name="warga_id" value="{{ $warga->id }}">
                
                <!-- KATEGORI UTAMA -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i> Kategori Utama</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($kategoriUtama as $kategori)
                            <div class="col-md-3 mb-3">
                                <div class="form-group">
                                    <label class="form-label">
                                        <strong>{{ $kategori->nama_kategori }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $kategori->poin_per_kg }} poin/kg</small>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="kategori_utama[{{ $kategori->id }}][berat]" 
                                               class="form-control kategori-utama-berat" 
                                               step="0.1" min="0" placeholder="0.0"
                                               data-poin="{{ $kategori->poin_per_kg }}"
                                               data-kategori="{{ $kategori->nama_kategori }}">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    <div class="mt-1">
                                        <small class="text-success poin-per-kategori" id="poin-{{ $kategori->id }}">
                                            0 poin
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- ITEM SPESIFIK -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Item Spesifik</h5>
                            <button type="button" class="btn btn-light btn-sm" id="btn-tambah-item">
                                <i class="fas fa-plus me-1"></i> Tambah Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Container Item Spesifik -->
                        <div id="item-spesifik-container">
                            <!-- Item akan ditambahkan secara dinamis -->
                        </div>
                    </div>
                </div>

                <!-- TOTAL -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Total</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Total Berat</h6>
                                <h3 id="total-berat">0 kg</h3>
                            </div>
                            <div class="col-md-4">
                                <h6>Total Poin</h6>
                                <h3 id="total-poin">0 poin</h3>
                            </div>
                            <div class="col-md-4">
                                <h6>Poin Warga Baru</h6>
                                <h3 id="total-poin-warga">{{ number_format($warga->total_points, 0, ',', '.') }} poin</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CATATAN -->
                <div class="mb-4">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="3" 
                              placeholder="Contoh: Sampah basah, kemasan plastik, dll"></textarea>
                </div>

                <!-- TOMBOL SUBMIT -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-netra btn-lg">
                        <i class="fas fa-save me-2"></i>Simpan Transaksi
                    </button>
                    <a href="{{ route('petugas.transaksi.select-type') }}?warga_id={{ $warga->id }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Kembali ke Pilihan
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 Styling */
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background-color: white;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #495057;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 6px;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 6px 12px;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2E8B57;
        color: white;
    }
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .item-spesifik {
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }
    .item-spesifik:hover {
        background-color: #e9ecef !important;
    }
    .btn-tambah-berat {
        padding: 2px 8px;
        font-size: 0.75rem;
        margin-right: 4px;
        margin-top: 4px;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    let itemCounter = 0;
    
    // ================== FUNGSI KALKULASI ==================
    
    // Kalkulasi kategori utama
    function calculateKategoriUtama() {
        let totalPoinKategori = 0;
        
        $('.kategori-utama-berat').each(function() {
            const berat = parseFloat($(this).val()) || 0;
            const poinPerKg = parseFloat($(this).data('poin')) || 0;
            const poin = berat * poinPerKg;
            
            // Update display per kategori
            const kategoriId = $(this).attr('name').match(/\[(\d+)\]/)[1];
            const poinElement = $('#poin-' + kategoriId);
            if (poinElement.length) {
                poinElement.text(poin.toLocaleString('id-ID') + ' poin');
                poinElement.removeClass('text-muted').addClass(poin > 0 ? 'text-success' : 'text-muted');
            }
            
            totalPoinKategori += poin;
        });
        
        return totalPoinKategori;
    }
    
    // Kalkulasi item spesifik
    function calculateItemSpesifik() {
        let totalPoinItem = 0;
        
        $('.item-spesifik').each(function() {
            const select = $(this).find('.item-select');
            const beratInput = $(this).find('.item-berat');
            const poinDisplay = $(this).find('.item-poin-display');
            const detailText = $(this).find('.item-detail');
            
            const selectedOption = select.find('option:selected');
            const poinPerKg = parseFloat(selectedOption.data('poin')) || 0;
            const berat = parseFloat(beratInput.val()) || 0;
            const poin = berat * poinPerKg;
            
            // Update poin display
            if (poinDisplay.length) {
                poinDisplay.text(poin.toLocaleString('id-ID') + ' poin');
                poinDisplay.removeClass('text-muted').addClass(poin > 0 ? 'text-success' : 'text-muted');
            }
            
            // Update detail text
            if (detailText.length && selectedOption.val()) {
                detailText.text(poinPerKg + ' poin/kg');
                detailText.removeClass('text-danger').addClass('text-success');
            } else if (detailText.length) {
                detailText.text('');
            }
            
            totalPoinItem += poin;
        });
        
        return totalPoinItem;
    }
    
    // Hitung total semua
    function calculateTotal() {
        let totalBerat = 0;
        
        // Hitung berat dari kategori utama
        $('.kategori-utama-berat').each(function() {
            totalBerat += parseFloat($(this).val()) || 0;
        });
        
        // Hitung berat dari item spesifik
        $('.item-berat').each(function() {
            totalBerat += parseFloat($(this).val()) || 0;
        });
        
        // Hitung total poin
        const totalPoinKategori = calculateKategoriUtama();
        const totalPoinItem = calculateItemSpesifik();
        const totalPoin = totalPoinKategori + totalPoinItem;
        
        // Update display
        $('#total-berat').text(totalBerat.toFixed(1) + ' kg');
        $('#total-poin').text(totalPoin.toLocaleString('id-ID') + ' poin');
        
        // Hitung poin warga baru
        const poinWargaSaatIni = {{ $warga->total_points }};
        const poinWargaBaru = poinWargaSaatIni + totalPoin;
        $('#total-poin-warga').text(poinWargaBaru.toLocaleString('id-ID') + ' poin');
    }
    
    // ================== TOMBOL TAMBAH ITEM ==================
    
    // Tombol tambah item
    $('#btn-tambah-item').click(function() {
        const container = $('#item-spesifik-container');
        
        // Buat element baru
        const newItem = $(`
            <div class="item-spesifik row mb-3 p-3 border rounded">
                <div class="col-md-6">
                    <label class="form-label">Pilih Item Spesifik</label>
                    <select name="item_spesifik[${itemCounter}][id]" 
                            class="form-control item-select select2-item"
                            style="width: 100%"
                            required>
                        <option value="">Pilih item...</option>
                        @foreach($itemSpesifik as $item)
                        <option value="{{ $item->id }}" 
                                data-poin="{{ $item->poin_per_kg }}">
                            {{ $item->nama_kategori }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted item-detail mt-1 d-block"></small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Berat (kg)</label>
                    <div class="input-group">
                        <input type="number" 
                               name="item_spesifik[${itemCounter}][berat]" 
                               class="form-control item-berat" 
                               step="0.1" min="0.1" placeholder="0.0" required>
                        <span class="input-group-text">kg</span>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-success btn-tambah-berat" data-tambah="0.1">
                            +0.1
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success btn-tambah-berat" data-tambah="0.5">
                            +0.5
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success btn-tambah-berat" data-tambah="1">
                            +1
                        </button>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-hapus-item w-100">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="col-12 mt-2">
                    <small class="text-success item-poin-display">0 poin</small>
                </div>
            </div>
        `);
        
        container.append(newItem);
        
        // ================== SETUP SELECT2 ==================
        
        // Inisialisasi Select2 untuk dropdown baru
        newItem.find('.select2-item').select2({
            placeholder: "Cari item...",
            allowClear: false,
            width: '100%',
            dropdownParent: newItem,
            language: {
                noResults: function() {
                    return "Item tidak ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });
        
        // Event listener untuk select2 change
        newItem.find('.select2-item').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const detailText = newItem.find('.item-detail');
            
            if (selectedOption.val()) {
                const poinPerKg = selectedOption.data('poin');
                detailText.text(poinPerKg + ' poin/kg');
                detailText.removeClass('text-danger').addClass('text-success');
            } else {
                detailText.text('');
            }
            
            calculateTotal();
        });
        
        // ================== SETUP EVENT LISTENERS LAINNYA ==================
        
        // Event listener untuk berat
        newItem.find('.item-berat').on('input', calculateTotal);
        
        // Event listener untuk tombol tambah berat cepat
        newItem.find('.btn-tambah-berat').click(function() {
            const tambahan = parseFloat($(this).data('tambah'));
            const beratInput = newItem.find('.item-berat');
            const currentBerat = parseFloat(beratInput.val()) || 0;
            beratInput.val((currentBerat + tambahan).toFixed(1));
            beratInput.trigger('input');
        });
        
        // Tombol hapus
        newItem.find('.btn-hapus-item').click(function() {
            // Hapus Select2 instance sebelum menghapus element
            newItem.find('.select2-item').select2('destroy');
            newItem.remove();
            calculateTotal();
        });
        
        // Fokus ke select2 setelah ditambahkan
        setTimeout(() => {
            newItem.find('.select2-item').select2('open');
        }, 100);
        
        itemCounter++;
    });
    
    // ================== EVENT LISTENERS ==================
    
    // Event listener untuk kategori utama
    $(document).on('input', '.kategori-utama-berat', calculateTotal);
    
    // ================== VALIDASI FORM ==================
    
    // Validasi form sebelum submit
    $('#transaksi-form').submit(function(e) {
        const kategoriUtamaInputs = $('.kategori-utama-berat');
        const itemSpesifikInputs = $('.item-berat');
        
        let hasValue = false;
        let validationErrors = [];
        
        // Cek kategori utama
        kategoriUtamaInputs.each(function() {
            if (parseFloat($(this).val()) > 0) {
                hasValue = true;
            }
        });
        
        // Cek item spesifik
        $('.item-spesifik').each(function(index) {
            const select = $(this).find('.item-select');
            const beratInput = $(this).find('.item-berat');
            const berat = parseFloat(beratInput.val()) || 0;
            
            if (berat > 0) {
                hasValue = true;
                
                // Validasi: jika berat > 0, item harus dipilih
                if (!select.val()) {
                    validationErrors.push(`Item #${index + 1}: Pilih item dari daftar`);
                    const detailText = $(this).find('.item-detail');
                    detailText.text('Harap pilih item dari daftar');
                    detailText.removeClass('text-success').addClass('text-danger');
                }
                
                // Validasi: berat minimal 0.1
                if (berat < 0.1) {
                    validationErrors.push(`Item #${index + 1}: Berat minimal 0.1 kg`);
                }
            }
        });
        
        // Tampilkan error jika ada
        if (!hasValue) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Data Kosong',
                text: 'Minimal satu kategori atau item harus diisi',
                confirmButtonColor: '#2E8B57'
            });
            return;
        }
        
        if (validationErrors.length > 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: `<div class="text-start"><strong>Perbaiki kesalahan berikut:</strong><br>
                      <ul class="mt-2">${validationErrors.map(error => `<li>${error}</li>`).join('')}</ul></div>`,
                confirmButtonColor: '#2E8B57'
            });
            return;
        }
    });
    
    // ================== INISIALISASI AWAL ==================
    
    // Initial calculation
    calculateTotal();
});
</script>
@endpush
@endsection