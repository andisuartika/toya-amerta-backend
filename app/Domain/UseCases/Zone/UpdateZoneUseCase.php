<?php

namespace App\Domain\UseCases\Zone;

use App\Domain\Contracts\ZoneRepositoryInterface;
use App\Domain\DTOs\Zone\ZoneDTO;
use App\Models\Zone;

class UpdateZoneUseCase
{
    public function __construct(private ZoneRepositoryInterface $repo) {}

    public function execute(int $id, ZoneDTO $dto): Zone
    {
        return $this->repo->update($id, $dto);
    }
}
