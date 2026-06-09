<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return Auth::check() && $user->can('manage-subjects');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject_category_id' => ['required', 'exists:subject_categories,id'],
            'code'                => ['required', 'string', 'max:20',
                                      'unique:subjects,code'],
            'name_fr'             => ['required', 'string', 'max:100'],
            'name_en'             => ['nullable', 'string', 'max:100'],
            'type'                => ['required', 'in:general,technical,
                                       language,sport,other'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject_category_id.required' => 'La catégorie est obligatoire.',
            'code.required'                => 'Le code est obligatoire.',
            'code.unique'                  => 'Ce code existe déjà.',
            'name_fr.required'             => 'Le nom en français est obligatoire.',
            'type.required'                => 'Le type est obligatoire.',
        ];
    }
}
