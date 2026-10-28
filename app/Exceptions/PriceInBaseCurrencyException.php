<?php

namespace App\Exceptions;

class PriceInBaseCurrencyException extends BusinessRuleException
{
    public function __construct()
    {
        parent::__construct('La divisa seleccionada es la divisa base del producto; su precio ya está definido en el producto.');
    }

    protected function field(): string
    {
        return 'currency_id';
    }
}
