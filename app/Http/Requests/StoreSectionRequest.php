<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return Auth::check() && $user->hasPermissionTo('manage-sections');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:sections,name'],
            'code' => ['required', 'string', 'max:20', 'unique:sections,code'],
            'language' => ['required', 'in:fr,en'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la section est requis.',
            'name.unique' => 'Une section avec ce nom existe déjà.',
            'code.required' => 'Le code de la section est requis.',
            'code.unique' => 'Une section avec ce code existe déjà.',
            'language.required' => 'La langue est requise.',
            'language.in' => 'La langue doit être FR ou EN.',
        ];
    }
}
