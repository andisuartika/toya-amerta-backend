<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MeterReplacementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'new_reading_start' => ['required', 'numeric', 'min:0'],
            'replaced_at'        => ['required', 'date'],
            'reason'             => ['nullable', 'string', 'max:150'],
            'notes'              => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'new_reading_start' => 'Angka awal meteran baru',
            'replaced_at'        => 'Tanggal penggantian',
        ];
    }
}
