<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyConverter
{
    private const SCALE = 10;

    private const MONEY_DECIMALS = 2;

    /**
     * amount × to.rate ÷ from.rate, rounded half away from zero to 2 decimals.
     */
    public function convert(string $amount, Currency $from, Currency $to): string
    {
        if ($from->is($to)) {
            return bcround($amount, self::MONEY_DECIMALS);
        }

        $converted = bcdiv(
            bcmul($amount, (string) $to->exchange_rate, self::SCALE),
            (string) $from->exchange_rate,
            self::SCALE,
        );

        return bcround($converted, self::MONEY_DECIMALS);
    }
}
