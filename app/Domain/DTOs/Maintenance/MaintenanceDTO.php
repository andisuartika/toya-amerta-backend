<?php

namespace App\Domain\DTOs\Maintenance;

readonly class MaintenanceDTO
{
    public function __construct(
        public string  $title,
        public string  $location,
        public string  $category,
        public string  $priority,
        public string  $reported_date,
        public ?int    $zone_id,
        public ?int    $customer_id,
        public ?string $description,
        public ?float  $material_cost,
        public ?float  $labor_cost,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title:         $data['title'],
            location:      $data['location'],
            category:      $data['category'],
            priority:      $data['priority'],
            reported_date: $data['reported_date'],
            zone_id:       isset($data['zone_id']) && $data['zone_id'] !== '' ? (int) $data['zone_id'] : null,
            customer_id:   isset($data['customer_id']) && $data['customer_id'] !== '' ? (int) $data['customer_id'] : null,
            description:   $data['description'] ?? null,
            material_cost: isset($data['material_cost']) && $data['material_cost'] !== '' ? (float) $data['material_cost'] : null,
            labor_cost:    isset($data['labor_cost']) && $data['labor_cost'] !== '' ? (float) $data['labor_cost'] : null,
        );
    }
}
