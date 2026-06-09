<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


class UpdateLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return Auth::check() && $user->hasPermissionTo('manage-levels');
    }

    public function rules(): array
    {
        return [
            'section_id' => ['required', 'exists:sections,id'],
            'name' => ['required', 'string', 'max:100'],
            'order_index' => ['required', 'integer', 'min:1', 'max:12'],
            'is_exam_class' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'La section est requise.',
            'section_id.exists' => 'La section sélectionnée n\'existe pas.',
            'name.required' => 'Le nom du niveau est requis.',
            'order_index.required' => 'L\'ordre d\'affichage est requis.',
            'is_exam_class.required' => 'Veuillez indiquer si c\'est une classe d\'examen.',
        ];
    }
}
