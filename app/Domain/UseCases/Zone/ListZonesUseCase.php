<?php

namespace App\Domain\UseCases\Zone;

use App\Domain\Contracts\ZoneRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListZonesUseCase
{
    public function __construct(private ZoneRepositoryInterface $repo) {}

    public function execute(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $search);
    }
}
