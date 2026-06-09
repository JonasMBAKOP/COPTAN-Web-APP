<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && $user->can('manage-students');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'academic_year_id'       => ['required', 'exists:academic_years,id'],
            'class_group_id'         => ['required', 'exists:class_groups,id'],
            'enrollment_date'        => ['required', 'date'],
            'is_repeating'           => ['boolean'],
            'previous_class_group_id'=> ['nullable', 'exists:class_groups,id'],
            'origin_school'          => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'academic_year_id.required' => 'L\'année scolaire est obligatoire.',
            'class_group_id.required'   => 'La classe est obligatoire.',
            'enrollment_date.required'  => 'La date d\'inscription est obligatoire.',
        ];
    }
}
