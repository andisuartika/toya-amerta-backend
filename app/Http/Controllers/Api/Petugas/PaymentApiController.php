<?php

namespace App\Http\Controllers\Api\Petugas;

use App\Domain\Contracts\PaymentRepositoryInterface;
use App\Domain\DTOs\Payment\PaymentDTO;
use App\Domain\UseCases\Payment\ConfirmPaymentUseCase;
use App\Http\Controllers\Controller;
use App\Models\PaymentRecord;
use App\Models\WaterReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PaymentApiController extends Controller
{
    public function __construct(
        private ConfirmPaymentUseCase $confirmUseCase,
        private PaymentRepositoryInterface $repo,
    ) {}

    #[OA\Get(
        path: '/petugas/tagihan',
        summary: 'Daftar tagihan belum bayar',
        security: [['sanctum' => []]],
        tags: ['Petugas - Pembayaran'],
        parameters: [
            new OA\Parameter(name: 'zone_id', description: 'Filter zona', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'year', description: 'Filter tahun periode', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 2026)),
            new OA\Parameter(name: 'month', description: 'Filter bulan periode', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 6)),
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
                                    new OA\Property(property: 'id', type: 'integer', example: 55),
                                    new OA\Property(property: 'customer_id', type: 'integer', example: 5),
                                    new OA\Property(property: 'customer_name', type: 'string', example: 'Wayan Karya'),
                                    new OA\Property(property: 'customer_number', type: 'string', example: 'PLG-0005'),
                                    new OA\Property(property: 'zone', type: 'string', example: 'Zona A'),
                                    new OA\Property(property: 'period_year', type: 'integer', example: 2026),
                                    new OA\Property(property: 'period_month', type: 'integer', example: 6),
                                    new OA\Property(property: 'period_label', type: 'string', example: 'Juni 2026'),
                                    new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 31000),
                                    new OA\Property(property: 'remaining_amount', type: 'number', format: 'float', example: 31000),
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
            new OA\Response(response: 403, description: 'Role tidak diizinkan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
        ]
    )]
    public function unpaid(Request $request): JsonResponse
    {
        $filters = $request->only(['zone_id', 'month', 'year']);
        $list    = $this->repo->unpaid($filters);

        $data = $list->map(fn (WaterReading $r) => [
            'id'               => $r->id,
            'customer_id'      => $r->customer_id,
            'customer_name'    => $r->customer?->name,
            'customer_number'  => $r->customer?->customer_number,
            'zone'             => $r->customer?->zone?->name,
            'period_year'      => $r->period_year,
            'period_month'     => $r->period_month,
            'period_label'     => $r->period_label,
            'total_amount'     => $r->total_amount,
            'remaining_amount' => $r->remaining_amount,
            'payment_status'   => $r->payment_status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $data,
            'meta'    => ['total' => $data->count()],
        ]);
    }

    #[OA\Post(
        path: '/petugas/payments',
        summary: 'Konfirmasi pembayaran',
        description: 'Mendukung pembayaran sebagian (cicilan). amount_paid tidak boleh melebihi sisa tagihan. ' .
            'Setelah konfirmasi, transaksi kas masuk otomatis tercatat.',
        security: [['sanctum' => []]],
        tags: ['Petugas - Pembayaran'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['water_reading_id', 'amount_paid', 'payment_date', 'payment_method'],
                properties: [
                    new OA\Property(property: 'water_reading_id', type: 'integer', example: 55),
                    new OA\Property(property: 'amount_paid', type: 'number', format: 'float', example: 31000),
                    new OA\Property(property: 'payment_date', type: 'string', format: 'date', example: '2026-06-09'),
                    new OA\Property(property: 'payment_method', type: 'string', enum: ['tunai', 'transfer', 'qris'], example: 'tunai'),
                    new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Bayar lunas'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Pembayaran berhasil dikonfirmasi',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Pembayaran berhasil dikonfirmasi.'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/PaymentRecord', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 403, description: 'Role tidak diizinkan', content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')),
            new OA\Response(response: 422, description: 'Validasi gagal / sudah lunas / melebihi sisa tagihan', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'water_reading_id' => 'required|integer|exists:water_readings,id',
            'amount_paid'      => 'required|numeric|min:1',
            'payment_date'     => 'required|date',
            'payment_method'   => 'required|in:tunai,transfer,qris',
            'notes'            => 'nullable|string|max:500',
        ]);

        $dto     = PaymentDTO::fromArray($validated);
        $payment = $this->confirmUseCase->execute($dto, $request->user()->id);
        $payment->load(['waterReading', 'recordedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi.',
            'data'    => $this->formatPayment($payment),
            'meta'    => null,
        ], 201);
    }

    #[OA\Get(
        path: '/petugas/payments/{id}',
        summary: 'Detail pembayaran',
        security: [['sanctum' => []]],
        tags: ['Petugas - Pembayaran'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'ID payment record', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'OK'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/PaymentRecord', type: 'object'),
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
        $payment = $this->repo->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $this->formatPayment($payment),
            'meta'    => null,
        ]);
    }

    private function formatPayment(PaymentRecord $p): array
    {
        return [
            'id'               => $p->id,
            'receipt_number'   => $p->receipt_number,
            'water_reading_id' => $p->water_reading_id,
            'customer_id'      => $p->customer_id,
            'period_label'     => $p->waterReading?->period_label,
            'amount_paid'      => $p->amount_paid,
            'payment_date'     => $p->payment_date?->format('Y-m-d'),
            'payment_method'   => $p->payment_method,
            'status'           => $p->status,
            'recorded_by'      => $p->recordedBy?->name,
            'notes'            => $p->notes,
        ];
    }
}
