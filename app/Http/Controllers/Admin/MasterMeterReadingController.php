<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Contracts\MasterMeterReadingRepositoryInterface;
use App\Domain\DTOs\MasterMeterReading\MasterMeterReadingDTO;
use App\Domain\UseCases\MasterMeterReading\CreateMasterMeterReadingUseCase;
use App\Domain\UseCases\MasterMeterReading\DeleteMasterMeterReadingUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MasterMeterReadingRequest;
use App\Models\WaterReading;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MasterMeterReadingController extends Controller
{
    public function __construct(
        private CreateMasterMeterReadingUseCase $createUseCase,
        private DeleteMasterMeterReadingUseCase $deleteUseCase,
        private MasterMeterReadingRepositoryInterface $repo,
    ) {}

    public function index(): View
    {
        $readings = $this->repo->all();

        // Total pemakaian pelanggan per periode yang sama, untuk dibandingkan dengan meteran induk
        // guna mendeteksi kebocoran/non-revenue water.
        $customerUsageByPeriod = WaterReading::selectRaw('period_year, period_month, SUM(current_reading - previous_reading) as total_usage')
            ->groupBy('period_year', 'period_month')
            ->get()
            ->keyBy(fn ($row) => $row->period_year . '-' . $row->period_month);

        $readings->each(function ($reading) use ($customerUsageByPeriod) {
            $key = $reading->period_year . '-' . $reading->period_month;
            $customerUsage = (float) ($customerUsageByPeriod[$key]->total_usage ?? 0);

            $reading->customer_usage = $customerUsage;
            $reading->loss_m3        = max(0, $reading->usage_m3 - $customerUsage);
            $reading->loss_percent   = $reading->usage_m3 > 0
                ? round(($reading->loss_m3 / $reading->usage_m3) * 100, 1)
                : 0;
        });

        return view('admin.master-meter-readings.index', compact('readings'));
    }

    public function store(MasterMeterReadingRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['recorded_by'] = auth()->id();

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('master-meter-readings', 'public');
                $data['photo_url'] = Storage::url($path);
            }

            $this->createUseCase->execute(MasterMeterReadingDTO::fromArray($data));

            return back()->with('success', 'Pencatatan meteran induk berhasil disimpan.');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }
    }

    public function destroy(int $masterMeterReading): RedirectResponse
    {
        $this->deleteUseCase->execute($masterMeterReading);

        return back()->with('success', 'Data pencatatan meteran induk berhasil dihapus.');
    }
}
