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

        $rules = [
            'customer_number'   => ['nullable', 'string', 'max:20', Rule::unique('customers', 'customer_number')->ignore($id)->whereNull('deleted_at')],
            'name'              => ['required', 'string', 'max:150'],
            'address'           => ['required', 'string'],
            'phone'             => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'zone_id'           => ['required', 'exists:zones,id'],
            'tariff_rate_id'    => ['required', 'exists:tariff_rates,id'],
            'installation_date' => ['nullable', 'date'],
            'initial_meter'     => ['nullable', 'numeric', 'min:0'],
            'is_active'         => ['boolean'],
            'notes'             => ['nullable', 'string'],
        ];

        // Field biaya pemasangan hanya saat store (POST)
        if ($this->isMethod('POST')) {
            $rules['fee_amount']         = ['nullable', 'numeric', 'min:0'];
            $rules['fee_type']           = ['nullable', 'in:biaya_pendaftaran,biaya_instalasi,biaya_meteran,uang_jaminan,lainnya'];
            $rules['fee_payment_date']   = ['nullable', 'date'];
            $rules['fee_payment_method'] = ['nullable', 'in:tunai,transfer,lainnya'];
            $rules['fee_description']    = ['nullable', 'string'];
        }

        return $rules;
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
            'phone'             => 'No. HP',
        ];
    }
}
