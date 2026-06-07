<?php

namespace App\Domain\UseCases\TariffRate;

use App\Domain\Contracts\TariffRateRepositoryInterface;
use App\Domain\DTOs\TariffRate\TariffRateDTO;
use App\Models\TariffRate;

class UpdateTariffRateUseCase
{
    public function __construct(private TariffRateRepositoryInterface $repo) {}

    public function execute(int $id, TariffRateDTO $dto): TariffRate
    {
        return $this->repo->update($id, $dto);
    }
}
