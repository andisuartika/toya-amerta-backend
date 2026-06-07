<?php

namespace App\Domain\DTOs\Payment;

readonly class PaymentDTO
{
    public function __construct(
        public int    $water_reading_id,
        public float  $amount_paid,
        public string $payment_date,
        public string $payment_method,
        public ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            water_reading_id: (int) $data['water_reading_id'],
            amount_paid:      (float) $data['amount_paid'],
            payment_date:     $data['payment_date'],
            payment_method:   $data['payment_method'],
            notes:            $data['notes'] ?? null,
        );
    }
}
