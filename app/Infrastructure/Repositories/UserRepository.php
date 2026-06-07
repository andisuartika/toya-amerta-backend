<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\DTOs\User\UserDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class UserRepository implements UserRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null, ?string $role = null): LengthAwarePaginator
    {
        return User::with('roles')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                                         ->orWhere('email', 'like', "%{$search}%"))
            ->when($role, fn ($q) => $q->where('role', $role))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function create(UserDTO $dto): User
    {
        $user = User::create([
            'name'              => $dto->name,
            'email'             => $dto->email,
            'phone'             => $dto->phone,
            'role'              => $dto->role,
            'is_active'         => $dto->is_active,
            'password'          => bcrypt($dto->password),
            'email_verified_at' => now(),
        ]);

        $spatieRole = Role::firstOrCreate(['name' => $dto->role, 'guard_name' => 'web']);
        $user->syncRoles($spatieRole);

        return $user;
    }

    public function update(int $id, UserDTO $dto): User
    {
        $user = $this->findById($id);

        $data = [
            'name'      => $dto->name,
            'email'     => $dto->email,
            'phone'     => $dto->phone,
            'role'      => $dto->role,
            'is_active' => $dto->is_active,
        ];

        if ($dto->password) {
            $data['password'] = bcrypt($dto->password);
        }

        $user->update($data);

        $spatieRole = Role::firstOrCreate(['name' => $dto->role, 'guard_name' => 'web']);
        $user->syncRoles($spatieRole);

        return $user->fresh();
    }

    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }

    public function toggleActive(int $id): User
    {
        $user = $this->findById($id);
        $user->update(['is_active' => !$user->is_active]);
        return $user->fresh();
    }
}
