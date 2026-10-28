<?php

namespace Tests\Unit;

use App\Models\Currency;
use App\Services\CurrencyConverter;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CurrencyConverterTest extends TestCase
{
    private CurrencyConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->converter = new CurrencyConverter;
    }

    /**
     * @return array<string, array{string, string, string, string}>
     */
    public static function conversions(): array
    {
        return [
            'USD to EUR' => ['100', '1', '0.92', '92.00'],
            'EUR to USD' => ['220', '0.92', '1', '239.13'],
            'EUR to COP' => ['92', '0.92', '4100', '410000.00'],
            'MXN to USD' => ['1890', '18.5', '1', '102.16'],
            'decimal amount' => ['0.10', '1', '3', '0.30'],
        ];
    }

    #[DataProvider('conversions')]
    public function test_converts_between_currencies(string $amount, string $fromRate, string $toRate, string $expected): void
    {
        $result = $this->converter->convert($amount, $this->currency(1, $fromRate), $this->currency(2, $toRate));

        $this->assertSame($expected, $result);
    }

    public function test_rounds_half_away_from_zero(): void
    {
        $this->assertSame('0.13', $this->converter->convert('1', $this->currency(1, '1'), $this->currency(2, '0.125')));
        $this->assertSame('0.12', $this->converter->convert('1', $this->currency(1, '1'), $this->currency(2, '0.124')));
    }

    public function test_same_currency_returns_the_amount_with_two_decimals(): void
    {
        $usd = $this->currency(1, '1');

        $this->assertSame('1200.50', $this->converter->convert('1200.5', $usd, $usd));
    }

    private function currency(int $id, string $exchangeRate): Currency
    {
        return (new Currency(['exchange_rate' => $exchangeRate]))->forceFill(['id' => $id]);
    }
}
