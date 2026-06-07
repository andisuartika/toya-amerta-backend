<?php

namespace App\Http\Controllers\Admin;

use App\Domain\DTOs\User\UserDTO;
use App\Domain\UseCases\User\CreateUserUseCase;
use App\Domain\UseCases\User\DeleteUserUseCase;
use App\Domain\UseCases\User\UpdateUserUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private CreateUserUseCase $createUseCase,
        private UpdateUserUseCase $updateUseCase,
        private DeleteUserUseCase $deleteUseCase,
    ) {}

    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->when($request->status !== null && $request->status !== '',
                fn ($q) => $q->where('is_active', $request->status)
            )
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->createUseCase->execute(UserDTO::fromArray($request->validated()));
        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(UserRequest $request, int $user): RedirectResponse
    {
        $this->updateUseCase->execute($user, UserDTO::fromArray($request->validated()));
        return back()->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(int $user): RedirectResponse
    {
        try {
            $this->deleteUseCase->execute($user);
            return back()->with('success', 'Pengguna berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['user'][0]);
        }
    }
}
