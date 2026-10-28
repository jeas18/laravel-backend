<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Message',
    properties: [new OA\Property(property: 'message', type: 'string')],
)]
#[OA\Schema(
    schema: 'ValidationError',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'El campo nombre es obligatorio. (y 1 error más)'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string')),
            example: ['name' => ['El campo nombre es obligatorio.'], 'price' => ['El campo precio debe ser al menos 0.']],
        ),
    ],
)]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Demo User'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'demo@example.com'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ],
)]
#[OA\Schema(
    schema: 'AuthToken',
    properties: [
        new OA\Property(property: 'data', properties: [
            new OA\Property(property: 'user', ref: '#/components/schemas/User'),
            new OA\Property(property: 'access_token', type: 'string', example: '1|Vb3kQ2x...'),
            new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
        ], type: 'object'),
    ],
)]
#[OA\Schema(
    schema: 'Currency',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'USD'),
        new OA\Property(property: 'symbol', type: 'string', example: '$'),
        new OA\Property(property: 'exchange_rate', type: 'string', description: 'Tasa respecto a la divisa de referencia (USD = 1)', example: '1.000000'),
    ],
)]
#[OA\Schema(
    schema: 'Product',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro 14'),
        new OA\Property(property: 'description', type: 'string', example: 'Portátil de 14 pulgadas, 16 GB de RAM y 512 GB SSD'),
        new OA\Property(property: 'price', type: 'string', description: 'Precio en la divisa base', example: '1200.00'),
        new OA\Property(property: 'currency_id', type: 'integer', example: 1),
        new OA\Property(property: 'currency', ref: '#/components/schemas/Currency'),
        new OA\Property(property: 'tax_cost', type: 'string', example: '228.00'),
        new OA\Property(property: 'manufacturing_cost', type: 'string', example: '650.00'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
)]
#[OA\Schema(
    schema: 'ProductInput',
    required: ['name', 'description', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Tablet X'),
        new OA\Property(property: 'description', type: 'string', maxLength: 255, example: 'Tablet de 11 pulgadas con 128 GB'),
        new OA\Property(property: 'price', type: 'number', format: 'decimal', minimum: 0, description: 'Máximo 2 decimales', example: 499.99),
        new OA\Property(property: 'currency_id', type: 'integer', description: 'ID de la divisa base (ver GET /currencies)', example: 1),
        new OA\Property(property: 'tax_cost', type: 'number', format: 'decimal', minimum: 0, description: 'Monto, no porcentaje', example: 95.00),
        new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'decimal', minimum: 0, example: 210.50),
    ],
)]
#[OA\Schema(
    schema: 'ProductPrice',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'currency_id', type: 'integer', example: 2),
        new OA\Property(property: 'currency', ref: '#/components/schemas/Currency'),
        new OA\Property(property: 'price', type: 'string', example: '1104.00'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
)]
#[OA\Schema(
    schema: 'ProductPriceInput',
    required: ['currency_id'],
    properties: [
        new OA\Property(property: 'currency_id', type: 'integer', description: 'Divisa distinta de la divisa base del producto', example: 2),
        new OA\Property(
            property: 'price',
            type: 'number',
            format: 'decimal',
            minimum: 0,
            nullable: true,
            description: 'Opcional. Si se omite, se calcula: precio base × tasa destino ÷ tasa origen',
            example: 1104.00,
        ),
    ],
)]
#[OA\Schema(
    schema: 'PaginationLinks',
    properties: [
        new OA\Property(property: 'first', type: 'string', example: 'http://127.0.0.1:8000/api/v1/products?page=1'),
        new OA\Property(property: 'last', type: 'string', example: 'http://127.0.0.1:8000/api/v1/products?page=2'),
        new OA\Property(property: 'prev', type: 'string', nullable: true),
        new OA\Property(property: 'next', type: 'string', nullable: true),
    ],
)]
#[OA\Schema(
    schema: 'PaginationMeta',
    properties: [
        new OA\Property(property: 'current_page', type: 'integer', example: 1),
        new OA\Property(property: 'from', type: 'integer', example: 1),
        new OA\Property(property: 'last_page', type: 'integer', example: 2),
        new OA\Property(property: 'path', type: 'string'),
        new OA\Property(property: 'per_page', type: 'integer', example: 15),
        new OA\Property(property: 'to', type: 'integer', example: 15),
        new OA\Property(property: 'total', type: 'integer', example: 20),
    ],
)]
class Schemas {}
