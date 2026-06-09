<?php

namespace App\Http\Controllers\Api\Petugas;

use App\Domain\Contracts\WaterReadingRepositoryInterface;
use App\Domain\DTOs\WaterReading\WaterReadingDTO;
use App\Domain\UseCases\WaterReading\CreateWaterReadingUseCase;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\WaterReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WaterReadingApiController extends Controller
{
    public function __construct(
        private CreateWaterReadingUseCase $createUseCase,
        private WaterReadingRepositoryInterface $repo,
    ) {}

    public function customers(): JsonResponse
    {
        $customers = Customer::with(['zone', 'tariffRate'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Customer $c) => [
                'id'              => $c->id,
                'customer_number' => $c->customer_number,
                'name'            => $c->name,
                'address'         => $c->address,
                'zone'            => $c->zone?->name,
                'tariff'          => $c->tariffRate?->name,
                'initial_meter'   => $c->initial_meter,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $customers,
            'meta'    => ['total' => $customers->count()],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $year  = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);

        $readings = $this->repo->forPeriod($year, $month)
            ->map(fn (WaterReading $r) => $this->formatReading($r));

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $readings,
            'meta'    => [
                'year'  => $year,
                'month' => $month,
                'total' => $readings->count(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id'     => 'required|integer|exists:customers,id',
            'period_year'     => 'required|integer|min:2000|max:2100',
            'period_month'    => 'required|integer|min:1|max:12',
            'current_reading' => 'required|numeric|min:0',
            'reading_date'    => 'required|date',
            'notes'           => 'nullable|string|max:500',
        ]);

        $dto = WaterReadingDTO::fromArray(array_merge($validated, [
            'officer_id' => $request->user()->id,
        ]));

        $reading = $this->createUseCase->execute($dto);
        $reading->load(['customer.zone', 'officer']);

        return response()->json([
            'success' => true,
            'message' => 'Pembacaan meter berhasil dicatat.',
            'data'    => $this->formatReading($reading),
            'meta'    => null,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $reading = $this->repo->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $this->formatReading($reading),
            'meta'    => null,
        ]);
    }

    private function formatReading(WaterReading $r): array
    {
        return [
            'id'               => $r->id,
            'customer_id'      => $r->customer_id,
            'customer_name'    => $r->customer?->name,
            'customer_number'  => $r->customer?->customer_number,
            'zone'             => $r->customer?->zone?->name,
            'officer_name'     => $r->officer?->name,
            'period_year'      => $r->period_year,
            'period_month'     => $r->period_month,
            'period_label'     => $r->period_label,
            'reading_date'     => $r->reading_date?->format('Y-m-d'),
            'previous_reading' => $r->previous_reading,
            'current_reading'  => $r->current_reading,
            'usage_m3'         => round($r->current_reading - $r->previous_reading, 2),
            'price_per_m3'     => $r->price_per_m3,
            'minimum_charge'   => $r->minimum_charge,
            'total_amount'     => $r->total_amount,
            'payment_status'   => $r->payment_status,
            'notes'            => $r->notes,
        ];
    }
}
