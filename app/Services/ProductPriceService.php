<?php

namespace App\Services;

use App\DTOs\ProductPriceData;
use App\Exceptions\CurrencyAlreadyRegisteredException;
use App\Exceptions\PriceInBaseCurrencyException;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\UniqueConstraintViolationException;

class ProductPriceService
{
    public function __construct(private readonly CurrencyConverter $converter) {}

    /**
     * @return Collection<int, ProductPrice>
     */
    public function listFor(Product $product): Collection
    {
        return $product->prices()->with('currency')->orderBy('id')->get();
    }

    /**
     * @throws PriceInBaseCurrencyException
     * @throws CurrencyAlreadyRegisteredException
     */
    public function register(Product $product, ProductPriceData $data): ProductPrice
    {
        if ($data->currencyId === $product->currency_id) {
            throw new PriceInBaseCurrencyException;
        }

        if ($product->prices()->where('currency_id', $data->currencyId)->exists()) {
            throw new CurrencyAlreadyRegisteredException;
        }

        $currency = Currency::findOrFail($data->currencyId);

        try {
            $price = $product->prices()->create([
                'currency_id' => $currency->id,
                'price' => $data->price ?? $this->converter->convert($product->price, $product->currency, $currency),
            ]);
        } catch (UniqueConstraintViolationException) {
            // Another request registered the same currency between the check and the insert.
            throw new CurrencyAlreadyRegisteredException;
        }

        return $price->setRelation('currency', $currency);
    }
}
