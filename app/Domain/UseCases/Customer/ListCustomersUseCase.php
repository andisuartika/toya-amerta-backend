<?php

namespace App\Domain\UseCases\Customer;

use App\Domain\Contracts\CustomerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListCustomersUseCase
{
    public function __construct(private CustomerRepositoryInterface $repo) {}

    public function execute(int $perPage = 15, ?string $search = null, ?int $zoneId = null): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $search, $zoneId);
    }
}
