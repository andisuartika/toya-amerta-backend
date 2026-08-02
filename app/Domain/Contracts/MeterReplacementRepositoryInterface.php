<?php

namespace App\Domain\Contracts;

use App\Domain\DTOs\MeterReplacement\MeterReplacementDTO;
use App\Models\MeterReplacement;
use Illuminate\Support\Collection;

interface MeterReplacementRepositoryInterface
{
    public function create(MeterReplacementDTO $dto): MeterReplacement;

    /** Riwayat penggantian meteran pelanggan, terbaru lebih dulu */
    public function forCustomer(int $customerId): Collection;

    /** Penggantian meteran terakhir pelanggan (jika ada) */
    public function latestForCustomer(int $customerId): ?MeterReplacement;
}
