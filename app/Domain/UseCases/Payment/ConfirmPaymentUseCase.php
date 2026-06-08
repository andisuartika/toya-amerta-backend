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

        $alreadyPaid = PaymentRecord::where('water_reading_id', $reading->id)->sum('amount_paid');
        $remaining   = $reading->total_amount - $alreadyPaid;

        if ($dto->amount_paid > $remaining) {
            throw ValidationException::withMessages([
                'amount_paid' => 'Jumlah bayar (Rp ' . number_format($dto->amount_paid, 0, ',', '.') . ') melebihi sisa tagihan (Rp ' . number_format($remaining, 0, ',', '.') . ').',
            ]);
        }

        return $this->repo->create($dto, $recordedBy);
    }
}
