<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\CustomerRepositoryInterface;
use App\Domain\DTOs\Customer\CustomerDTO;
use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null, ?int $zoneId = null): LengthAwarePaginator
    {
        return Customer::with(['zone', 'tariffRate'])
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                                         ->orWhere('customer_number', 'like', "%{$search}%")
                                         ->orWhere('phone', 'like', "%{$search}%"))
            ->when($zoneId, fn ($q) => $q->where('zone_id', $zoneId))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): Customer
    {
        return Customer::with(['zone', 'tariffRate'])->findOrFail($id);
    }

    public function create(CustomerDTO $dto): Customer
    {
        return Customer::create([
            'user_id'           => $dto->user_id,
            'customer_number'   => $dto->customer_number,
            'name'              => $dto->name,
            'address'           => $dto->address,
            'phone'             => $dto->phone,
            'zone_id'           => $dto->zone_id,
            'tariff_rate_id'    => $dto->tariff_rate_id,
            'installation_date' => $dto->installation_date,
            'initial_meter'     => $dto->initial_meter,
            'is_active'         => $dto->is_active,
            'notes'             => $dto->notes,
        ]);
    }

    public function update(int $id, CustomerDTO $dto): Customer
    {
        $customer = $this->findById($id);
        $customer->update([
            'user_id'           => $dto->user_id,
            'customer_number'   => $dto->customer_number,
            'name'              => $dto->name,
            'address'           => $dto->address,
            'phone'             => $dto->phone,
            'zone_id'           => $dto->zone_id,
            'tariff_rate_id'    => $dto->tariff_rate_id,
            'installation_date' => $dto->installation_date ?? optional($customer->installation_date)->format('Y-m-d'),
            'initial_meter'     => $dto->initial_meter ?? $customer->initial_meter,
            'is_active'         => $dto->is_active,
            'notes'             => $dto->notes,
        ]);
        return $customer->fresh(['zone', 'tariffRate']);
    }

    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }

    public function toggleActive(int $id): Customer
    {
        $customer = $this->findById($id);
        $customer->update(['is_active' => !$customer->is_active]);
        return $customer->fresh();
    }

    public function generateCustomerNumber(): string
    {
        $year  = date('Y');
        $last  = Customer::withTrashed()
                    ->where('customer_number', 'like', "PDAM-{$year}-%")
                    ->latest('id')
                    ->value('customer_number');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return sprintf('PDAM-%s-%04d', $year, $seq);
    }
}
