<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('user');
        $isUpdate = (bool) $id;

        return [
            'name'      => ['required', 'string', 'max:150'],
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'phone'     => ['nullable', 'string', 'max:20'],
            'role'      => ['required', Rule::in(['admin', 'petugas', 'pelanggan'])],
            'is_active' => ['boolean'],
            'password'  => $isUpdate
                ? ['nullable', 'string', 'min:8', 'confirmed']
                : ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'Nama',
            'email'    => 'Email',
            'role'     => 'Role',
            'password' => 'Password',
        ];
    }
}
