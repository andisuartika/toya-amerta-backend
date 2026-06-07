<?php

namespace App\Domain\UseCases\User;

use App\Domain\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DeleteUserUseCase
{
    public function __construct(private UserRepositoryInterface $repo) {}

    public function execute(int $id): void
    {
        if ($id === Auth::id()) {
            throw ValidationException::withMessages([
                'user' => 'Tidak bisa menghapus akun yang sedang login.',
            ]);
        }

        $this->repo->delete($id);
    }
}
