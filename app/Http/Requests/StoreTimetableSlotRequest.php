<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTimetableSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return Auth::check() && $user->can('manage-timetable');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'class_group_id' => ['required', 'exists:class_groups,id'],
            'class_subject_id' => ['required', 'exists:class_subjects,id'],
            'day_of_week' => ['required', 'integer', 'min:1', 'max:6'],
            'period_index' => ['required', 'integer', 'min:1', 'max:20'],
            'periods_count' => ['required', 'integer', 'min:1', 'max:14'],
            'room' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'class_subject_id.required' => 'Veuillez choisir une matiÃ¨re.',
            'day_of_week.required' => 'Veuillez choisir un jour.',
            'period_index.required' => 'Veuillez choisir la pÃ©riode de dÃ©but.',
            'periods_count.required' => 'Veuillez indiquer le nombre de pÃ©riodes.',
            'periods_count.min' => 'Le cours doit occuper au moins une pÃ©riode.',
        ];
    }
}
