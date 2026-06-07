<?php

namespace App\Domain\Contracts;

use App\Domain\DTOs\User\UserDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null, ?string $role = null): LengthAwarePaginator;
    public function findById(int $id): User;
    public function create(UserDTO $dto): User;
    public function update(int $id, UserDTO $dto): User;
    public function delete(int $id): void;
    public function toggleActive(int $id): User;
}
