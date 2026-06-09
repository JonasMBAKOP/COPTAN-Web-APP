<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->can('manage-users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'      => ['required', 'string', 'max:191'],
            'email'     => ['required', 'email', 'max:191',
                            "unique:users,email,{$userId}"],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles'     => ['required', 'array', 'min:1'],
            'roles.*'   => ['string', 'exists:roles,name'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom est obligatoire.',
            'email.required'     => 'L\'adresse e-mail est obligatoire.',
            'email.unique'       => 'Cette adresse e-mail est déjà utilisée.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'roles.required'     => 'Veuillez assigner au moins un rôle.',
        ];
    }
}
