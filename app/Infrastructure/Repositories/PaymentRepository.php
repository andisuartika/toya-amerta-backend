<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\PaymentRepositoryInterface;
use App\Domain\DTOs\Payment\PaymentDTO;
use App\Models\PaymentRecord;
use App\Models\WaterReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(PaymentDTO $dto, int $recordedBy): PaymentRecord
    {
        return DB::transaction(function () use ($dto, $recordedBy) {
            $reading = WaterReading::with('customer')->findOrFail($dto->water_reading_id);

            // Tentukan status: lunas jika bayar >= total tagihan
            $status = $dto->amount_paid >= $reading->total_amount ? 'lunas' : 'sebagian';

            $payment = PaymentRecord::create([
                'water_reading_id' => $reading->id,
                'customer_id'      => $reading->customer_id,
                'officer_id'       => $recordedBy,
                'recorded_by'      => $recordedBy,
                'amount_paid'      => $dto->amount_paid,
                'payment_date'     => $dto->payment_date,
                'payment_method'   => $dto->payment_method,
                'status'           => $status,
                'receipt_number'   => $this->generateReceiptNumber(),
                'notes'            => $dto->notes,
            ]);

            // Update status di water_reading
            $reading->update(['payment_status' => $status]);

            return $payment;
        });
    }

    public function findById(int $id): PaymentRecord
    {
        return PaymentRecord::with(['waterReading.customer', 'recordedBy'])->findOrFail($id);
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $payment = $this->findById($id);
            $reading = $payment->waterReading;

            $payment->delete();

            // Kembalikan status water_reading ke belum_bayar jika tidak ada pembayaran lain
            $otherPayments = PaymentRecord::where('water_reading_id', $reading->id)->exists();
            if (! $otherPayments) {
                $reading->update(['payment_status' => 'belum_bayar']);
            }
        });
    }

    public function unpaid(array $filters = []): Collection
    {
        return WaterReading::with(['customer.zone', 'customer.tariffRate'])
            ->join('customers', 'customers.id', '=', 'water_readings.customer_id')
            ->whereIn('water_readings.payment_status', ['belum_bayar', 'sebagian'])
            ->when($filters['zone_id'] ?? null, fn ($q) => $q->where('customers.zone_id', $filters['zone_id']))
            ->when($filters['month'] ?? null, fn ($q) => $q->where('water_readings.period_month', $filters['month']))
            ->when($filters['year'] ?? null, fn ($q) => $q->where('water_readings.period_year', $filters['year']))
            ->orderBy('water_readings.period_year')
            ->orderBy('water_readings.period_month')
            ->orderBy('customers.name')
            ->select('water_readings.*')
            ->get();
    }

    public function history(array $filters = []): Collection
    {
        return PaymentRecord::with(['waterReading', 'customer.zone', 'recordedBy'])
            ->when($filters['customer_id'] ?? null, fn ($q) => $q->where('customer_id', $filters['customer_id']))
            ->when($filters['date_from'] ?? null, fn ($q) => $q->where('payment_date', '>=', $filters['date_from']))
            ->when($filters['date_to'] ?? null, fn ($q) => $q->where('payment_date', '<=', $filters['date_to']))
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->limit(200)
            ->get();
    }

    public function generateReceiptNumber(): string
    {
        $prefix = 'KWT-' . date('Ymd') . '-';
        $last   = PaymentRecord::where('receipt_number', 'like', $prefix . '%')
                    ->orderByDesc('receipt_number')
                    ->value('receipt_number');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
