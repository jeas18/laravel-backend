<?php

namespace App\Exceptions;

class CurrencyAlreadyRegisteredException extends BusinessRuleException
{
    public function __construct()
    {
        parent::__construct('El producto ya tiene un precio registrado en la divisa seleccionada.');
    }

    protected function field(): string
    {
        return 'currency_id';
    }
}
