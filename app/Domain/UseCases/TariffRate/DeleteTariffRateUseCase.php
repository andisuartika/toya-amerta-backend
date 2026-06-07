<?php

namespace App\Domain\UseCases\TariffRate;

use App\Domain\Contracts\TariffRateRepositoryInterface;
use Illuminate\Validation\ValidationException;

class DeleteTariffRateUseCase
{
    public function __construct(private TariffRateRepositoryInterface $repo) {}

    public function execute(int $id): void
    {
        $tariff = $this->repo->findById($id);

        if ($tariff->customers()->count() > 0) {
            throw ValidationException::withMessages([
                'tariff_rate' => 'Tarif tidak bisa dihapus karena masih digunakan oleh pelanggan.',
            ]);
        }

        $this->repo->delete($id);
    }
}
