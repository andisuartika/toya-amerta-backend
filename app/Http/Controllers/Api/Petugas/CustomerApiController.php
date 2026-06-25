<?php

namespace App\Http\Controllers\Api\Petugas;

use App\Domain\Contracts\CustomerRepositoryInterface;
use App\Domain\Contracts\TariffRateRepositoryInterface;
use App\Domain\Contracts\ZoneRepositoryInterface;
use App\Domain\DTOs\Customer\CustomerDTO;
use App\Domain\UseCases\Customer\CreateCustomerUseCase;
use App\Domain\UseCases\Customer\DeleteCustomerUseCase;
use App\Domain\UseCases\Customer\UpdateCustomerUseCase;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\TariffRate;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class CustomerApiController extends Controller
{
    public function __construct(
        private CreateCustomerUseCase $createUseCase,
        private UpdateCustomerUseCase $updateUseCase,
        private DeleteCustomerUseCase $deleteUseCase,
        private CustomerRepositoryInterface $repo,
        private ZoneRepositoryInterface $zoneRepo,
        private TariffRateRepositoryInterface $tariffRepo,
    ) {}

    #[OA\Get(
        path: '/petugas/customers/form-options',
        summary: 'Opsi zona & tarif untuk form tambah/edit pelanggan',
        description: 'Daftar zona aktif dan golongan tarif aktif, dipakai untuk mengisi dropdown saat tambah/edit pelanggan.',
        security: [['sanctum' => []]],
        tags: ['Petugas - Pelanggan'],
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
                                new OA\Property(
                                    property: 'zones',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'name', type: 'string', example: 'Zona A'),
                                            new OA\Property(property: 'code', type: 'string', nullable: true, example: 'ZN-A'),
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: 'tariffs',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'name', type: 'string', example: 'Tarif Rumah Tangga'),
                                            new OA\Property(property: 'price_per_m3', type: 'number', format: 'float', example: 2500),
                                            new OA\Property(property: 'minimum_charge', type: 'number', format: 'float', example: 15000),
                                            new OA\Property(property: 'minimum_usage', type: 'number', format: 'float', example: 5),
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
        ]
    )]
    public function formOptions(): JsonResponse
    {
        $zones   = $this->zoneRepo->allActive()->map(fn (Zone $z) => [
            'id'   => $z->id,
            'name' => $z->name,
            'code' => $z->code,
        ]);

        $tariffs = $this->tariffRepo->allActive()->map(fn (TariffRate $t) => [
            'id'             => $t->id,
            'name'           => $t->name,
            'price_per_m3'   => $t->price_per_m3,
            'minimum_charge' => $t->minimum_charge,
            'minimum_usage'  => $t->minimum_usage,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => [
                'zones'   => $zones,
                'tariffs' => $tariffs,
            ],
            'meta' => null,
        ]);
    }

    #[OA\Post(
        path: '/petugas/customers',
        summary: 'Tambah pelanggan baru',
        security: [['sanctum' => []]],
        tags: ['Petugas - Pelanggan'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'address', 'zone_id', 'tariff_rate_id'],
                properties: [
                    new OA\Property(property: 'customer_number', type: 'string', nullable: true, example: 'PDAM-2026-0001', description: 'Dikosongkan untuk generate otomatis'),
                    new OA\Property(property: 'name', type: 'string', example: 'Wayan Karya'),
                    new OA\Property(property: 'address', type: 'string', example: 'Banjar Kaja No. 12'),
                    new OA\Property(property: 'phone', type: 'string', nullable: true, example: '082233445566'),
                    new OA\Property(property: 'zone_id', type: 'integer', example: 1),
                    new OA\Property(property: 'tariff_rate_id', type: 'integer', example: 1),
                    new OA\Property(property: 'installation_date', type: 'string', format: 'date', nullable: true, example: '2026-06-24'),
                    new OA\Property(property: 'initial_meter', type: 'number', format: 'float', nullable: true, example: 0),
                    new OA\Property(property: 'is_active', type: 'boolean', nullable: true, example: true),
                    new OA\Property(property: 'notes', type: 'string', nullable: true, example: null),
                    new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: null, description: 'Hubungkan ke akun login pelanggan (opsional)'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Pelanggan berhasil ditambahkan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Pelanggan berhasil ditambahkan.'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CustomerDetail', type: 'object'),
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
        $validated = $request->validate($this->rules());

        $validated['customer_number'] = $validated['customer_number'] ?? $this->repo->generateCustomerNumber();

        $customer = $this->createUseCase->execute(CustomerDTO::fromArray($validated));
        $customer->load(['zone', 'tariffRate']);

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil ditambahkan.',
            'data'    => $this->formatCustomer($customer),
            'meta'    => null,
        ], 201);
    }

    #[OA\Put(
        path: '/petugas/customers/{id}',
        summary: 'Update data pelanggan',
        security: [['sanctum' => []]],
        tags: ['Petugas - Pelanggan'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'ID pelanggan', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'address', 'zone_id', 'tariff_rate_id'],
                properties: [
                    new OA\Property(property: 'customer_number', type: 'string', nullable: true, example: 'PDAM-2026-0001'),
                    new OA\Property(property: 'name', type: 'string', example: 'Wayan Karya'),
                    new OA\Property(property: 'address', type: 'string', example: 'Banjar Kaja No. 12'),
                    new OA\Property(property: 'phone', type: 'string', nullable: true, example: '082233445566'),
                    new OA\Property(property: 'zone_id', type: 'integer', example: 1),
                    new OA\Property(property: 'tariff_rate_id', type: 'integer', example: 1),
                    new OA\Property(property: 'installation_date', type: 'string', format: 'date', nullable: true, example: '2026-06-24'),
                    new OA\Property(property: 'initial_meter', type: 'number', format: 'float', nullable: true, example: 0),
                    new OA\Property(property: 'is_active', type: 'boolean', nullable: true, example: true),
                    new OA\Property(property: 'notes', type: 'string', nullable: true, example: null),
                    new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: null),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Pelanggan berhasil diperbarui',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Pelanggan berhasil diperbarui.'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CustomerDetail', type: 'object'),
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
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate($this->rules($id));

        $customer = $this->updateUseCase->execute($id, CustomerDTO::fromArray($validated));

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil diperbarui.',
            'data'    => $this->formatCustomer($customer),
            'meta'    => null,
        ]);
    }

    #[OA\Delete(
        path: '/petugas/customers/{id}',
        summary: 'Hapus pelanggan',
        description: 'Soft delete — data pelanggan tidak dihapus permanen dari database.',
        security: [['sanctum' => []]],
        tags: ['Petugas - Pelanggan'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'ID pelanggan', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Pelanggan berhasil dihapus',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Pelanggan berhasil dihapus.'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role tidak diizinkan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
            new OA\Response(response: 404, description: 'Tidak ditemukan', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->deleteUseCase->execute($id);

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil dihapus.',
            'data'    => null,
            'meta'    => null,
        ]);
    }

    private function rules(?int $id = null): array
    {
        return [
            'customer_number'   => ['nullable', 'string', 'max:20', Rule::unique('customers', 'customer_number')->ignore($id)->whereNull('deleted_at')],
            'name'              => ['required', 'string', 'max:150'],
            'address'           => ['required', 'string'],
            'phone'             => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'zone_id'           => ['required', 'integer', 'exists:zones,id'],
            'tariff_rate_id'    => ['required', 'integer', 'exists:tariff_rates,id'],
            'installation_date' => ['nullable', 'date'],
            'initial_meter'     => ['nullable', 'numeric', 'min:0'],
            'is_active'         => ['boolean'],
            'notes'             => ['nullable', 'string'],
            'user_id'           => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    private function formatCustomer(Customer $c): array
    {
        return [
            'id'                => $c->id,
            'user_id'           => $c->user_id,
            'customer_number'   => $c->customer_number,
            'name'              => $c->name,
            'address'           => $c->address,
            'phone'             => $c->phone,
            'zone_id'           => $c->zone_id,
            'zone'              => $c->zone?->name,
            'tariff_rate_id'    => $c->tariff_rate_id,
            'tariff_name'       => $c->tariffRate?->name,
            'installation_date' => $c->installation_date?->format('Y-m-d'),
            'initial_meter'     => $c->initial_meter,
            'is_active'         => $c->is_active,
            'notes'             => $c->notes,
        ];
    }
}
