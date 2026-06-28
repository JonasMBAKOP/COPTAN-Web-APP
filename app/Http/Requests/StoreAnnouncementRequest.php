<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && $user->hasAnyRole(['super-admin', 'directeur', 'censeur']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:191'],
            'content'      => ['required', 'string', 'max:5000'],
            'category'     => ['required', 'in:pedagogique,administratif,financier,evenement,general'],
            'target_roles' => ['nullable', 'array'],
            'is_pinned'    => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'   => 'Le titre est obligatoire.',
            'content.required' => 'Le contenu est obligatoire.',
        ];
    }
}
