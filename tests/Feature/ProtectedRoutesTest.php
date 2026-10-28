<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProtectedRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string, string}>
     */
    public static function protectedRoutes(): array
    {
        return [
            'list currencies' => ['GET', '/api/v1/currencies'],
            'list products' => ['GET', '/api/v1/products'],
            'create product' => ['POST', '/api/v1/products'],
            'show product' => ['GET', '/api/v1/products/{id}'],
            'replace product' => ['PUT', '/api/v1/products/{id}'],
            'update product' => ['PATCH', '/api/v1/products/{id}'],
            'delete product' => ['DELETE', '/api/v1/products/{id}'],
            'list prices' => ['GET', '/api/v1/products/{id}/prices'],
            'create price' => ['POST', '/api/v1/products/{id}/prices'],
        ];
    }

    #[DataProvider('protectedRoutes')]
    public function test_route_requires_authentication(string $method, string $uri): void
    {
        $product = Product::factory()->create();

        $this->json($method, str_replace('{id}', (string) $product->id, $uri))
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'No autenticado.']);

        $this->assertNotSoftDeleted($product);
    }

    public function test_api_responds_json_even_without_accept_header(): void
    {
        $this->get('/api/v1/products')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'No autenticado.']);
    }
}
