<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('customer');

        return [
            'customer_number'   => ['required', 'string', 'max:20', Rule::unique('customers', 'customer_number')->ignore($id)->whereNull('deleted_at')],
            'name'              => ['required', 'string', 'max:150'],
            'address'           => ['required', 'string'],
            'phone'             => ['nullable', 'string', 'max:20'],
            'zone_id'           => ['required', 'exists:zones,id'],
            'tariff_rate_id'    => ['required', 'exists:tariff_rates,id'],
            'installation_date' => ['nullable', 'date'],
            'initial_meter'     => ['nullable', 'numeric', 'min:0'],
            'is_active'         => ['boolean'],
            'notes'             => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_number'   => 'Nomor pelanggan',
            'name'              => 'Nama pelanggan',
            'address'           => 'Alamat',
            'zone_id'           => 'Zona',
            'tariff_rate_id'    => 'Tarif',
            'installation_date' => 'Tanggal pasang',
            'initial_meter'     => 'Meteran awal',
        ];
    }
}
