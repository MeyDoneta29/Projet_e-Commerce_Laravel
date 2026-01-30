<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Géré par middleware auth
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Le produit est obligatoire',
            'product_id.exists' => 'Ce produit n\'existe pas',
            'quantity.required' => 'La quantité est obligatoire',
            'quantity.min' => 'La quantité doit être au moins 1',
            'quantity.max' => 'Vous ne pouvez pas commander plus de 100 unités',
        ];
    }
}