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

class PaymentApiController extends Controller
{
    public function __construct(
        private ConfirmPaymentUseCase $confirmUseCase,
        private PaymentRepositoryInterface $repo,
    ) {}

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
