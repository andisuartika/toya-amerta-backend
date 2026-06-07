<?php

namespace App\Domain\UseCases\Customer;

use App\Domain\Contracts\CustomerRepositoryInterface;
use App\Domain\DTOs\Customer\CustomerDTO;
use App\Models\Customer;

class CreateCustomerUseCase
{
    public function __construct(private CustomerRepositoryInterface $repo) {}

    public function execute(CustomerDTO $dto): Customer
    {
        return $this->repo->create($dto);
    }
}
