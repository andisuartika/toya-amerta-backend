<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'water_reading_id' => ['required', 'exists:water_readings,id'],
            'amount_paid'      => ['required', 'numeric', 'min:1'],
            'payment_date'     => ['required', 'date'],
            'payment_method'   => ['required', 'in:tunai,transfer,lainnya'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'water_reading_id' => 'Tagihan',
            'amount_paid'      => 'Jumlah bayar',
            'payment_date'     => 'Tanggal bayar',
            'payment_method'   => 'Metode bayar',
        ];
    }
}
