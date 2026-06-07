<?php

namespace App\Domain\UseCases\Zone;

use App\Domain\Contracts\ZoneRepositoryInterface;
use App\Domain\DTOs\Zone\ZoneDTO;
use App\Models\Zone;

class CreateZoneUseCase
{
    public function __construct(private ZoneRepositoryInterface $repo) {}

    public function execute(ZoneDTO $dto): Zone
    {
        return $this->repo->create($dto);
    }
}
