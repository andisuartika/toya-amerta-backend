<?php

namespace App\Domain\UseCases\MasterMeterReading;

use App\Domain\Contracts\MasterMeterReadingRepositoryInterface;

class DeleteMasterMeterReadingUseCase
{
    public function __construct(private MasterMeterReadingRepositoryInterface $repo) {}

    public function execute(int $id): void
    {
        $this->repo->delete($id);
    }
}
