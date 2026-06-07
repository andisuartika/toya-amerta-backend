<?php

namespace App\Domain\Contracts;

use App\Domain\DTOs\TariffRate\TariffRateDTO;
use App\Models\TariffRate;
use Illuminate\Pagination\LengthAwarePaginator;

interface TariffRateRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
    public function findById(int $id): TariffRate;
    public function create(TariffRateDTO $dto): TariffRate;
    public function update(int $id, TariffRateDTO $dto): TariffRate;
    public function delete(int $id): void;
    public function toggleActive(int $id): TariffRate;
    public function allActive(): \Illuminate\Database\Eloquent\Collection;
}
