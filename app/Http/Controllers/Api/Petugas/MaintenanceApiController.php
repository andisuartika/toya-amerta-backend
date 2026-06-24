<?php

namespace App\Http\Controllers\Api\Petugas;

use App\Domain\Contracts\MaintenanceRepositoryInterface;
use App\Domain\DTOs\Maintenance\MaintenanceDTO;
use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MaintenanceApiController extends Controller
{
    public function __construct(private MaintenanceRepositoryInterface $repo) {}

    #[OA\Get(
        path: '/petugas/maintenance',
        summary: 'Daftar laporan maintenance',
        security: [['sanctum' => []]],
        tags: ['Petugas - Maintenance'],
        parameters: [
            new OA\Parameter(name: 'status', description: 'Filter status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['dilaporkan', 'dalam_proses', 'selesai', 'ditunda'])),
            new OA\Parameter(name: 'priority', description: 'Filter prioritas', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['rendah', 'sedang', 'tinggi', 'darurat'])),
            new OA\Parameter(name: 'category', description: 'Filter kategori', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['pipa_bocor', 'meteran_rusak', 'pompa', 'reservoir', 'instalasi_baru', 'lainnya'])),
            new OA\Parameter(name: 'zone_id', description: 'Filter zona', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'OK'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Maintenance')),
                        new OA\Property(
                            property: 'meta',
                            properties: [new OA\Property(property: 'total', type: 'integer', example: 1)],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role tidak diizinkan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
        ]
    )]
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

    #[OA\Get(
        path: '/petugas/maintenance/{id}',
        summary: 'Detail laporan maintenance',
        security: [['sanctum' => []]],
        tags: ['Petugas - Maintenance'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'ID laporan maintenance', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'OK'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Maintenance', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role tidak diizinkan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
            new OA\Response(response: 404, description: 'Tidak ditemukan', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
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

    #[OA\Post(
        path: '/petugas/maintenance',
        summary: 'Buat laporan maintenance',
        security: [['sanctum' => []]],
        tags: ['Petugas - Maintenance'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'location', 'category', 'priority', 'reported_date'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Pipa bocor di Jl. Raya Desa'),
                    new OA\Property(property: 'location', type: 'string', example: 'Jl. Raya Desa No. 45'),
                    new OA\Property(property: 'category', type: 'string', enum: ['pipa_bocor', 'meteran_rusak', 'pompa', 'reservoir', 'instalasi_baru', 'lainnya'], example: 'pipa_bocor'),
                    new OA\Property(property: 'priority', type: 'string', enum: ['rendah', 'sedang', 'tinggi', 'darurat'], example: 'tinggi'),
                    new OA\Property(property: 'reported_date', type: 'string', format: 'date', example: '2026-06-08'),
                    new OA\Property(property: 'zone_id', type: 'integer', nullable: true, example: 2),
                    new OA\Property(property: 'customer_id', type: 'integer', nullable: true, example: null),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Air merembes dari sambungan pipa bawah tanah.'),
                    new OA\Property(property: 'material_cost', type: 'number', format: 'float', nullable: true, example: null),
                    new OA\Property(property: 'labor_cost', type: 'number', format: 'float', nullable: true, example: null),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Laporan maintenance berhasil disimpan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Laporan maintenance berhasil disimpan.'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Maintenance', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role tidak diizinkan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
            new OA\Response(response: 422, description: 'Validasi gagal', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
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

    #[OA\Patch(
        path: '/petugas/maintenance/{id}/status',
        summary: 'Update status maintenance',
        description: 'Saat status berubah ke dalam_proses, handled_date otomatis diisi. ' .
            'Saat selesai, completed_date diisi dan biaya otomatis tercatat ke kas keluar.',
        security: [['sanctum' => []]],
        tags: ['Petugas - Maintenance'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'ID laporan maintenance', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['dilaporkan', 'dalam_proses', 'selesai', 'ditunda'], example: 'selesai'),
                    new OA\Property(property: 'material_cost', type: 'number', format: 'float', nullable: true, example: 150000),
                    new OA\Property(property: 'labor_cost', type: 'number', format: 'float', nullable: true, example: 100000),
                    new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Pipa sudah diganti dan tersegel.'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Status maintenance berhasil diperbarui',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Status maintenance berhasil diperbarui.'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Maintenance', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role tidak diizinkan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
            new OA\Response(response: 404, description: 'Tidak ditemukan', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 422, description: 'Validasi gagal', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
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
