<?php

namespace App\Domain\Contracts;

use App\Domain\DTOs\Maintenance\MaintenanceDTO;
use App\Models\Maintenance;
use Illuminate\Support\Collection;

interface MaintenanceRepositoryInterface
{
    public function all(array $filters = []): Collection;
    public function findById(int $id): Maintenance;
    public function create(MaintenanceDTO $dto, int $reportedBy): Maintenance;
    public function update(int $id, array $data): Maintenance;
    public function delete(int $id): void;
    public function updateStatus(int $id, string $status, array $extra = []): Maintenance;
}
