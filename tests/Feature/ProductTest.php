<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());

        $this->currency = Currency::factory()->create();
    }

    public function test_lists_products_paginated(): void
    {
        Product::factory()->count(20)->for($this->currency)->create();

        $this->getJson('/api/v1/products?per_page=5')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.total', 20)
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'description', 'price', 'currency_id', 'currency', 'tax_cost', 'manufacturing_cost', 'created_at', 'updated_at']],
                'links',
                'meta',
            ]);
    }

    public function test_uses_fifteen_products_per_page_by_default(): void
    {
        Product::factory()->count(20)->for($this->currency)->create();

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonCount(15, 'data');
    }

    public function test_searches_products_by_name(): void
    {
        Product::factory()->for($this->currency)->create(['name' => 'Hub USB-C']);
        Product::factory()->for($this->currency)->create(['name' => 'Monitor 4K']);

        $this->getJson('/api/v1/products?search=usb')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Hub USB-C');
    }

    public function test_rejects_invalid_list_parameters(): void
    {
        $this->getJson('/api/v1/products?per_page=500&search='.str_repeat('a', 101))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page', 'search']);
    }

    public function test_creates_a_product(): void
    {
        $response = $this->postJson('/api/v1/products', $this->validPayload());

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Tablet X')
            ->assertJsonPath('data.price', '499.99')
            ->assertJsonPath('data.tax_cost', '95.00')
            ->assertJsonPath('data.manufacturing_cost', '210.50')
            ->assertJsonPath('data.currency.id', $this->currency->id);

        $this->assertDatabaseHas('products', ['name' => 'Tablet X', 'currency_id' => $this->currency->id]);
    }

    public function test_create_requires_all_fields(): void
    {
        $this->postJson('/api/v1/products', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'description', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost']);
    }

    public function test_create_rejects_invalid_amounts_and_unknown_currency(): void
    {
        $this->postJson('/api/v1/products', $this->validPayload([
            'price' => -1,
            'tax_cost' => 'abc',
            'manufacturing_cost' => 10.555,
            'currency_id' => 999,
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price', 'tax_cost', 'manufacturing_cost', 'currency_id']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_shows_a_product_with_its_currency(): void
    {
        $product = Product::factory()->for($this->currency)->create();

        $this->getJson("/api/v1/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.currency.name', $this->currency->name);
    }

    public function test_returns_not_found_for_unknown_product(): void
    {
        $this->getJson('/api/v1/products/999')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Recurso no encontrado.']);
    }

    public function test_put_replaces_the_product(): void
    {
        $product = Product::factory()->for($this->currency)->create();

        $this->putJson("/api/v1/products/{$product->id}", $this->validPayload(['name' => 'Tablet X Pro', 'price' => 599.99]))
            ->assertOk()
            ->assertJsonPath('data.name', 'Tablet X Pro')
            ->assertJsonPath('data.price', '599.99');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Tablet X Pro']);
    }

    public function test_put_requires_all_fields(): void
    {
        $product = Product::factory()->for($this->currency)->create();

        $this->putJson("/api/v1/products/{$product->id}", ['price' => 100])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'description', 'currency_id', 'tax_cost', 'manufacturing_cost']);
    }

    public function test_patch_updates_only_the_given_fields(): void
    {
        $product = Product::factory()->for($this->currency)->create(['name' => 'Original', 'price' => 100]);

        $this->patchJson("/api/v1/products/{$product->id}", ['price' => 150.5])
            ->assertOk()
            ->assertJsonPath('data.name', 'Original')
            ->assertJsonPath('data.price', '150.50');
    }

    public function test_patch_still_validates_the_given_fields(): void
    {
        $product = Product::factory()->for($this->currency)->create();

        $this->patchJson("/api/v1/products/{$product->id}", ['price' => -10])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price']);
    }

    public function test_deletes_a_product_with_soft_delete(): void
    {
        $product = Product::factory()->for($this->currency)->create();

        $this->deleteJson("/api/v1/products/{$product->id}")->assertNoContent();

        $this->assertSoftDeleted($product);
        $this->getJson("/api/v1/products/{$product->id}")->assertNotFound();
        $this->getJson('/api/v1/products')->assertJsonCount(0, 'data');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'name' => 'Tablet X',
            'description' => 'Tablet de 11 pulgadas',
            'price' => 499.99,
            'currency_id' => $this->currency->id,
            'tax_cost' => 95,
            'manufacturing_cost' => 210.50,
            ...$overrides,
        ];
    }
}
