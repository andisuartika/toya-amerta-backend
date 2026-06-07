<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TariffRateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100'],
            'price_per_m3'   => ['required', 'numeric', 'min:0'],
            'minimum_charge' => ['required', 'numeric', 'min:0'],
            'minimum_usage'  => ['required', 'numeric', 'min:0'],
            'is_active'      => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'           => 'Nama tarif',
            'price_per_m3'   => 'Harga per m³',
            'minimum_charge' => 'Minimum tagihan',
            'minimum_usage'  => 'Minimum pemakaian',
        ];
    }
}
