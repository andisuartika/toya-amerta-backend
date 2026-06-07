<?php

namespace App\Domain\DTOs\User;

readonly class UserDTO
{
    public function __construct(
        public string  $name,
        public string  $email,
        public ?string $phone,
        public string  $role,
        public bool    $is_active,
        public ?string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:      $data['name'],
            email:     $data['email'],
            phone:     $data['phone'] ?? null,
            role:      $data['role'],
            is_active:   (bool) ($data['is_active'] ?? false),
            password:  $data['password'] ?? null,
        );
    }
}
