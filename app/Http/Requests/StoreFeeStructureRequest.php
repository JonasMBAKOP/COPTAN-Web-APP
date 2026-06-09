<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFeeStructureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->can('configure-fees');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'installments'                 => ['required', 'array', 'min:1'],
            'installments.*.label'         => ['required', 'string', 'max:100'],
            'installments.*.amount'        => ['required', 'numeric', 'min:0'],
            'installments.*.due_date_start'=> ['nullable', 'date'],
            'installments.*.due_date_end'  => ['nullable', 'date',
                'after_or_equal:installments.*.due_date_start'],
        ];
    }

    public function messages(): array
    {
        return [
            'installments.*.label.required'  => 'Le libellé est obligatoire.',
            'installments.*.amount.required' => 'Le montant est obligatoire.',
            'installments.*.amount.min'      => 'Le montant ne peut pas être négatif.',
        ];
    }
}
