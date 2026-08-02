<?php

namespace App\Domain\DTOs\MeterReplacement;

readonly class MeterReplacementDTO
{
    public function __construct(
        public int     $customer_id,
        public float   $old_reading_final,
        public float   $new_reading_start,
        public string  $replaced_at,
        public ?string $reason,
        public ?string $notes,
        public ?int    $recorded_by,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            customer_id:       (int) $data['customer_id'],
            old_reading_final: (float) ($data['old_reading_final'] ?? 0),
            new_reading_start: (float) ($data['new_reading_start'] ?? 0),
            replaced_at:       $data['replaced_at'],
            reason:            $data['reason'] ?? null,
            notes:             $data['notes'] ?? null,
            recorded_by:       isset($data['recorded_by']) ? (int) $data['recorded_by'] : null,
        );
    }
}
