<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id_user;

        return [
            'nama' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('user')->ignore($userId, 'id_user')],
            'telp' => 'nullable|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('user')->ignore($userId, 'id_user')],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}
