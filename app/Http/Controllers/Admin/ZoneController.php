<?php

namespace App\Http\Controllers\Admin;

use App\Domain\DTOs\Zone\ZoneDTO;
use App\Domain\UseCases\Zone\CreateZoneUseCase;
use App\Domain\UseCases\Zone\DeleteZoneUseCase;
use App\Domain\UseCases\Zone\UpdateZoneUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ZoneRequest;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ZoneController extends Controller
{
    public function __construct(
        private CreateZoneUseCase $createUseCase,
        private UpdateZoneUseCase $updateUseCase,
        private DeleteZoneUseCase $deleteUseCase,
    ) {}

    public function index(): View
    {
        $zones = Zone::orderBy('name')->get();
        return view('admin.zones.index', compact('zones'));
    }

    public function store(ZoneRequest $request): RedirectResponse
    {
        $this->createUseCase->execute(ZoneDTO::fromArray($request->validated()));
        return back()->with('success', 'Zona berhasil ditambahkan.');
    }

    public function update(ZoneRequest $request, int $zone): RedirectResponse
    {
        $this->updateUseCase->execute($zone, ZoneDTO::fromArray($request->validated()));
        return back()->with('success', 'Zona berhasil diperbarui.');
    }

    public function destroy(int $zone): RedirectResponse
    {
        try {
            $this->deleteUseCase->execute($zone);
            return back()->with('success', 'Zona berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['zone'][0]);
        }
    }
}
