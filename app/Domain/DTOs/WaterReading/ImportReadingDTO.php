<?php

namespace App\Domain\DTOs\WaterReading;

readonly class ImportReadingDTO
{
    public function __construct(
        public int     $customer_id,
        public int     $officer_id,
        public int     $period_year,
        public int     $period_month,
        public float   $previous_reading,
        public float   $current_reading,
        public float   $price_per_m3,
        public float   $total_amount,
        public string  $reading_date,
        public string  $payment_status, // belum_bayar | sebagian | lunas
        public ?float  $amount_paid = null,
    ) {}
}
