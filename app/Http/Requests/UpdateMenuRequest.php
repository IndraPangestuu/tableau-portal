<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'tableau_view_path' => 'nullable|string|max:500',
            'tableau_username' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable',
            'parent_id' => 'nullable|exists:menus,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama menu wajib diisi.',
            'icon.required' => 'Icon wajib diisi.',
            'parent_id.exists' => 'Parent menu tidak valid.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $menu = $this->route('menu');
            if ($this->parent_id == $menu->id) {
                $validator->errors()->add('parent_id', 'Menu tidak bisa menjadi parent dari dirinya sendiri.');
            }
        });
    }
}
