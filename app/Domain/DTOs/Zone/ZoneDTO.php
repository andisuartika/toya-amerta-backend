<?php

namespace App\Domain\DTOs\Zone;

readonly class ZoneDTO
{
    public function __construct(
        public string $name,
        public string $code,
        public ?string $description,
        public bool $is_active,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:        $data['name'],
            code:        strtoupper($data['code']),
            description: $data['description'] ?? null,
            is_active:   (bool) ($data['is_active'] ?? true),
        );
    }
}
