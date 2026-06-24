<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\WaterReadingRepositoryInterface;
use App\Domain\DTOs\WaterReading\ImportReadingDTO;
use App\Domain\DTOs\WaterReading\WaterReadingDTO;
use App\Models\Customer;
use App\Models\WaterReading;
use Illuminate\Support\Collection;

class WaterReadingRepository implements WaterReadingRepositoryInterface
{
    public function create(WaterReadingDTO $dto): WaterReading
    {
        $customer = Customer::with('tariffRate')->findOrFail($dto->customer_id);
        $tariff   = $customer->tariffRate;

        $previous = $this->lastReading($dto->customer_id);
        $previousReading = $previous?->current_reading ?? $customer->initial_meter;

        $usageM3       = max(0, $dto->current_reading - $previousReading);
        $billableUsage = max($usageM3, $tariff->minimum_usage);
        $usageCost     = $billableUsage * $tariff->price_per_m3;
        $totalAmount   = max($usageCost, $tariff->minimum_charge);

        return WaterReading::create([
            'customer_id'      => $dto->customer_id,
            'officer_id'       => $dto->officer_id,
            'period_year'      => $dto->period_year,
            'period_month'     => $dto->period_month,
            'previous_reading' => $previousReading,
            'current_reading'  => $dto->current_reading,
            'tariff_rate_id'   => $tariff->id,
            'price_per_m3'     => $tariff->price_per_m3,
            'minimum_charge'   => $tariff->minimum_charge,
            'minimum_usage'    => $tariff->minimum_usage,
            'total_amount'     => $totalAmount,
            'reading_date'     => $dto->reading_date,
            'notes'            => $dto->notes,
            'photo_url'        => $dto->photo_url,
            'payment_status'   => 'belum_bayar',
        ]);
    }

    public function createImported(ImportReadingDTO $dto): WaterReading
    {
        $customer = Customer::with('tariffRate')->findOrFail($dto->customer_id);
        $tariff   = $customer->tariffRate;

        return WaterReading::create([
            'customer_id'      => $dto->customer_id,
            'officer_id'       => $dto->officer_id,
            'period_year'      => $dto->period_year,
            'period_month'     => $dto->period_month,
            'previous_reading' => $dto->previous_reading,
            'current_reading'  => $dto->current_reading,
            'tariff_rate_id'   => $tariff?->id,
            'price_per_m3'     => $dto->price_per_m3,
            'minimum_charge'   => $tariff?->minimum_charge,
            'minimum_usage'    => $tariff?->minimum_usage,
            'total_amount'     => $dto->total_amount,
            'reading_date'     => $dto->reading_date,
            'notes'            => 'Diimpor dari data historis',
            'payment_status'   => 'belum_bayar',
        ]);
    }

    public function findById(int $id): WaterReading
    {
        return WaterReading::with(['customer', 'officer', 'tariffRate'])->findOrFail($id);
    }

    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }

    public function forPeriod(int $year, int $month): Collection
    {
        return WaterReading::with(['customer.zone', 'officer'])
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->orderBy('reading_date', 'desc')
            ->get();
    }

    public function forCustomer(int $customerId, int $limit = 12): Collection
    {
        return WaterReading::with(['officer'])
            ->where('customer_id', $customerId)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->limit($limit)
            ->get();
    }

    public function lastReading(int $customerId): ?WaterReading
    {
        return WaterReading::where('customer_id', $customerId)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->first();
    }

    public function existsForPeriod(int $customerId, int $year, int $month, ?int $excludeId = null): bool
    {
        return WaterReading::where('customer_id', $customerId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }
}
