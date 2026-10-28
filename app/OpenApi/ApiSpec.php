<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Products API',
    description: "API RESTful para gestionar productos y sus precios en múltiples divisas.\n\n"
        .'**Autenticación:** obtén un token con `POST /auth/login` (usuario demo: `demo@example.com` / `password`) '
        .'y pégalo en el botón **Authorize**.',
)]
#[OA\Server(url: '/api/v1', description: 'API v1')]
#[OA\SecurityScheme(securityScheme: 'sanctum', type: 'http', scheme: 'bearer', description: 'Token de Sanctum devuelto por login o register')]
#[OA\Tag(name: 'Auth', description: 'Registro, inicio y cierre de sesión')]
#[OA\Tag(name: 'Currencies', description: 'Catálogo de divisas')]
#[OA\Tag(name: 'Products', description: 'CRUD de productos')]
#[OA\Tag(name: 'Product Prices', description: 'Precios de un producto en otras divisas')]
#[OA\Response(
    response: 'Unauthenticated',
    description: 'Token ausente, inválido o revocado',
    content: new OA\JsonContent(ref: '#/components/schemas/Message', example: ['message' => 'No autenticado.']),
)]
#[OA\Response(
    response: 'NotFound',
    description: 'Recurso no encontrado',
    content: new OA\JsonContent(ref: '#/components/schemas/Message', example: ['message' => 'Recurso no encontrado.']),
)]
#[OA\Response(
    response: 'ValidationError',
    description: 'Datos inválidos o regla de negocio incumplida',
    content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
)]
#[OA\Response(
    response: 'TooManyRequests',
    description: 'Límite de peticiones superado (ver cabecera Retry-After)',
    content: new OA\JsonContent(ref: '#/components/schemas/Message', example: ['message' => 'Demasiados intentos. Inténtalo de nuevo más tarde.']),
)]
class ApiSpec {}
