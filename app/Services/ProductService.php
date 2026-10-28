<?php

namespace App\Services;

use App\DTOs\ProductData;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public const DEFAULT_PER_PAGE = 15;

    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function paginate(?string $search = null, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return Product::query()
            ->with('currency')
            ->search($search)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(ProductData $data): Product
    {
        return Product::create($data->toArray())->load('currency');
    }

    public function update(Product $product, ProductData $data): Product
    {
        $product->update($data->toArray());

        return $product->load('currency');
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
