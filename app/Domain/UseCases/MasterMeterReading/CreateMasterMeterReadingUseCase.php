<?php

namespace App\Domain\UseCases\MasterMeterReading;

use App\Domain\Contracts\MasterMeterReadingRepositoryInterface;
use App\Domain\DTOs\MasterMeterReading\MasterMeterReadingDTO;
use App\Models\MasterMeterReading;
use Illuminate\Validation\ValidationException;

class CreateMasterMeterReadingUseCase
{
    public function __construct(private MasterMeterReadingRepositoryInterface $repo) {}

    public function execute(MasterMeterReadingDTO $dto): MasterMeterReading
    {
        if ($this->repo->existsForPeriod($dto->period_year, $dto->period_month)) {
            throw ValidationException::withMessages([
                'period' => 'Meteran induk sudah dicatat untuk periode ' . $dto->period_month . '/' . $dto->period_year . '.',
            ]);
        }

        return $this->repo->create($dto);
    }
}
