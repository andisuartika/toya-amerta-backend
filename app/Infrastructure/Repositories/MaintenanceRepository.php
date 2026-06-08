<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\MaintenanceRepositoryInterface;
use App\Domain\DTOs\Maintenance\MaintenanceDTO;
use App\Models\Maintenance;
use Illuminate\Support\Collection;

class MaintenanceRepository implements MaintenanceRepositoryInterface
{
    public function all(array $filters = []): Collection
    {
        return Maintenance::with(['zone', 'customer', 'reportedBy'])
            ->when($filters['status'] ?? null, fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['priority'] ?? null, fn ($q) => $q->where('priority', $filters['priority']))
            ->when($filters['category'] ?? null, fn ($q) => $q->where('category', $filters['category']))
            ->when($filters['zone_id'] ?? null, fn ($q) => $q->where('zone_id', $filters['zone_id']))
            ->orderByRaw("FIELD(priority, 'darurat','tinggi','sedang','rendah')")
            ->orderByDesc('reported_date')
            ->orderByDesc('id')
            ->limit(200)
            ->get();
    }

    public function findById(int $id): Maintenance
    {
        return Maintenance::with(['zone', 'customer', 'reportedBy'])->findOrFail($id);
    }

    public function create(MaintenanceDTO $dto, int $reportedBy): Maintenance
    {
        return Maintenance::create([
            'title'         => $dto->title,
            'location'      => $dto->location,
            'category'      => $dto->category,
            'priority'      => $dto->priority,
            'reported_date' => $dto->reported_date,
            'zone_id'       => $dto->zone_id,
            'customer_id'   => $dto->customer_id,
            'reported_by'   => $reportedBy,
            'description'   => $dto->description,
            'material_cost' => $dto->material_cost,
            'labor_cost'    => $dto->labor_cost,
            'status'        => 'dilaporkan',
        ]);
    }

    public function update(int $id, array $data): Maintenance
    {
        $maintenance = $this->findById($id);
        $maintenance->update($data);
        return $maintenance->fresh();
    }

    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }

    public function updateStatus(int $id, string $status, array $extra = []): Maintenance
    {
        $maintenance = $this->findById($id);

        $data = array_merge(['status' => $status], $extra);

        if ($status === 'dalam_proses' && ! $maintenance->handled_date) {
            $data['handled_date'] = now()->toDateString();
        }

        if ($status === 'selesai' && ! $maintenance->completed_date) {
            $data['completed_date'] = now()->toDateString();
        }

        $maintenance->update($data);
        return $maintenance->fresh();
    }
}
