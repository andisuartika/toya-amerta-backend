<?php

namespace App\Http\Controllers\Admin;

use App\Domain\DTOs\TariffRate\TariffRateDTO;
use App\Domain\UseCases\TariffRate\CreateTariffRateUseCase;
use App\Domain\UseCases\TariffRate\DeleteTariffRateUseCase;
use App\Domain\UseCases\TariffRate\UpdateTariffRateUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TariffRateRequest;
use App\Models\TariffRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TariffRateController extends Controller
{
    public function __construct(
        private CreateTariffRateUseCase $createUseCase,
        private UpdateTariffRateUseCase $updateUseCase,
        private DeleteTariffRateUseCase $deleteUseCase,
    ) {}

    public function index(): View
    {
        $tariffs = TariffRate::orderBy('name')->get();
        return view('admin.tariff-rates.index', compact('tariffs'));
    }

    public function store(TariffRateRequest $request): RedirectResponse
    {
        $this->createUseCase->execute(TariffRateDTO::fromArray($request->validated()));
        return back()->with('success', 'Tarif berhasil ditambahkan.');
    }

    public function update(TariffRateRequest $request, int $tariffRate): RedirectResponse
    {
        $this->updateUseCase->execute($tariffRate, TariffRateDTO::fromArray($request->validated()));
        return back()->with('success', 'Tarif berhasil diperbarui.');
    }

    public function destroy(int $tariffRate): RedirectResponse
    {
        try {
            $this->deleteUseCase->execute($tariffRate);
            return back()->with('success', 'Tarif berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['tariff_rate'][0]);
        }
    }
}
