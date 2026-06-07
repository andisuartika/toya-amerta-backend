<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ZoneRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('zone');

        return [
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['required', 'string', 'max:20', Rule::unique('zones', 'code')->ignore($id)],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama zona',
            'code' => 'Kode zona',
        ];
    }
}
