<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSchoolSettingRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // La vérification des permissions se fera via le Middleware Cancancan (Policy)
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->can('manage-settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name'    => 'required|string|max:200',
            'short_name'   => 'required|string|max:50',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'address'      => 'nullable|string|max:500',
            'postal_box'   => 'nullable|string|max:100',
            'city'         => 'nullable|string|max:100',
            'region'       => 'nullable|string|max:100',
            'email'        => 'nullable|email|max:191',
            'website'      => 'nullable|url|max:200',
            'motto'        => 'nullable|string|max:255',
            'order_type'   => 'nullable|string|max:100',
            'ministry'     => 'nullable|string|max:200',
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array
    {
        return [
            'full_name.required'   => 'Le nom complet de l\'établissement est obligatoire.',
            'full_name.max'        => 'Le nom complet ne peut pas dépasser 200 caractères.',
            'short_name.required'  => 'L\'acronyme est obligatoire.',
            'short_name.max'       => 'L\'acronyme ne peut pas dépasser 50 caractères.',
            'logo.image'           => 'Le logo doit être une image.',
            'logo.mimes'           => 'Le logo doit être en format JPEG, PNG, JPG ou GIF.',
            'logo.max'             => 'Le logo ne peut pas dépasser 5 Mo.',
            'email.email'          => 'L\'adresse email doit être valide.',
            'website.url'          => 'Le site web doit être une URL valide.',
        ];
    }
}
