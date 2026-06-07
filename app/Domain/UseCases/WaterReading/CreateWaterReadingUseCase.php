<?php

namespace App\Domain\UseCases\WaterReading;

use App\Domain\Contracts\WaterReadingRepositoryInterface;
use App\Domain\DTOs\WaterReading\WaterReadingDTO;
use App\Models\WaterReading;
use Illuminate\Validation\ValidationException;

class CreateWaterReadingUseCase
{
    public function __construct(private WaterReadingRepositoryInterface $repo) {}

    public function execute(WaterReadingDTO $dto): WaterReading
    {
        if ($this->repo->existsForPeriod($dto->customer_id, $dto->period_year, $dto->period_month)) {
            throw ValidationException::withMessages([
                'period' => 'Pelanggan ini sudah dicatat untuk periode ' . $dto->period_month . '/' . $dto->period_year . '.',
            ]);
        }

        return $this->repo->create($dto);
    }
}
