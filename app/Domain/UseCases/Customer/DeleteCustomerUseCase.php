<?php

namespace App\Domain\UseCases\Customer;

use App\Domain\Contracts\CustomerRepositoryInterface;

class DeleteCustomerUseCase
{
    public function __construct(private CustomerRepositoryInterface $repo) {}

    public function execute(int $id): void
    {
        $this->repo->delete($id);
    }
}
