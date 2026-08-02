<?php

namespace App\Domain\UseCases\MeterReplacement;

use App\Domain\Contracts\MeterReplacementRepositoryInterface;
use App\Domain\Contracts\WaterReadingRepositoryInterface;
use App\Domain\DTOs\MeterReplacement\MeterReplacementDTO;
use App\Models\MeterReplacement;

class ReplaceMeterUseCase
{
    public function __construct(
        private MeterReplacementRepositoryInterface $repo,
        private WaterReadingRepositoryInterface      $waterReadingRepo,
    ) {}

    public function execute(MeterReplacementDTO $dto): MeterReplacement
    {
        $oldReadingFinal = $this->waterReadingRepo->baselineReading($dto->customer_id);

        return $this->repo->create(new MeterReplacementDTO(
            customer_id:       $dto->customer_id,
            old_reading_final: $oldReadingFinal,
            new_reading_start: $dto->new_reading_start,
            replaced_at:       $dto->replaced_at,
            reason:            $dto->reason,
            notes:             $dto->notes,
            recorded_by:       $dto->recorded_by,
        ));
    }
}
