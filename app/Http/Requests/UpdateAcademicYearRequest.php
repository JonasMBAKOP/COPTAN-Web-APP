<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAcademicYearRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->can('manage-academic-years');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('academicYear')->id;

        return [
            'label'      => ['required', 'string', 'max:50', "unique:academic_years,label,{$id}", 'regex:/^\d{4}-\d{4}$/'],
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'label.required' => 'Le libellé de l\'année scolaire est obligatoire.',
            'label.unique'   => 'Cette année scolaire existe déjà.',
            'label.regex'    => 'Format attendu : AAAA-AAAA (ex: 2024-2025).',
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.before'   => 'La date de début doit être avant la date de fin.',
            'end_date.required'   => 'La date de fin est obligatoire.',
            'end_date.after'      => 'La date de fin doit être après la date de début.',
        ];
    }
}
