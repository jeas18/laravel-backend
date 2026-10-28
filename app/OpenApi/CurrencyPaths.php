<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/currencies',
    operationId: 'listCurrencies',
    summary: 'Listar divisas',
    description: 'Catálogo de solo lectura con los `currency_id` válidos.',
    security: [['sanctum' => []]],
    tags: ['Currencies'],
    responses: [
        new OA\Response(response: 200, description: 'Divisas ordenadas por nombre', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Currency')),
        ])),
        new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
    ],
)]
class CurrencyPaths {}
