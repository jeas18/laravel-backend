<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductPriceTest extends TestCase
{
    use RefreshDatabase;

    private Currency $usd;

    private Currency $eur;

    private Currency $cop;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());

        $this->usd = Currency::factory()->create(['name' => 'USD', 'exchange_rate' => 1]);
        $this->eur = Currency::factory()->create(['name' => 'EUR', 'exchange_rate' => 0.92]);
        $this->cop = Currency::factory()->create(['name' => 'COP', 'exchange_rate' => 4100]);

        $this->product = Product::factory()->for($this->usd)->create(['price' => 100]);
    }

    public function test_lists_only_the_prices_of_the_product(): void
    {
        ProductPrice::factory()->for($this->product)->for($this->eur)->create(['price' => 92]);
        ProductPrice::factory()->for($this->product)->for($this->cop)->create(['price' => 410000]);
        ProductPrice::factory()->for(Product::factory()->for($this->usd))->for($this->eur)->create();

        $this->getJson("/api/v1/products/{$this->product->id}/prices")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.price', '92.00')
            ->assertJsonPath('data.0.currency.name', 'EUR')
            ->assertJsonPath('data.1.currency.name', 'COP');
    }

    public function test_listing_includes_the_product_with_its_base_currency(): void
    {
        $this->getJson("/api/v1/products/{$this->product->id}/prices")
            ->assertOk()
            ->assertJsonPath('product.id', $this->product->id)
            ->assertJsonPath('product.name', $this->product->name)
            ->assertJsonPath('product.price', '100.00')
            ->assertJsonPath('product.currency.name', 'USD');
    }

    public function test_registers_a_price_given_manually(): void
    {
        $this->postJson("/api/v1/products/{$this->product->id}/prices", ['currency_id' => $this->eur->id, 'price' => 85.5])
            ->assertCreated()
            ->assertJsonPath('data.product_id', $this->product->id)
            ->assertJsonPath('data.currency.name', 'EUR')
            ->assertJsonPath('data.price', '85.50');

        $this->assertDatabaseHas('product_prices', ['product_id' => $this->product->id, 'currency_id' => $this->eur->id]);
    }

    public function test_calculates_the_price_from_exchange_rates_when_omitted(): void
    {
        $this->postJson("/api/v1/products/{$this->product->id}/prices", ['currency_id' => $this->eur->id])
            ->assertCreated()
            ->assertJsonPath('data.price', '92.00');

        $this->postJson("/api/v1/products/{$this->product->id}/prices", ['currency_id' => $this->cop->id])
            ->assertCreated()
            ->assertJsonPath('data.price', '410000.00');
    }

    public function test_calculates_the_price_for_a_product_with_non_reference_base_currency(): void
    {
        $product = Product::factory()->for($this->eur)->create(['price' => 220]);

        $this->postJson("/api/v1/products/{$product->id}/prices", ['currency_id' => $this->usd->id])
            ->assertCreated()
            ->assertJsonPath('data.price', '239.13');
    }

    public function test_rejects_a_price_in_the_base_currency(): void
    {
        $this->postJson("/api/v1/products/{$this->product->id}/prices", ['currency_id' => $this->usd->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['currency_id']);

        $this->assertDatabaseCount('product_prices', 0);
    }

    public function test_rejects_a_currency_already_registered(): void
    {
        ProductPrice::factory()->for($this->product)->for($this->eur)->create();

        $this->postJson("/api/v1/products/{$this->product->id}/prices", ['currency_id' => $this->eur->id, 'price' => 90])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['currency_id' => 'El producto ya tiene un precio registrado en la divisa seleccionada.']);

        $this->assertDatabaseCount('product_prices', 1);
    }

    public function test_validates_the_price_payload(): void
    {
        $this->postJson("/api/v1/products/{$this->product->id}/prices", ['currency_id' => 999, 'price' => -5])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['currency_id', 'price']);
    }

    public function test_returns_not_found_for_unknown_product(): void
    {
        $this->getJson('/api/v1/products/999/prices')->assertNotFound();
        $this->postJson('/api/v1/products/999/prices', ['currency_id' => $this->eur->id])->assertNotFound();
    }

    public function test_returns_not_found_for_deleted_product(): void
    {
        $this->product->delete();

        $this->getJson("/api/v1/products/{$this->product->id}/prices")->assertNotFound();
    }
}
