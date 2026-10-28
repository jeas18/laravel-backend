<?php

/*
| Spanish validation messages for the rules used by this API.
| Rules not listed here fall back to the English defaults.
*/

return [
    'confirmed' => 'La confirmación del campo :attribute no coincide.',
    'decimal' => 'El campo :attribute debe tener :decimal decimales.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'exists' => 'El valor seleccionado en :attribute no existe.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe superar los :max caracteres.',
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'numeric' => 'El campo :attribute debe ser un número.',
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser un texto.',
    'unique' => 'El valor de :attribute ya está registrado.',

    'custom' => [
        'email' => [
            'unique' => 'El correo electrónico ya está registrado.',
        ],
    ],

    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'description' => 'descripción',
        'price' => 'precio',
        'currency_id' => 'divisa',
        'tax_cost' => 'costo de impuestos',
        'manufacturing_cost' => 'costo de fabricación',
        'search' => 'búsqueda',
        'per_page' => 'elementos por página',
    ],
];
