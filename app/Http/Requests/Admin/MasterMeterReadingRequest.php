<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MasterMeterReadingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'period_year'     => ['required', 'integer', 'min:2020', 'max:2099'],
            'period_month'    => ['required', 'integer', 'min:1', 'max:12'],
            'current_reading' => ['required', 'numeric', 'min:0'],
            'reading_date'    => ['required', 'date'],
            'notes'           => ['nullable', 'string', 'max:500'],
            'photo'           => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'period_year'     => 'Tahun periode',
            'period_month'    => 'Bulan periode',
            'current_reading' => 'Angka meter saat ini',
            'reading_date'    => 'Tanggal baca',
        ];
    }
}
