<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\ZoneRepositoryInterface;
use App\Domain\DTOs\Zone\ZoneDTO;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ZoneRepository implements ZoneRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return Zone::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                                         ->orWhere('code', 'like', "%{$search}%"))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): Zone
    {
        return Zone::findOrFail($id);
    }

    public function create(ZoneDTO $dto): Zone
    {
        return Zone::create([
            'name'        => $dto->name,
            'code'        => $dto->code,
            'description' => $dto->description,
            'is_active'   => $dto->is_active,
        ]);
    }

    public function update(int $id, ZoneDTO $dto): Zone
    {
        $zone = $this->findById($id);
        $zone->update([
            'name'        => $dto->name,
            'code'        => $dto->code,
            'description' => $dto->description,
            'is_active'   => $dto->is_active,
        ]);
        return $zone->fresh();
    }

    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }

    public function toggleActive(int $id): Zone
    {
        $zone = $this->findById($id);
        $zone->update(['is_active' => !$zone->is_active]);
        return $zone->fresh();
    }

    public function allActive(): Collection
    {
        return Zone::where('is_active', true)->orderBy('name')->get();
    }
}
