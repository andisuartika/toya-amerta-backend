<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\CashTransactionController;
use App\Http\Controllers\Admin\NewCustomerFeeController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\WaterReadingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TariffRateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

// ── Auth ───────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password',  [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ── Admin Panel ────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|petugas'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {

        // Zona Wilayah
        Route::get('zones',            [ZoneController::class, 'index'])->name('zones.index');
        Route::post('zones',           [ZoneController::class, 'store'])->name('zones.store');
        Route::put('zones/{zone}',     [ZoneController::class, 'update'])->name('zones.update');
        Route::delete('zones/{zone}',  [ZoneController::class, 'destroy'])->name('zones.destroy');

        // Tarif Air
        Route::get('tariff-rates',                 [TariffRateController::class, 'index'])->name('tariff-rates.index');
        Route::post('tariff-rates',                [TariffRateController::class, 'store'])->name('tariff-rates.store');
        Route::put('tariff-rates/{tariffRate}',    [TariffRateController::class, 'update'])->name('tariff-rates.update');
        Route::delete('tariff-rates/{tariffRate}', [TariffRateController::class, 'destroy'])->name('tariff-rates.destroy');

        // Pelanggan
        Route::get('customers/generate-number', [CustomerController::class, 'generateNumber'])->name('customers.generate-number');
        Route::get('customers',                 [CustomerController::class, 'index'])->name('customers.index');
        Route::post('customers',                [CustomerController::class, 'store'])->name('customers.store');
        Route::put('customers/{customer}',      [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('customers/{customer}',   [CustomerController::class, 'destroy'])->name('customers.destroy');

        // Pembayaran
        Route::get('payments/history',       [PaymentController::class, 'history'])->name('payments.history');
        Route::get('payments',               [PaymentController::class, 'index'])->name('payments.index');
        Route::post('payments',              [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('payments/{payment}',  [PaymentController::class, 'destroy'])->name('payments.destroy');

        // Kas Transaksi
        Route::get('cash-transactions',                    [CashTransactionController::class, 'index'])->name('cash-transactions.index');
        Route::post('cash-transactions',                   [CashTransactionController::class, 'store'])->name('cash-transactions.store');
        Route::put('cash-transactions/{cashTransaction}',  [CashTransactionController::class, 'update'])->name('cash-transactions.update');
        Route::delete('cash-transactions/{cashTransaction}', [CashTransactionController::class, 'destroy'])->name('cash-transactions.destroy');

        // Biaya Pemasangan Pelanggan Baru
        Route::get('new-customer-fees',                    [NewCustomerFeeController::class, 'index'])->name('new-customer-fees.index');
        Route::delete('new-customer-fees/{newCustomerFee}', [NewCustomerFeeController::class, 'destroy'])->name('new-customer-fees.destroy');

        // Maintenance
        Route::get('maintenances',                        [MaintenanceController::class, 'index'])->name('maintenances.index');
        Route::post('maintenances',                       [MaintenanceController::class, 'store'])->name('maintenances.store');
        Route::put('maintenances/{maintenance}',          [MaintenanceController::class, 'update'])->name('maintenances.update');
        Route::patch('maintenances/{maintenance}/status', [MaintenanceController::class, 'updateStatus'])->name('maintenances.update-status');
        Route::delete('maintenances/{maintenance}',       [MaintenanceController::class, 'destroy'])->name('maintenances.destroy');

        // Pencatatan Meteran
        Route::get('water-readings/history',              [WaterReadingController::class, 'history'])->name('water-readings.history');
        Route::get('water-readings/customer/{customer}',  [WaterReadingController::class, 'customerInfo'])->name('water-readings.customer-info');
        Route::get('water-readings/unrecorded',            [WaterReadingController::class, 'unrecordedCustomers'])->name('water-readings.unrecorded');
        Route::get('water-readings',                      [WaterReadingController::class, 'index'])->name('water-readings.index');
        Route::post('water-readings',                     [WaterReadingController::class, 'store'])->name('water-readings.store');
        Route::delete('water-readings/{waterReading}',   [WaterReadingController::class, 'destroy'])->name('water-readings.destroy');

        // Pengguna
        Route::get('users',            [UserController::class, 'index'])->name('users.index');
        Route::post('users',           [UserController::class, 'store'])->name('users.store');
        Route::put('users/{user}',     [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}',  [UserController::class, 'destroy'])->name('users.destroy');
    });
});

Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->middleware('auth')->name('dashboard');
