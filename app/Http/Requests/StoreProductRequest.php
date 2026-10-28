<?php

namespace App\Http\Requests;

use App\DTOs\ProductData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public const MAX_AMOUNT = '9999999999999.99';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $amount = ['bail', 'required', 'numeric', 'decimal:0,2', 'min:0', 'max:'.self::MAX_AMOUNT];

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'price' => $amount,
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'tax_cost' => $amount,
            'manufacturing_cost' => $amount,
        ];
    }

    public function toData(): ProductData
    {
        return ProductData::fromArray($this->validated());
    }
}
