<?php

namespace App\Domain\UseCases\User;

use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\DTOs\User\UserDTO;
use App\Models\User;

class CreateUserUseCase
{
    public function __construct(private UserRepositoryInterface $repo) {}

    public function execute(UserDTO $dto): User
    {
        return $this->repo->create($dto);
    }
}
