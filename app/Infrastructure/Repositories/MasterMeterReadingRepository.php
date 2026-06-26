<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\MasterMeterReadingRepositoryInterface;
use App\Domain\DTOs\MasterMeterReading\MasterMeterReadingDTO;
use App\Models\MasterMeterReading;
use Illuminate\Database\Eloquent\Collection;

class MasterMeterReadingRepository implements MasterMeterReadingRepositoryInterface
{
    public function all(int $limit = 24): Collection
    {
        return MasterMeterReading::with('recordedBy')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->limit($limit)
            ->get();
    }

    public function findById(int $id): MasterMeterReading
    {
        return MasterMeterReading::findOrFail($id);
    }

    public function existsForPeriod(int $year, int $month): bool
    {
        return MasterMeterReading::where('period_year', $year)
            ->where('period_month', $month)
            ->exists();
    }

    public function lastReading(): ?MasterMeterReading
    {
        return MasterMeterReading::orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->first();
    }

    public function create(MasterMeterReadingDTO $dto): MasterMeterReading
    {
        $previousReading = $this->lastReading()?->current_reading ?? 0;

        return MasterMeterReading::create([
            'period_year'      => $dto->period_year,
            'period_month'     => $dto->period_month,
            'previous_reading' => $previousReading,
            'current_reading'  => $dto->current_reading,
            'reading_date'     => $dto->reading_date,
            'notes'            => $dto->notes,
            'photo_url'        => $dto->photo_url,
            'recorded_by'      => $dto->recorded_by,
        ]);
    }

    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
