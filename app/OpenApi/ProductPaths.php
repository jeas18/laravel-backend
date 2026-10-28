<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/products',
    operationId: 'listProducts',
    summary: 'Listar productos',
    security: [['sanctum' => []]],
    tags: ['Products'],
    parameters: [
        new OA\QueryParameter(name: 'search', description: 'Filtra por nombre (contiene)', schema: new OA\Schema(type: 'string', maxLength: 100), example: 'laptop'),
        new OA\QueryParameter(name: 'per_page', description: 'Productos por página (1–100)', schema: new OA\Schema(type: 'integer', default: 15, maximum: 100, minimum: 1)),
        new OA\QueryParameter(name: 'page', schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Listado paginado', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Product')),
            new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
            new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
        ])),
        new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
    ],
)]
#[OA\Post(
    path: '/products',
    operationId: 'createProduct',
    summary: 'Crear un producto',
    security: [['sanctum' => []]],
    tags: ['Products'],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')),
    responses: [
        new OA\Response(response: 201, description: 'Producto creado', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
        ])),
        new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
    ],
)]
#[OA\Get(
    path: '/products/{product}',
    operationId: 'showProduct',
    summary: 'Obtener un producto',
    security: [['sanctum' => []]],
    tags: ['Products'],
    parameters: [new OA\PathParameter(name: 'product', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
    responses: [
        new OA\Response(response: 200, description: 'Producto', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
        ])),
        new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        new OA\Response(ref: '#/components/responses/NotFound', response: 404),
    ],
)]
#[OA\Put(
    path: '/products/{product}',
    operationId: 'replaceProduct',
    summary: 'Actualizar un producto (todos los campos)',
    security: [['sanctum' => []]],
    tags: ['Products'],
    parameters: [new OA\PathParameter(name: 'product', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')),
    responses: [
        new OA\Response(response: 200, description: 'Producto actualizado', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
        ])),
        new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
    ],
)]
#[OA\Patch(
    path: '/products/{product}',
    operationId: 'updateProduct',
    summary: 'Actualizar parcialmente un producto',
    description: 'Solo se modifican los campos enviados.',
    security: [['sanctum' => []]],
    tags: ['Products'],
    parameters: [new OA\PathParameter(name: 'product', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'name', type: 'string'),
            new OA\Property(property: 'description', type: 'string'),
            new OA\Property(property: 'price', type: 'number', format: 'decimal'),
            new OA\Property(property: 'currency_id', type: 'integer'),
            new OA\Property(property: 'tax_cost', type: 'number', format: 'decimal'),
            new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'decimal'),
        ],
        example: ['price' => 1300.00],
    )),
    responses: [
        new OA\Response(response: 200, description: 'Producto actualizado', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
        ])),
        new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
    ],
)]
#[OA\Delete(
    path: '/products/{product}',
    operationId: 'deleteProduct',
    summary: 'Eliminar un producto',
    description: 'Borrado lógico (soft delete): el producto deja de estar disponible, pero se conserva junto con sus precios.',
    security: [['sanctum' => []]],
    tags: ['Products'],
    parameters: [new OA\PathParameter(name: 'product', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
    responses: [
        new OA\Response(response: 204, description: 'Producto eliminado'),
        new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        new OA\Response(ref: '#/components/responses/NotFound', response: 404),
    ],
)]
class ProductPaths {}
