<?php

namespace App\Domain\UseCases\TariffRate;

use App\Domain\Contracts\TariffRateRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListTariffRatesUseCase
{
    public function __construct(private TariffRateRepositoryInterface $repo) {}

    public function execute(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $search);
    }
}
