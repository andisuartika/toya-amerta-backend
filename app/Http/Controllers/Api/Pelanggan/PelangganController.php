<?php

namespace App\Http\Controllers\Api\Pelanggan;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WaterReadingResource;
use App\Models\Customer;
use App\Models\WaterReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    private function customer(Request $request): Customer
    {
        return Customer::with(['zone', 'tariffRate'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

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
