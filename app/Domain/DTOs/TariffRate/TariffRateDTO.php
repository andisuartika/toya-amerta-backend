<?php

namespace App\Domain\DTOs\TariffRate;

readonly class TariffRateDTO
{
    public function __construct(
        public string $name,
        public float  $price_per_m3,
        public float  $minimum_charge,
        public float  $minimum_usage,
        public bool   $is_active,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:           $data['name'],
            price_per_m3:   (float) $data['price_per_m3'],
            minimum_charge: (float) $data['minimum_charge'],
            minimum_usage:  (float) $data['minimum_usage'],
            is_active:      (bool) ($data['is_active'] ?? true),
        );
    }
}
