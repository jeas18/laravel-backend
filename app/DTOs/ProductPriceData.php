<?php

namespace App\DTOs;

final readonly class ProductPriceData
{
    public function __construct(
        public int $currencyId,
        public ?string $price = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            currencyId: (int) $data['currency_id'],
            price: isset($data['price']) ? (string) $data['price'] : null,
        );
    }
}
