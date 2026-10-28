<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/products/{product}/prices',
    operationId: 'listProductPrices',
    summary: 'Listar los precios de un producto',
    security: [['sanctum' => []]],
    tags: ['Product Prices'],
    parameters: [new OA\PathParameter(name: 'product', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
    responses: [
        new OA\Response(response: 200, description: 'Precios en otras divisas junto con el producto y su precio base', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ProductPrice')),
            new OA\Property(property: 'product', ref: '#/components/schemas/Product'),
        ])),
        new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        new OA\Response(ref: '#/components/responses/NotFound', response: 404),
    ],
)]
#[OA\Post(
    path: '/products/{product}/prices',
    operationId: 'createProductPrice',
    summary: 'Registrar un precio en otra divisa',
    description: "Reglas:\n- No se admite la divisa base del producto.\n- Solo un precio por divisa.\n"
        .'- Si `price` se omite, se calcula con las tasas de cambio y se redondea a 2 decimales.',
    security: [['sanctum' => []]],
    tags: ['Product Prices'],
    parameters: [new OA\PathParameter(name: 'product', required: true, schema: new OA\Schema(type: 'integer'), example: 2)],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductPriceInput')),
    responses: [
        new OA\Response(response: 201, description: 'Precio registrado', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/ProductPrice'),
        ])),
        new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        new OA\Response(
            response: 422,
            description: 'Datos inválidos, divisa base o divisa ya registrada',
            content: new OA\JsonContent(ref: '#/components/schemas/ValidationError', example: [
                'message' => 'El producto ya tiene un precio registrado en la divisa seleccionada.',
                'errors' => ['currency_id' => ['El producto ya tiene un precio registrado en la divisa seleccionada.']],
            ]),
        ),
    ],
)]
class ProductPricePaths {}
