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
use OpenApi\Attributes as OA;

class WaterReadingApiController extends Controller
{
    public function __construct(
        private CreateWaterReadingUseCase $createUseCase,
        private WaterReadingRepositoryInterface $repo,
    ) {}

    #[OA\Get(
        path: '/petugas/customers',
        summary: 'Daftar pelanggan aktif',
        description: 'Digunakan untuk mengisi dropdown pelanggan saat input meter.',
        security: [['sanctum' => []]],
        tags: ['Petugas - Catat Meter'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'OK'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 5),
                                    new OA\Property(property: 'customer_number', type: 'string', example: 'PLG-0005'),
                                    new OA\Property(property: 'name', type: 'string', example: 'Wayan Karya'),
                                    new OA\Property(property: 'address', type: 'string', example: 'Banjar Kaja No. 12'),
                                    new OA\Property(property: 'zone', type: 'string', example: 'Zona A'),
                                    new OA\Property(property: 'tariff', type: 'string', example: 'Tarif Rumah Tangga'),
                                    new OA\Property(property: 'initial_meter', type: 'number', format: 'float', example: 100),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'meta',
                            properties: [new OA\Property(property: 'total', type: 'integer', example: 45)],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role tidak diizinkan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
        ]
    )]
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

    #[OA\Get(
        path: '/petugas/customers/{id}',
        summary: 'Detail pelanggan',
        description: 'Profil pelanggan, pembacaan meter terakhir, dan riwayat tagihan (5 periode terakhir sebelum pembacaan terakhir).',
        security: [['sanctum' => []]],
        tags: ['Petugas - Catat Meter'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'ID pelanggan', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'OK'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 5),
                                new OA\Property(property: 'name', type: 'string', example: 'Andi Suartika'),
                                new OA\Property(property: 'customer_number', type: 'string', example: 'PLG-2024-001'),
                                new OA\Property(property: 'zone', type: 'string', example: 'Lingkungan Sangket'),
                                new OA\Property(property: 'is_active', type: 'boolean', example: true),
                                new OA\Property(property: 'registered_date', type: 'string', format: 'date', example: '2024-01-10'),
                                new OA\Property(property: 'tariff_group', type: 'string', example: 'Rumah Tangga'),
                                new OA\Property(property: 'price_per_m3', type: 'number', format: 'float', example: 8000),
                                new OA\Property(property: 'minimum_usage', type: 'number', format: 'float', example: 5, description: 'Batas minimal pemakaian (m3) yang tetap dikenakan tarif penuh'),
                                new OA\Property(property: 'minimum_charge', type: 'number', format: 'float', example: 15000, description: 'Tagihan minimum walaupun pemakaian di bawah batas minimum'),
                                new OA\Property(
                                    property: 'last_reading',
                                    nullable: true,
                                    properties: [
                                        new OA\Property(property: 'current_reading', type: 'number', format: 'float', example: 145.5),
                                        new OA\Property(property: 'usage_m3', type: 'number', format: 'float', example: 12.4),
                                        new OA\Property(property: 'period_label', type: 'string', example: 'Juni 2026'),
                                        new OA\Property(property: 'reading_date', type: 'string', format: 'date', example: '2025-06-14'),
                                        new OA\Property(property: 'payment_status', type: 'string', enum: ['belum_bayar', 'sebagian', 'lunas'], example: 'lunas'),
                                    ],
                                    type: 'object'
                                ),
                                new OA\Property(
                                    property: 'billing_history',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'period_label', type: 'string', example: 'Mei 2026'),
                                            new OA\Property(property: 'usage_m3', type: 'number', format: 'float', example: 27.4),
                                            new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 68500),
                                            new OA\Property(property: 'payment_status', type: 'string', enum: ['belum_bayar', 'sebagian', 'lunas'], example: 'belum_bayar'),
                                        ]
                                    )
                                ),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role tidak diizinkan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
            new OA\Response(response: 404, description: 'Tidak ditemukan', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function customerDetail(int $id): JsonResponse
    {
        $customer = Customer::with(['zone', 'tariffRate'])->findOrFail($id);

        $readings = WaterReading::where('customer_id', $customer->id)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->limit(6)
            ->get();

        $last    = $readings->first();
        $history = $readings->slice(1, 5);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => [
                'id'               => $customer->id,
                'name'             => $customer->name,
                'customer_number'  => $customer->customer_number,
                'zone'             => $customer->zone?->name,
                'is_active'        => $customer->is_active,
                'registered_date'  => $customer->installation_date?->format('Y-m-d'),
                'tariff_group'     => $customer->tariffRate?->name,
                'price_per_m3'     => $customer->tariffRate?->price_per_m3,
                'minimum_usage'    => $customer->tariffRate?->minimum_usage,
                'minimum_charge'   => $customer->tariffRate?->minimum_charge,
                'last_reading'     => $last ? [
                    'current_reading' => $last->current_reading,
                    'usage_m3'        => round($last->current_reading - $last->previous_reading, 2),
                    'period_label'    => $last->period_label,
                    'reading_date'    => $last->reading_date?->format('Y-m-d'),
                    'payment_status'  => $last->payment_status,
                ] : null,
                'billing_history'  => $history->map(fn (WaterReading $r) => [
                    'period_label'   => $r->period_label,
                    'usage_m3'       => round($r->current_reading - $r->previous_reading, 2),
                    'total_amount'   => $r->total_amount,
                    'payment_status' => $r->payment_status,
                ])->values(),
            ],
            'meta' => null,
        ]);
    }

    #[OA\Get(
        path: '/petugas/water-readings',
        summary: 'Daftar pembacaan meter per periode',
        security: [['sanctum' => []]],
        tags: ['Petugas - Catat Meter'],
        parameters: [
            new OA\Parameter(name: 'year', description: 'Filter tahun periode (default: tahun sekarang)', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 2026)),
            new OA\Parameter(name: 'month', description: 'Filter bulan periode 1-12 (default: bulan sekarang)', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 6)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'OK'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/WaterReading')),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'year', type: 'integer', example: 2026),
                                new OA\Property(property: 'month', type: 'integer', example: 6),
                                new OA\Property(property: 'total', type: 'integer', example: 1),
                            ],
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

    #[OA\Post(
        path: '/petugas/water-readings',
        summary: 'Catat meter baru',
        description: 'Sistem otomatis menghitung previous_reading, usage_m3, dan total_amount berdasarkan tarif pelanggan. ' .
            'Jika pelanggan sudah dicatat pada periode yang sama, request akan ditolak (422). ' .
            'Gunakan `multipart/form-data` jika menyertakan foto meter.',
        security: [['sanctum' => []]],
        tags: ['Petugas - Catat Meter'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['customer_id', 'period_year', 'period_month', 'current_reading', 'reading_date'],
                    properties: [
                        new OA\Property(property: 'customer_id', type: 'integer', example: 5),
                        new OA\Property(property: 'period_year', type: 'integer', example: 2026),
                        new OA\Property(property: 'period_month', type: 'integer', example: 6),
                        new OA\Property(property: 'current_reading', type: 'number', format: 'float', example: 145.20),
                        new OA\Property(property: 'reading_date', type: 'string', format: 'date', example: '2026-06-03'),
                        new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Meteran normal'),
                        new OA\Property(property: 'photo', description: 'Foto meter (jpg/png/webp, maks 5MB)', type: 'string', format: 'binary', nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Pembacaan meter berhasil dicatat',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Pembacaan meter berhasil dicatat.'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/WaterReading', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role tidak diizinkan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
            new OA\Response(response: 422, description: 'Validasi gagal / pelanggan sudah dicatat di periode ini', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id'     => 'required|integer|exists:customers,id',
            'period_year'     => 'required|integer|min:2000|max:2100',
            'period_month'    => 'required|integer|min:1|max:12',
            'current_reading' => 'required|numeric|min:0',
            'reading_date'    => 'required|date',
            'notes'           => 'nullable|string|max:500',
            'photo'           => 'nullable|image|max:5120',
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $path     = $request->file('photo')->store('water-readings', 'public');
            $photoUrl = \Illuminate\Support\Facades\Storage::url($path);
        }

        $dto = WaterReadingDTO::fromArray(array_merge($validated, [
            'officer_id' => $request->user()->id,
            'photo_url'  => $photoUrl,
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

    #[OA\Get(
        path: '/petugas/water-readings/{id}',
        summary: 'Detail pembacaan meter',
        security: [['sanctum' => []]],
        tags: ['Petugas - Catat Meter'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'ID pembacaan meter', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'OK'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/WaterReading', type: 'object'),
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
            'photo_url'        => $r->photo_url ? url($r->photo_url) : null,
        ];
    }
}
