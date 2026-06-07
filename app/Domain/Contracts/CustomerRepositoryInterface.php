<?php

namespace App\Domain\Contracts;

use App\Domain\DTOs\Customer\CustomerDTO;
use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null, ?int $zoneId = null): LengthAwarePaginator;
    public function findById(int $id): Customer;
    public function create(CustomerDTO $dto): Customer;
    public function update(int $id, CustomerDTO $dto): Customer;
    public function delete(int $id): void;
    public function toggleActive(int $id): Customer;
    public function generateCustomerNumber(): string;
}
