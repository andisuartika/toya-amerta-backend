<?php

namespace App\Domain\UseCases\WaterReading;

use App\Domain\Contracts\WaterReadingRepositoryInterface;
use Illuminate\Validation\ValidationException;

class DeleteWaterReadingUseCase
{
    public function __construct(private WaterReadingRepositoryInterface $repo) {}

    public function execute(int $id): void
    {
        $reading = $this->repo->findById($id);

        if ($reading->payment_status === 'lunas') {
            throw ValidationException::withMessages([
                'reading' => 'Pencatatan yang sudah lunas tidak dapat dihapus.',
            ]);
        }

        $this->repo->delete($id);
    }
}
