<?php

namespace App\Http\Controllers\Api\Pelanggan;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WaterReadingResource;
use App\Models\Customer;
use App\Models\WaterReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PelangganController extends Controller
{
    private function customer(Request $request): Customer
    {
        return Customer::with(['zone', 'tariffRate'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    #[OA\Get(
        path: '/pelanggan/profile',
        summary: 'Profil pelanggan',
        description: 'Mengembalikan data profil & informasi tarif pelanggan yang sedang login.',
        security: [['sanctum' => []]],
        tags: ['Pelanggan'],
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
                                new OA\Property(property: 'customer_number', type: 'string', example: 'PLG-0005'),
                                new OA\Property(property: 'name', type: 'string', example: 'Wayan Karya'),
                                new OA\Property(property: 'address', type: 'string', example: 'Banjar Kaja No. 12'),
                                new OA\Property(property: 'phone', type: 'string', example: '082233445566'),
                                new OA\Property(property: 'zone', type: 'string', example: 'Zona A'),
                                new OA\Property(property: 'tariff_name', type: 'string', example: 'Tarif Rumah Tangga'),
                                new OA\Property(property: 'price_per_m3', type: 'number', format: 'float', example: 2500),
                                new OA\Property(property: 'minimum_charge', type: 'number', format: 'float', example: 15000),
                                new OA\Property(property: 'minimum_usage', type: 'number', format: 'float', example: 5),
                                new OA\Property(property: 'installation_date', type: 'string', format: 'date', example: '2022-03-15'),
                                new OA\Property(property: 'is_active', type: 'boolean', example: true),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role bukan pelanggan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
        ]
    )]
    public function profile(Request $request): JsonResponse
    {
        $customer = $this->customer($request);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => [
                'id'                => $customer->id,
                'customer_number'   => $customer->customer_number,
                'name'              => $customer->name,
                'address'           => $customer->address,
                'phone'             => $customer->phone,
                'zone'              => $customer->zone?->name,
                'tariff_name'       => $customer->tariffRate?->name,
                'price_per_m3'      => $customer->tariffRate?->price_per_m3,
                'minimum_charge'    => $customer->tariffRate?->minimum_charge,
                'minimum_usage'     => $customer->tariffRate?->minimum_usage,
                'installation_date' => $customer->installation_date?->format('Y-m-d'),
                'is_active'         => $customer->is_active,
            ],
            'meta' => null,
        ]);
    }

    #[OA\Get(
        path: '/pelanggan/tagihan',
        summary: 'Tagihan belum lunas',
        description: 'Mengembalikan semua tagihan dengan status belum_bayar atau sebagian milik pelanggan yang sedang login.',
        security: [['sanctum' => []]],
        tags: ['Pelanggan'],
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
                                    new OA\Property(property: 'id', type: 'integer', example: 42),
                                    new OA\Property(property: 'period_year', type: 'integer', example: 2026),
                                    new OA\Property(property: 'period_month', type: 'integer', example: 5),
                                    new OA\Property(property: 'period_label', type: 'string', example: 'Mei 2026'),
                                    new OA\Property(property: 'reading_date', type: 'string', format: 'date', example: '2026-05-03'),
                                    new OA\Property(property: 'previous_reading', type: 'number', format: 'float', example: 120.50),
                                    new OA\Property(property: 'current_reading', type: 'number', format: 'float', example: 132.80),
                                    new OA\Property(property: 'usage_m3', type: 'number', format: 'float', example: 12.30),
                                    new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 30750),
                                    new OA\Property(property: 'amount_paid', type: 'number', format: 'float', example: 0),
                                    new OA\Property(property: 'remaining_amount', type: 'number', format: 'float', example: 30750),
                                    new OA\Property(property: 'payment_status', type: 'string', enum: ['belum_bayar', 'sebagian'], example: 'belum_bayar'),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'meta',
                            properties: [new OA\Property(property: 'total', type: 'integer', example: 1)],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role bukan pelanggan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
        ]
    )]
    public function tagihan(Request $request): JsonResponse
    {
        $customer = $this->customer($request);

        $readings = WaterReading::with(['paymentRecords'])
            ->where('customer_id', $customer->id)
            ->whereIn('payment_status', ['belum_bayar', 'sebagian'])
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->get();

        $data = $readings->map(function (WaterReading $r) {
            $paid      = $r->paymentRecords->sum('amount_paid');
            $remaining = max(0, $r->total_amount - $paid);

            return [
                'id'               => $r->id,
                'period_year'      => $r->period_year,
                'period_month'     => $r->period_month,
                'period_label'     => $r->period_label,
                'reading_date'     => $r->reading_date?->format('Y-m-d'),
                'previous_reading' => $r->previous_reading,
                'current_reading'  => $r->current_reading,
                'usage_m3'         => round($r->current_reading - $r->previous_reading, 2),
                'total_amount'     => $r->total_amount,
                'amount_paid'      => $paid,
                'remaining_amount' => $remaining,
                'payment_status'   => $r->payment_status,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $data,
            'meta'    => ['total' => $data->count()],
        ]);
    }

    #[OA\Get(
        path: '/pelanggan/riwayat',
        summary: 'Riwayat pemakaian air',
        description: 'Mengembalikan riwayat pembacaan meter pelanggan, terbaru lebih dulu.',
        security: [['sanctum' => []]],
        tags: ['Pelanggan'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                description: 'Jumlah data yang diambil, maksimal 24',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 12, maximum: 24)
            ),
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
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 42),
                                    new OA\Property(property: 'period_year', type: 'integer', example: 2026),
                                    new OA\Property(property: 'period_month', type: 'integer', example: 5),
                                    new OA\Property(property: 'period_label', type: 'string', example: 'Mei 2026'),
                                    new OA\Property(property: 'reading_date', type: 'string', format: 'date', example: '2026-05-03'),
                                    new OA\Property(property: 'previous_reading', type: 'number', format: 'float', example: 120.50),
                                    new OA\Property(property: 'current_reading', type: 'number', format: 'float', example: 132.80),
                                    new OA\Property(property: 'usage_m3', type: 'number', format: 'float', example: 12.30),
                                    new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 30750),
                                    new OA\Property(property: 'amount_paid', type: 'number', format: 'float', example: 30750),
                                    new OA\Property(property: 'payment_status', type: 'string', enum: ['belum_bayar', 'sebagian', 'lunas'], example: 'lunas'),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'total', type: 'integer', example: 2),
                                new OA\Property(property: 'limit', type: 'integer', example: 12),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role bukan pelanggan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
        ]
    )]
    public function riwayat(Request $request): JsonResponse
    {
        $customer = $this->customer($request);
        $limit    = min((int) ($request->query('limit', 12)), 24);

        $readings = WaterReading::with(['paymentRecords'])
            ->where('customer_id', $customer->id)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->limit($limit)
            ->get();

        $data = $readings->map(function (WaterReading $r) {
            $paid = $r->paymentRecords->sum('amount_paid');

            return [
                'id'               => $r->id,
                'period_year'      => $r->period_year,
                'period_month'     => $r->period_month,
                'period_label'     => $r->period_label,
                'reading_date'     => $r->reading_date?->format('Y-m-d'),
                'previous_reading' => $r->previous_reading,
                'current_reading'  => $r->current_reading,
                'usage_m3'         => round($r->current_reading - $r->previous_reading, 2),
                'total_amount'     => $r->total_amount,
                'amount_paid'      => $paid,
                'payment_status'   => $r->payment_status,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $data,
            'meta'    => ['total' => $data->count(), 'limit' => $limit],
        ]);
    }
}
