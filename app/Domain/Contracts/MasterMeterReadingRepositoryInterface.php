<?php

namespace App\Domain\Contracts;

use App\Domain\DTOs\MasterMeterReading\MasterMeterReadingDTO;
use App\Models\MasterMeterReading;
use Illuminate\Database\Eloquent\Collection;

interface MasterMeterReadingRepositoryInterface
{
    public function all(int $limit = 24): Collection;
    public function findById(int $id): MasterMeterReading;
    public function existsForPeriod(int $year, int $month): bool;
    public function lastReading(): ?MasterMeterReading;
    public function create(MasterMeterReadingDTO $dto): MasterMeterReading;
    public function delete(int $id): void;
}
