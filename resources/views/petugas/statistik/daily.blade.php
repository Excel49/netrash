@extends('layouts.app')

@section('title', 'Statistik Harian')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Statistik Harian</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('petugas.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('petugas.statistik.index') }}">Statistik</a></li>
                        <li class="breadcrumb-item active">Harian</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Halaman Statistik Harian</h5>
                    <p class="text-muted">Fitur ini sedang dalam pengembangan.</p>
                    <a href="{{ route('petugas.statistik.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Statistik
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection