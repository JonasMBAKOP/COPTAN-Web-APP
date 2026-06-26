<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAbsenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole([
            'super-admin','directeur','censeur','enseignant','surveillant-general'
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
            'class_group_id'    => ['required', 'exists:class_groups,id'],
            'class_subject_id'  => ['nullable', 'exists:class_subjects,id'],
            'absence_date'      => ['required', 'date', 'before_or_equal:today'],
            'period'            => ['nullable', 'string', 'max:20'],
            'absences'          => ['required', 'array'],
            'absences.*.hours'  => ['nullable', 'numeric', 'min:0.5', 'max:8'],
        ];
    }
}
