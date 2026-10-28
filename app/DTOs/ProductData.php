<?php

namespace App\DTOs;

final readonly class ProductData
{
    public const FIELDS = ['name', 'description', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost'];

    public function __construct(
        public string $name,
        public string $description,
        public string $price,
        public int $currencyId,
        public string $taxCost,
        public string $manufacturingCost,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'],
            price: (string) $data['price'],
            currencyId: (int) $data['currency_id'],
            taxCost: (string) $data['tax_cost'],
            manufacturingCost: (string) $data['manufacturing_cost'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'currency_id' => $this->currencyId,
            'tax_cost' => $this->taxCost,
            'manufacturing_cost' => $this->manufacturingCost,
        ];
    }
}
