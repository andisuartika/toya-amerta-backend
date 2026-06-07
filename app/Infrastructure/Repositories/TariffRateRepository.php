<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\TariffRateRepositoryInterface;
use App\Domain\DTOs\TariffRate\TariffRateDTO;
use App\Models\TariffRate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TariffRateRepository implements TariffRateRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return TariffRate::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): TariffRate
    {
        return TariffRate::findOrFail($id);
    }

    public function create(TariffRateDTO $dto): TariffRate
    {
        return TariffRate::create([
            'name'           => $dto->name,
            'price_per_m3'   => $dto->price_per_m3,
            'minimum_charge' => $dto->minimum_charge,
            'minimum_usage'  => $dto->minimum_usage,
            'is_active'      => $dto->is_active,
        ]);
    }

    public function update(int $id, TariffRateDTO $dto): TariffRate
    {
        $tariff = $this->findById($id);
        $tariff->update([
            'name'           => $dto->name,
            'price_per_m3'   => $dto->price_per_m3,
            'minimum_charge' => $dto->minimum_charge,
            'minimum_usage'  => $dto->minimum_usage,
            'is_active'      => $dto->is_active,
        ]);
        return $tariff->fresh();
    }

    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }

    public function toggleActive(int $id): TariffRate
    {
        $tariff = $this->findById($id);
        $tariff->update(['is_active' => !$tariff->is_active]);
        return $tariff->fresh();
    }

    public function allActive(): Collection
    {
        return TariffRate::where('is_active', true)->orderBy('name')->get();
    }
}
