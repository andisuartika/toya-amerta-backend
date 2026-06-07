<?php

namespace App\Domain\DTOs\WaterReading;

readonly class WaterReadingDTO
{
    public function __construct(
        public int    $customer_id,
        public int    $officer_id,
        public int    $period_year,
        public int    $period_month,
        public float  $current_reading,
        public string $reading_date,
        public ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            customer_id:    (int) $data['customer_id'],
            officer_id:     (int) $data['officer_id'],
            period_year:    (int) $data['period_year'],
            period_month:   (int) $data['period_month'],
            current_reading: (float) $data['current_reading'],
            reading_date:   $data['reading_date'],
            notes:          $data['notes'] ?? null,
        );
    }
}
