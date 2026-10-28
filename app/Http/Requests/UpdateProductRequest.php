<?php

namespace App\Http\Requests;

use App\DTOs\ProductData;
use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateProductRequest extends StoreProductRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        if ($this->isMethod('PATCH')) {
            $rules = array_map(fn (array $fieldRules) => ['sometimes', ...$fieldRules], $rules);
        }

        return $rules;
    }

    public function toData(): ProductData
    {
        /** @var Product $product */
        $product = $this->route('product');

        return ProductData::fromArray([
            ...$product->only(ProductData::FIELDS),
            ...$this->validated(),
        ]);
    }
}
