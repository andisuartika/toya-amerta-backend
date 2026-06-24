<?php

namespace App\Domain\UseCases\WaterReading;

use App\Domain\Contracts\PaymentRepositoryInterface;
use App\Domain\Contracts\WaterReadingRepositoryInterface;
use App\Domain\DTOs\Payment\PaymentDTO;
use App\Domain\DTOs\WaterReading\ImportReadingDTO;

class ImportWaterReadingRowUseCase
{
    public function __construct(
        private WaterReadingRepositoryInterface $waterReadingRepo,
        private PaymentRepositoryInterface $paymentRepo,
    ) {}

    public function execute(ImportReadingDTO $dto, int $recordedBy): array
    {
        if ($this->waterReadingRepo->existsForPeriod($dto->customer_id, $dto->period_year, $dto->period_month)) {
            return ['status' => 'skipped', 'message' => 'Sudah ada pencatatan untuk periode ini, baris dilewati.'];
        }

        $reading = $this->waterReadingRepo->createImported($dto);

        if ($dto->payment_status !== 'belum_bayar') {
            $amountPaid = $dto->payment_status === 'lunas'
                ? $dto->total_amount
                : (float) $dto->amount_paid;

            $paymentDto = PaymentDTO::fromArray([
                'water_reading_id' => $reading->id,
                'amount_paid'      => $amountPaid,
                'payment_date'     => $dto->reading_date,
                'payment_method'   => 'tunai',
                'notes'            => 'Pembayaran diimpor dari data historis',
            ]);

            $this->paymentRepo->create($paymentDto, $recordedBy);
        }

        return ['status' => 'created', 'water_reading_id' => $reading->id];
    }
}
