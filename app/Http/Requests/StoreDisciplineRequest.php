<?php

namespace App\Http\Requests;

use App\Models\DisciplineIncident;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreDisciplineRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && $user->hasAnyRole([
            'super-admin', 'directeur', 'censeur', 'surveillant-general',
        ]);
    }

    public function rules(): array
    {
        return [
            'student_enrollment_id' => ['required', 'exists:student_enrollments,id'],
            'incident_date'         => ['required', 'date', 'before_or_equal:today'],
            'incident_time'         => ['nullable', 'date_format:H:i'],
            'location'              => ['nullable', Rule::in(array_keys(DisciplineIncident::LOCATIONS))],
            'incident_type'         => ['required', Rule::in(array_keys(DisciplineIncident::INCIDENT_TYPES))],
            'description'           => ['required', 'string', 'max:2000'],
            'sanction_type'         => ['nullable', Rule::in(array_keys(DisciplineIncident::SANCTIONS))],
            'sanction_duration_days'=> ['nullable', 'integer', 'min:1', 'max:30'],
            'parent_convoked'       => ['boolean'],
            'convocation_date'      => ['nullable', 'date'],
        ];
    }
}
