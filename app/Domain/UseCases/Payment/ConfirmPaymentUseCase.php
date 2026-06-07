<?php

namespace App\Domain\UseCases\Payment;

use App\Domain\Contracts\PaymentRepositoryInterface;
use App\Domain\DTOs\Payment\PaymentDTO;
use App\Models\PaymentRecord;
use Illuminate\Validation\ValidationException;

class ConfirmPaymentUseCase
{
    public function __construct(private PaymentRepositoryInterface $repo) {}

    public function execute(PaymentDTO $dto, int $recordedBy): PaymentRecord
    {
        $reading = \App\Models\WaterReading::findOrFail($dto->water_reading_id);

        if ($reading->payment_status === 'lunas') {
            throw ValidationException::withMessages([
                'payment' => 'Tagihan ini sudah lunas.',
            ]);
        }

        if ($dto->amount_paid <= 0) {
            throw ValidationException::withMessages([
                'amount_paid' => 'Jumlah bayar harus lebih dari 0.',
            ]);
        }

        return $this->repo->create($dto, $recordedBy);
    }
}
