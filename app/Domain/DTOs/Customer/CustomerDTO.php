<?php

namespace App\Domain\DTOs\Customer;

readonly class CustomerDTO
{
    public function __construct(
        public string  $customer_number,
        public string  $name,
        public string  $address,
        public ?string $phone,
        public int     $zone_id,
        public int     $tariff_rate_id,
        public ?string $installation_date,
        public float   $initial_meter,
        public bool    $is_active,
        public ?string $notes,
        public ?int    $user_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            customer_number:   $data['customer_number'],
            name:              $data['name'],
            address:           $data['address'],
            phone:             $data['phone'] ?? null,
            zone_id:           (int) $data['zone_id'],
            tariff_rate_id:    (int) $data['tariff_rate_id'],
            installation_date: $data['installation_date'],
            initial_meter:     (float) ($data['initial_meter'] ?? 0),
            is_active:         (bool) ($data['is_active'] ?? true),
            notes:             $data['notes'] ?? null,
            user_id:           isset($data['user_id']) ? (int) $data['user_id'] : null,
        );
    }
}
