<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WhatsappTemplateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type'     => ['required', 'string', Rule::in(['tagihan', 'konfirmasi_bayar'])],
            'template' => ['required', 'string', 'max:3000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'template' => 'Template pesan',
        ];
    }
}
