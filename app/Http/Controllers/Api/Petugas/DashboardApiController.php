<?php

namespace App\Http\Controllers\Api\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PaymentRecord;
use App\Models\WaterReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DashboardApiController extends Controller
{
    #[OA\Get(
        path: '/petugas/dashboard',
        summary: 'Ringkasan dashboard petugas',
        description: 'Statistik pelanggan aktif, pencatatan meter, tagihan belum bayar, total kas terkumpul, ' .
            'serta daftar gabungan pelanggan sudah/belum dicatat untuk periode tertentu.',
        security: [['sanctum' => []]],
        tags: ['Petugas - Catat Meter'],
        parameters: [
            new OA\Parameter(name: 'year', description: 'Tahun periode (default: tahun sekarang)', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 2026)),
            new OA\Parameter(name: 'month', description: 'Bulan periode 1-12 (default: bulan sekarang)', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 6)),
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
                                new OA\Property(property: 'active_customers', type: 'integer', example: 45),
                                new OA\Property(property: 'recorded_count', type: 'integer', example: 30),
                                new OA\Property(property: 'not_recorded_count', type: 'integer', example: 15),
                                new OA\Property(property: 'unpaid_count', type: 'integer', example: 12),
                                new OA\Property(property: 'total_collected', type: 'number', format: 'float', example: 540000),
                                new OA\Property(
                                    property: 'history',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'water_reading_id', type: 'integer', nullable: true, example: 55),
                                            new OA\Property(property: 'customer_id', type: 'integer', example: 5),
                                            new OA\Property(property: 'customer_name', type: 'string', example: 'Andi Suartika'),
                                            new OA\Property(property: 'customer_number', type: 'string', example: 'PLG-2024-001'),
                                            new OA\Property(property: 'zone', type: 'string', example: 'Lingkungan Sangket'),
                                            new OA\Property(property: 'recorded', type: 'boolean', example: true),
                                            new OA\Property(property: 'reading_date', type: 'string', format: 'date', nullable: true, example: '2026-06-03'),
                                            new OA\Property(property: 'total_amount', type: 'number', format: 'float', nullable: true, example: 31000),
                                            new OA\Property(property: 'amount_paid', type: 'number', format: 'float', nullable: true, example: 0),
                                            new OA\Property(property: 'payment_status', type: 'string', enum: ['belum_bayar', 'sebagian', 'lunas', 'belum_dicatat'], example: 'belum_bayar'),
                                        ]
                                    )
                                ),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'year', type: 'integer', example: 2026),
                                new OA\Property(property: 'month', type: 'integer', example: 6),
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

        $activeCustomers = Customer::where('is_active', true)->count();

        $readings = WaterReading::with(['customer.zone', 'paymentRecords'])
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->get();

        $unpaidCount = $readings->whereIn('payment_status', ['belum_bayar', 'sebagian'])->count();

        $totalCollected = (float) PaymentRecord::whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->sum('amount_paid');

        $recordedCustomerIds = $readings->pluck('customer_id')->all();

        $notRecorded = Customer::with('zone')
            ->where('is_active', true)
            ->whereNotIn('id', $recordedCustomerIds)
            ->orderBy('name')
            ->get();

        $recordedHistory = $readings->map(function (WaterReading $r) {
            return [
                'water_reading_id' => $r->id,
                'customer_id'      => $r->customer_id,
                'customer_name'    => $r->customer?->name,
                'customer_number'  => $r->customer?->customer_number,
                'zone'             => $r->customer?->zone?->name,
                'recorded'         => true,
                'reading_date'     => $r->reading_date?->format('Y-m-d'),
                'total_amount'     => $r->total_amount,
                'amount_paid'      => $r->paymentRecords->sum('amount_paid'),
                'payment_status'   => $r->payment_status,
            ];
        });

        $notRecordedHistory = $notRecorded->map(fn (Customer $c) => [
            'water_reading_id' => null,
            'customer_id'      => $c->id,
            'customer_name'    => $c->name,
            'customer_number'  => $c->customer_number,
            'zone'             => $c->zone?->name,
            'recorded'         => false,
            'reading_date'     => null,
            'total_amount'     => null,
            'amount_paid'      => null,
            'payment_status'   => 'belum_dicatat',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => [
                'active_customers'   => $activeCustomers,
                'recorded_count'     => $readings->count(),
                'not_recorded_count' => $notRecorded->count(),
                'unpaid_count'       => $unpaidCount,
                'total_collected'    => $totalCollected,
                'history'            => $recordedHistory->merge($notRecordedHistory)->values(),
            ],
            'meta' => [
                'year'  => $year,
                'month' => $month,
            ],
        ]);
    }
}
