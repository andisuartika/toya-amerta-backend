<?php

namespace App\Domain\Contracts;

use App\Domain\DTOs\Zone\ZoneDTO;
use App\Models\Zone;
use Illuminate\Pagination\LengthAwarePaginator;

interface ZoneRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
    public function findById(int $id): Zone;
    public function create(ZoneDTO $dto): Zone;
    public function update(int $id, ZoneDTO $dto): Zone;
    public function delete(int $id): void;
    public function toggleActive(int $id): Zone;
    public function allActive(): \Illuminate\Database\Eloquent\Collection;
}
