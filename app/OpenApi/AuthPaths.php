<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/auth/register',
    operationId: 'register',
    summary: 'Registrar un usuario',
    tags: ['Auth'],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['name', 'email', 'password', 'password_confirmation'],
        properties: [
            new OA\Property(property: 'name', type: 'string', example: 'Ana'),
            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ana@example.com'),
            new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'secret123'),
            new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'secret123'),
        ],
    )),
    responses: [
        new OA\Response(response: 201, description: 'Usuario creado', content: new OA\JsonContent(ref: '#/components/schemas/AuthToken')),
        new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        new OA\Response(ref: '#/components/responses/TooManyRequests', response: 429),
    ],
)]
#[OA\Post(
    path: '/auth/login',
    operationId: 'login',
    summary: 'Iniciar sesión y obtener un token',
    description: 'Límite: 5 intentos por minuto por correo e IP.',
    tags: ['Auth'],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
        required: ['email', 'password'],
        properties: [
            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'demo@example.com'),
            new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
        ],
    )),
    responses: [
        new OA\Response(response: 200, description: 'Token emitido', content: new OA\JsonContent(ref: '#/components/schemas/AuthToken')),
        new OA\Response(
            response: 401,
            description: 'Credenciales inválidas',
            content: new OA\JsonContent(ref: '#/components/schemas/Message', example: ['message' => 'Credenciales inválidas.']),
        ),
        new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        new OA\Response(ref: '#/components/responses/TooManyRequests', response: 429),
    ],
)]
#[OA\Post(
    path: '/auth/logout',
    operationId: 'logout',
    summary: 'Cerrar sesión (revoca el token actual)',
    security: [['sanctum' => []]],
    tags: ['Auth'],
    responses: [
        new OA\Response(response: 204, description: 'Token revocado'),
        new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
    ],
)]
class AuthPaths {}
