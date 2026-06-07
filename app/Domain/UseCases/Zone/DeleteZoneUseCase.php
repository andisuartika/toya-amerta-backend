<?php

namespace App\Domain\UseCases\Zone;

use App\Domain\Contracts\ZoneRepositoryInterface;
use Illuminate\Validation\ValidationException;

class DeleteZoneUseCase
{
    public function __construct(private ZoneRepositoryInterface $repo) {}

    public function execute(int $id): void
    {
        $zone = $this->repo->findById($id);

        if ($zone->customers()->count() > 0) {
            throw ValidationException::withMessages([
                'zone' => 'Zona tidak bisa dihapus karena masih memiliki pelanggan aktif.',
            ]);
        }

        $this->repo->delete($id);
    }
}
