<?php

use App\Http\Controllers\Api\Auth\ApiAuthController;
use App\Http\Controllers\Api\Pelanggan\PelangganController;
use App\Http\Controllers\Api\Petugas\CustomerApiController;
use App\Http\Controllers\Api\Petugas\DashboardApiController;
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
        Route::post('profile', [ApiAuthController::class, 'updateProfile']);
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
        // Dashboard
        Route::get('dashboard', [DashboardApiController::class, 'index']);

        // Daftar pelanggan aktif (untuk dropdown input meter) & detail (riwayat pembacaan)
        Route::get('customers', [WaterReadingApiController::class, 'customers']);

        // Opsi zona & tarif untuk form tambah/edit pelanggan (didaftarkan sebelum customers/{id})
        Route::get('customers/form-options', [CustomerApiController::class, 'formOptions']);

        Route::get('customers/{id}', [WaterReadingApiController::class, 'customerDetail']);
        Route::get('customers/{id}/readings', [WaterReadingApiController::class, 'customerReadings']);

        // CRUD data master pelanggan
        Route::post('customers', [CustomerApiController::class, 'store']);
        Route::put('customers/{id}', [CustomerApiController::class, 'update']);
        Route::delete('customers/{id}', [CustomerApiController::class, 'destroy']);

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
