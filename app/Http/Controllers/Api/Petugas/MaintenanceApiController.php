<?php

namespace App\Http\Controllers\Api\Petugas;

use App\Domain\Contracts\MaintenanceRepositoryInterface;
use App\Domain\DTOs\Maintenance\MaintenanceDTO;
use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceApiController extends Controller
{
    public function __construct(private MaintenanceRepositoryInterface $repo) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'priority', 'category', 'zone_id']);
        $list    = $this->repo->all($filters);

        $data = $list->map(fn (Maintenance $m) => $this->formatMaintenance($m));

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $data,
            'meta'    => ['total' => $data->count()],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $maintenance = $this->repo->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $this->formatMaintenance($maintenance),
            'meta'    => null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'category'      => 'required|in:pipa_bocor,meteran_rusak,pompa,reservoir,instalasi_baru,lainnya',
            'priority'      => 'required|in:rendah,sedang,tinggi,darurat',
            'reported_date' => 'required|date',
            'zone_id'       => 'nullable|integer|exists:zones,id',
            'customer_id'   => 'nullable|integer|exists:customers,id',
            'description'   => 'nullable|string|max:1000',
            'material_cost' => 'nullable|numeric|min:0',
            'labor_cost'    => 'nullable|numeric|min:0',
        ]);

        $dto         = MaintenanceDTO::fromArray($validated);
        $maintenance = $this->repo->create($dto, $request->user()->id);
        $maintenance->load(['zone', 'customer', 'reportedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Laporan maintenance berhasil disimpan.',
            'data'    => $this->formatMaintenance($maintenance),
            'meta'    => null,
        ], 201);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status'        => 'required|in:dilaporkan,dalam_proses,selesai,ditunda',
            'material_cost' => 'nullable|numeric|min:0',
            'labor_cost'    => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string|max:500',
        ]);

        $extra = array_filter([
            'material_cost' => $validated['material_cost'] ?? null,
            'labor_cost'    => $validated['labor_cost'] ?? null,
            'description'   => $validated['notes'] ?? null,
        ], fn ($v) => $v !== null);

        $maintenance = $this->repo->updateStatus($id, $validated['status'], $extra);

        return response()->json([
            'success' => true,
            'message' => 'Status maintenance berhasil diperbarui.',
            'data'    => $this->formatMaintenance($maintenance),
            'meta'    => null,
        ]);
    }

    private function formatMaintenance(Maintenance $m): array
    {
        return [
            'id'              => $m->id,
            'title'           => $m->title,
            'location'        => $m->location,
            'category'        => $m->category,
            'category_label'  => Maintenance::categoryLabel($m->category),
            'priority'        => $m->priority,
            'priority_label'  => Maintenance::priorityLabel($m->priority),
            'status'          => $m->status,
            'status_label'    => Maintenance::statusLabel($m->status),
            'zone'            => $m->zone?->name,
            'customer_name'   => $m->customer?->name,
            'reported_by'     => $m->reportedBy?->name,
            'reported_date'   => $m->reported_date?->format('Y-m-d'),
            'handled_date'    => $m->handled_date?->format('Y-m-d'),
            'completed_date'  => $m->completed_date?->format('Y-m-d'),
            'description'     => $m->description,
            'material_cost'   => $m->material_cost,
            'labor_cost'      => $m->labor_cost,
            'total_cost'      => ($m->material_cost ?? 0) + ($m->labor_cost ?? 0),
        ];
    }
}
