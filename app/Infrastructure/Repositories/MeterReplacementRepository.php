<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\MeterReplacementRepositoryInterface;
use App\Domain\DTOs\MeterReplacement\MeterReplacementDTO;
use App\Models\MeterReplacement;
use Illuminate\Support\Collection;

class MeterReplacementRepository implements MeterReplacementRepositoryInterface
{
    public function create(MeterReplacementDTO $dto): MeterReplacement
    {
        return MeterReplacement::create([
            'customer_id'        => $dto->customer_id,
            'old_reading_final'  => $dto->old_reading_final,
            'new_reading_start'  => $dto->new_reading_start,
            'replaced_at'        => $dto->replaced_at,
            'reason'             => $dto->reason,
            'notes'              => $dto->notes,
            'recorded_by'        => $dto->recorded_by,
        ]);
    }

    public function forCustomer(int $customerId): Collection
    {
        return MeterReplacement::with('recordedBy')
            ->where('customer_id', $customerId)
            ->orderByDesc('replaced_at')
            ->orderByDesc('id')
            ->get();
    }

    public function latestForCustomer(int $customerId): ?MeterReplacement
    {
        return MeterReplacement::where('customer_id', $customerId)
            ->orderByDesc('replaced_at')
            ->orderByDesc('id')
            ->first();
    }
}
