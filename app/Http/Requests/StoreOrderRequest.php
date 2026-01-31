<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|in:card,cash,mobile_money',
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.required' => 'L\'adresse de livraison est obligatoire',
            'shipping_address.max' => 'L\'adresse ne peut pas dépasser 500 caractères',
            'payment_method.required' => 'Le mode de paiement est obligatoire',
            'payment_method.in' => 'Mode de paiement invalide (card, cash, mobile_money)',
        ];
    }
}