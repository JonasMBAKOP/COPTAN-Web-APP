<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GenerateBulletinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole([
            'super-admin', 'directeur', 'censeur'
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'class_group_id' => ['required', 'exists:class_groups,id'],
            'type'           => ['required', 'in:sequentiel,trimestriel,annuel'],
            'sequence_id'    => ['required_if:type,sequentiel', 'nullable', 'exists:sequences,id'],
            'trimester_id'   => ['required_if:type,trimestriel', 'nullable', 'exists:trimesters,id'],
            'student_ids'    => ['nullable', 'array'],
            'student_ids.*'  => ['exists:student_enrollments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'class_group_id.required' => 'Veuillez sélectionner une classe.',
            'type.required'           => 'Veuillez choisir un type de bulletin.',
            'sequence_id.required_if' => 'Veuillez choisir une séquence.',
            'trimester_id.required_if'=> 'Veuillez choisir un trimestre.',
        ];
    }
}
