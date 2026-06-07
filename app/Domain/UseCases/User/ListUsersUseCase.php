<?php

namespace App\Domain\UseCases\User;

use App\Domain\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListUsersUseCase
{
    public function __construct(private UserRepositoryInterface $repo) {}

    public function execute(int $perPage = 15, ?string $search = null, ?string $role = null): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $search, $role);
    }
}
