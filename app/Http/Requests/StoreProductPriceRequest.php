<?php

namespace App\Http\Requests;

use App\DTOs\ProductPriceData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'price' => ['bail', 'nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:'.StoreProductRequest::MAX_AMOUNT],
        ];
    }

    public function toData(): ProductPriceData
    {
        return ProductPriceData::fromArray($this->validated());
    }
}
