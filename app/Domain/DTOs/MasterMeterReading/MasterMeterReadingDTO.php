<?php

namespace App\Domain\DTOs\MasterMeterReading;

readonly class MasterMeterReadingDTO
{
    public function __construct(
        public int     $period_year,
        public int     $period_month,
        public float   $current_reading,
        public string  $reading_date,
        public ?string $notes,
        public int     $recorded_by,
        public ?string $photo_url = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            period_year:   (int) $data['period_year'],
            period_month:  (int) $data['period_month'],
            current_reading: (float) $data['current_reading'],
            reading_date:  $data['reading_date'],
            notes:         $data['notes'] ?? null,
            recorded_by:   (int) $data['recorded_by'],
            photo_url:     $data['photo_url'] ?? null,
        );
    }
}
