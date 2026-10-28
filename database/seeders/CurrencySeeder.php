<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Exchange rates are expressed against USD (reference currency = 1).
     */
    private const CURRENCIES = [
        ['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1],
        ['name' => 'EUR', 'symbol' => '€', 'exchange_rate' => 0.92],
        ['name' => 'GBP', 'symbol' => '£', 'exchange_rate' => 0.79],
        ['name' => 'MXN', 'symbol' => '$', 'exchange_rate' => 18.5],
        ['name' => 'COP', 'symbol' => '$', 'exchange_rate' => 4100],
    ];

    public function run(): void
    {
        foreach (self::CURRENCIES as $currency) {
            Currency::updateOrCreate(
                ['name' => $currency['name']],
                $currency,
            );
        }
    }
}
