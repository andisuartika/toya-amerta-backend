<?php

namespace App\Domain\UseCases\Customer;

use App\Domain\Contracts\CustomerRepositoryInterface;
use App\Domain\DTOs\Customer\CustomerDTO;
use App\Models\Customer;

class UpdateCustomerUseCase
{
    public function __construct(private CustomerRepositoryInterface $repo) {}

    public function execute(int $id, CustomerDTO $dto): Customer
    {
        return $this->repo->update($id, $dto);
    }
}
