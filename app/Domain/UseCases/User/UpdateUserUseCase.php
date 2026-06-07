<?php

namespace App\Domain\UseCases\User;

use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\DTOs\User\UserDTO;
use App\Models\User;

class UpdateUserUseCase
{
    public function __construct(private UserRepositoryInterface $repo) {}

    public function execute(int $id, UserDTO $dto): User
    {
        return $this->repo->update($id, $dto);
    }
}
