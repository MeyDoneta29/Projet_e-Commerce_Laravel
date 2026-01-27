<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'La quantité est obligatoire',
            'quantity.min' => 'La quantité doit être au moins 1',
            'quantity.max' => 'Maximum 100 unités par article',
        ];
    }
}