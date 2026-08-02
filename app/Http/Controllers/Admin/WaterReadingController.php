<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Contracts\WaterReadingRepositoryInterface;
use App\Domain\DTOs\WaterReading\WaterReadingDTO;
use App\Domain\UseCases\WaterReading\CreateWaterReadingUseCase;
use App\Domain\UseCases\WaterReading\DeleteWaterReadingUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WaterReadingRequest;
use App\Models\Customer;
use App\Models\WaterReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WaterReadingController extends Controller
{
    public function __construct(
        private CreateWaterReadingUseCase    $createUseCase,
        private DeleteWaterReadingUseCase    $deleteUseCase,
        private WaterReadingRepositoryInterface $repo,
    ) {}

    public function index(Request $request): View
    {
        $year  = (int) ($request->year  ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $readings  = $this->repo->forPeriod($year, $month);
        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        // Pelanggan yang BELUM dicatat pada periode ini
        $recordedIds   = $readings->pluck('customer_id')->toArray();
        $unrecorded    = $customers->whereNotIn('id', $recordedIds)->values();

        return view('admin.water-readings.index', compact(
            'readings', 'customers', 'unrecorded', 'year', 'month'
        ));
    }

    public function store(WaterReadingRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['officer_id'] = auth()->id();

            $this->createUseCase->execute(WaterReadingDTO::fromArray($data));
            return back()->with('success', 'Pencatatan meteran berhasil disimpan.');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }
    }

    public function destroy(int $waterReading): RedirectResponse
    {
        try {
            $this->deleteUseCase->execute($waterReading);
            return back()->with('success', 'Data pencatatan berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }
    }

    public function history(Request $request): View
    {
        $customerId = $request->customer_id;
        $customer   = $customerId ? Customer::findOrFail($customerId) : null;
        $readings   = $customer ? $this->repo->forCustomer($customerId, 24) : collect();
        $customers  = Customer::where('is_active', true)->orderBy('name')->get();

        return view('admin.water-readings.history', compact('readings', 'customers', 'customer'));
    }

    /** AJAX: daftar pelanggan yang belum dicatat pada periode tertentu */
    public function unrecordedCustomers(Request $request): JsonResponse
    {
        $year  = (int) ($request->year  ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $recordedIds = WaterReading::where('period_year', $year)
            ->where('period_month', $month)
            ->pluck('customer_id')
            ->toArray();

        $customers = Customer::where('is_active', true)
            ->whereNotIn('id', $recordedIds)
            ->orderBy('name')
            ->get(['id', 'customer_number', 'name']);

        return response()->json($customers);
    }

    /** AJAX: ambil previous_reading dan info pelanggan */
    public function customerInfo(int $customerId): JsonResponse
    {
        $customer = Customer::with('tariffRate')->findOrFail($customerId);
        $tariff   = $customer->tariffRate;

        return response()->json([
            'previous_reading' => $this->repo->baselineReading($customerId),
            'tariff_name'      => $tariff?->name,
            'zone_name'        => $customer->zone?->name ?? '—',
            'price_per_m3'     => $tariff?->price_per_m3 ?? 0,
            'minimum_usage'    => $tariff?->minimum_usage ?? 1,
            'minimum_charge'   => $tariff?->minimum_charge ?? 0,
        ]);
    }
}
