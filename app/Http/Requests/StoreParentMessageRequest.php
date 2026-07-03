<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreParentMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && $user->can('manage-parent-communication');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject'        => ['nullable', 'string', 'max:191'],
            'body'           => ['required', 'string', 'max:1000'],
            'channel'        => ['required', 'in:sms,whatsapp,both'],
            'target_type'    => ['required', 'in:all,selected,class'],
            'class_group_id' => ['required_if:target_type,class', 'nullable', 'exists:class_groups,id'],
            'student_ids'    => ['required_if:target_type,selected', 'nullable', 'array'],
            'student_ids.*'  => ['exists:students,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required'                => 'Le message est obligatoire.',
            'body.max'                     => 'Le message ne doit pas dépasser 1000 caractères (limite SMS).',
            'class_group_id.required_if'   => 'Veuillez choisir une classe.',
            'student_ids.required_if'      => 'Veuillez sélectionner au moins un élève.',
        ];
    }
}
