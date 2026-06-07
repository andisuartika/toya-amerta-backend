<?php

namespace App\Domain\Contracts;

use App\Domain\DTOs\WaterReading\WaterReadingDTO;
use App\Models\WaterReading;
use Illuminate\Support\Collection;

interface WaterReadingRepositoryInterface
{
    public function create(WaterReadingDTO $dto): WaterReading;

    public function findById(int $id): WaterReading;

    public function delete(int $id): void;

    /** Semua pembacaan untuk periode tertentu */
    public function forPeriod(int $year, int $month): Collection;

    /** Riwayat pembacaan untuk satu pelanggan */
    public function forCustomer(int $customerId, int $limit = 12): Collection;

    /** Pembacaan terakhir pelanggan (untuk mengisi previous_reading otomatis) */
    public function lastReading(int $customerId): ?WaterReading;

    /** Cek apakah pelanggan sudah dicatat di periode ini */
    public function existsForPeriod(int $customerId, int $year, int $month, ?int $excludeId = null): bool;
}
