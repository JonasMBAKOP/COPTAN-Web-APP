<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AssignSubjectsRequest extends FormRequest
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
            'subjects'                    => ['nullable', 'array'],
            'subjects.*.subject_id'       => ['required', 'exists:subjects,id'],
            'subjects.*.coefficient'      => ['required', 'integer',
                                              'min:1', 'max:9'],
            'subjects.*.hours_per_week'   => ['nullable', 'numeric',
                                              'min:0.5', 'max:30'],
            'subjects.*.is_active'        => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'subjects.*.coefficient.required' =>
                'Le coefficient est obligatoire.',
            'subjects.*.coefficient.min' =>
                'Le coefficient minimum est 1.',
            'subjects.*.coefficient.max' =>
                'Le coefficient maximum est 9.',
        ];
    }
}
