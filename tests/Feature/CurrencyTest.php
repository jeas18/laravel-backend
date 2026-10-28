<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_currencies_ordered_by_name(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Currency::factory()->create(['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1]);
        Currency::factory()->create(['name' => 'EUR', 'symbol' => '€', 'exchange_rate' => 0.92]);

        $this->getJson('/api/v1/currencies')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'EUR')
            ->assertJsonPath('data.0.symbol', '€')
            ->assertJsonPath('data.0.exchange_rate', '0.920000')
            ->assertJsonPath('data.1.name', 'USD');
    }
}
