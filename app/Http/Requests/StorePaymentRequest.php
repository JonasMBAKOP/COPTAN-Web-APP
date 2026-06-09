<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user && $user->can('manage-finances');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fee_installment_id' => ['required', 'exists:fee_installments,id'],
            'amount_paid'        => ['required', 'numeric', 'min:1'],
            'payment_date'       => ['required', 'date'],
            'payment_method'     => ['required',
                'in:cash,orange_money,mtn_momo,bank_transfer,other'],
            'reference'          => ['nullable', 'string', 'max:100'],
            'notes'              => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'fee_installment_id.required' => 'La tranche est obligatoire.',
            'amount_paid.required'        => 'Le montant est obligatoire.',
            'amount_paid.min'             => 'Le montant doit être supérieur à 0.',
            'payment_date.required'       => 'La date de paiement est obligatoire.',
            'payment_method.required'     => 'Le mode de paiement est obligatoire.',
        ];
    }
}
