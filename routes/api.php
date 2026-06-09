<?php

use App\Http\Controllers\Api\Auth\ApiAuthController;
use App\Http\Controllers\Api\Pelanggan\PelangganController;
use App\Http\Controllers\Api\Petugas\MaintenanceApiController;
use App\Http\Controllers\Api\Petugas\PaymentApiController;
use App\Http\Controllers\Api\Petugas\WaterReadingApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Toya Amerta Mobile (Flutter)
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [ApiAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [ApiAuthController::class, 'me']);
        Route::post('logout', [ApiAuthController::class, 'logout']);
    });
});

// ── Pelanggan ─────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:pelanggan'])
    ->prefix('pelanggan')
    ->group(function () {
        Route::get('profile', [PelangganController::class, 'profile']);
        Route::get('tagihan', [PelangganController::class, 'tagihan']);
        Route::get('riwayat', [PelangganController::class, 'riwayat']);
    });

// ── Petugas ───────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:petugas|admin'])
    ->prefix('petugas')
    ->group(function () {
        // Daftar pelanggan aktif (untuk dropdown input meter)
        Route::get('customers', [WaterReadingApiController::class, 'customers']);

        // Catat meter
        Route::get('water-readings', [WaterReadingApiController::class, 'index']);
        Route::post('water-readings', [WaterReadingApiController::class, 'store']);
        Route::get('water-readings/{id}', [WaterReadingApiController::class, 'show']);

        // Tagihan & pembayaran
        Route::get('tagihan', [PaymentApiController::class, 'unpaid']);
        Route::post('payments', [PaymentApiController::class, 'store']);
        Route::get('payments/{id}', [PaymentApiController::class, 'show']);

        // Maintenance
        Route::get('maintenance', [MaintenanceApiController::class, 'index']);
        Route::post('maintenance', [MaintenanceApiController::class, 'store']);
        Route::get('maintenance/{id}', [MaintenanceApiController::class, 'show']);
        Route::patch('maintenance/{id}/status', [MaintenanceApiController::class, 'updateStatus']);
    });
