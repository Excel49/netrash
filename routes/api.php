<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\QrCodeController;

// Gunakan middleware auth (bukan auth:sanctum jika belum setup sanctum)
Route::middleware(['auth'])->group(function () {
    // Scanner API
    Route::post('/scan', [QrCodeController::class, 'apiProcessScan']);
    Route::post('/warga/search-email', [PetugasController::class, 'searchByEmailApi']);
});