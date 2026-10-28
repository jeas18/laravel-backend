# Products API

API RESTful en Laravel para gestionar productos y registrar sus precios en múltiples divisas.

- CRUD de productos con búsqueda y paginación
- Precios por divisa, ingresados manualmente o calculados con las tasas de cambio
- Autenticación por token (Laravel Sanctum)
- Documentación interactiva con Swagger
- 50 tests automáticos

## Stack

| | |
|---|---|
| Framework | Laravel 13 |
| Lenguaje | PHP 8.4+ (con extensión `bcmath`) |
| Base de datos | SQLite (por defecto) o MySQL |
| Autenticación | Laravel Sanctum |
| Documentación | OpenAPI 3 / Swagger (L5-Swagger) |
| Tests | PHPUnit |

## Instalación

Requisitos: PHP 8.4+ con `bcmath` y Composer.

```bash
git clone https://github.com/jeas18/laravel-backend.git
cd laravel-backend

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

php artisan l5-swagger:generate
php artisan serve
```

La API queda disponible en `http://127.0.0.1:8000/api/v1`.

<details>
<summary>Usar MySQL en lugar de SQLite</summary>

En `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=products_api
DB_USERNAME=root
DB_PASSWORD=
```

Después: `php artisan migrate --seed`.
</details>

### Datos de ejemplo

El seeder crea:

- **Usuario demo:** `demo@example.com` / `password`
- **5 divisas:** USD, EUR, GBP, MXN, COP (tasas respecto a USD = 1)
- **10 productos**, algunos con precios en otras divisas

## Documentación

**Swagger UI:** http://127.0.0.1:8000/api/documentation

1. Ejecuta `POST /auth/login` con el usuario demo.
2. Copia el `access_token` de la respuesta.
3. Pulsa **Authorize**, pega el token y prueba cualquier endpoint.

La especificación OpenAPI está en [`storage/api-docs/api-docs.json`](storage/api-docs/api-docs.json) y puede importarse directamente en Postman o Insomnia.

## Endpoints

Base: `/api/v1`. Todas las rutas, salvo `register` y `login`, requieren el header `Authorization: Bearer <token>`.

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/auth/register` | Registrar usuario y obtener token |
| POST | `/auth/login` | Iniciar sesión y obtener token |
| POST | `/auth/logout` | Revocar el token actual |
| GET | `/currencies` | Listar divisas |
| GET | `/products` | Listar productos (`?search=`, `?per_page=`, `?page=`) |
| POST | `/products` | Crear producto |
| GET | `/products/{id}` | Obtener producto |
| PUT | `/products/{id}` | Actualizar producto (todos los campos) |
| PATCH | `/products/{id}` | Actualizar parcialmente |
| DELETE | `/products/{id}` | Eliminar producto (soft delete) |
| GET | `/products/{id}/prices` | Listar precios del producto, junto con el producto y su precio base |
| POST | `/products/{id}/prices` | Registrar precio en otra divisa |

### Ejemplo

```bash
# 1. Obtener token
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@example.com","password":"password"}'

# 2. Registrar un precio en EUR para el producto 2 (se calcula con las tasas de cambio)
curl -X POST http://127.0.0.1:8000/api/v1/products/2/prices \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"currency_id": 2}'
```

```json
{
  "data": {
    "id": 6,
    "product_id": 2,
    "currency_id": 2,
    "currency": { "id": 2, "name": "EUR", "symbol": "€", "exchange_rate": "0.920000" },
    "price": "349.60",
    "created_at": "2026-10-28T10:00:00+00:00",
    "updated_at": "2026-10-28T10:00:00+00:00"
  }
}
```

### Respuestas de error

| Código | Cuándo |
|---|---|
| 401 | Token ausente, inválido o revocado |
| 404 | Recurso inexistente o eliminado |
| 422 | Datos inválidos o regla de negocio incumplida |
| 429 | Límite de peticiones superado |

Los errores 422 siempre tienen la misma forma, provengan de la validación o de una regla de negocio:

```json
{
  "message": "El producto ya tiene un precio registrado en la divisa seleccionada.",
  "errors": { "currency_id": ["El producto ya tiene un precio registrado en la divisa seleccionada."] }
}
```

## Arquitectura

MVC con capa de servicios y DTOs:

```
Request → FormRequest → Controller → DTO → Service → Model (Eloquent)
                                                         ↓
                                        JSON ← Resource ←┘
```

| Capa | Responsabilidad |
|---|---|
| `Http/Requests` | Validación y construcción del DTO (`toData()`) |
| `Http/Controllers/Api/V1` | Recibir la petición, delegar en el servicio y responder |
| `DTOs` | Transportar datos validados entre capas (inmutables) |
| `Services` | Lógica de negocio y conversión de divisas |
| `Exceptions` | Reglas de negocio incumplidas (responden 422) |
| `Models` | Eloquent: relaciones, casts y scopes |
| `Http/Resources` | Formato de las respuestas JSON |
| `OpenApi` | Documentación Swagger, separada de los controladores |

### Modelo de datos

```
currencies (1) ──< (N) products          divisa base del producto
products   (1) ──< (N) product_prices    precios en otras divisas
currencies (1) ──< (N) product_prices    divisa de cada precio
```

- `products.price` es el precio en la **divisa base** (`products.currency_id`).
- `product_prices` guarda los precios del mismo producto **en otras divisas**, uno por divisa.

## Decisiones de diseño

1. **Dinero en `decimal`, nunca `float`.** Los cálculos usan BCMath y los montos viajan como `string` en los DTOs y en el JSON, para evitar errores de precisión.
2. **Un precio por divisa y producto.** El servicio lo comprueba y un índice `unique` lo garantiza en la base de datos ante peticiones simultáneas.
3. **No se registran precios en la divisa base.** Ese precio ya vive en `products.price`; duplicarlo podría generar inconsistencias.
4. **`price` es opcional al registrar un precio.** Si se omite, se calcula como `precio_base × tasa_destino ÷ tasa_origen`, redondeado a 2 decimales.
5. **Los precios registrados no se recalculan** si cambia una tasa de cambio: son una fotografía del momento.
6. **`tax_cost` es un monto**, no un porcentaje.
7. **Divisas de solo lectura.** Se cargan por seeder y se exponen en `GET /currencies` para conocer los `currency_id` válidos.
8. **Soft delete en productos.** Un producto eliminado responde 404, pero se conserva junto con sus precios. Una divisa en uso no puede borrarse (`restrictOnDelete`).
9. **PUT reemplaza, PATCH modifica.** PUT exige todos los campos; PATCH solo los enviados.
10. **Búsqueda sin índice.** `?search=` usa `LIKE '%término%'`, que no aprovecha índices B-tree; para grandes volúmenes convendría FULLTEXT o Scout.
11. **Rutas versionadas** en `/api/v1`.

## Seguridad

- Autenticación por token con Sanctum; el logout revoca solo el token actual.
- **Rate limiting:** 5 intentos por minuto por correo e IP en `login`/`register`; 60 peticiones por minuto por usuario en el resto.
- Mismo mensaje para correo inexistente y contraseña incorrecta, para evitar la enumeración de usuarios.
- Validación estricta de todas las entradas (`per_page` limitado a 100).
- Asignación masiva restringida con `#[Fillable]`; las respuestas pasan por Resources y nunca exponen la contraseña.
- Los 404 no revelan nombres internos de clases.
- Todas las respuestas de la API son JSON, incluso sin el header `Accept`.

## Tests

```bash
php artisan test
```

50 tests (196 aserciones) sobre SQLite en memoria:

| Suite | Cubre |
|---|---|
| `Unit/CurrencyConverterTest` | Conversiones, redondeo, misma divisa |
| `Feature/AuthTest` | Registro, login, logout, rate limiting |
| `Feature/ProtectedRoutesTest` | 401 en todas las rutas protegidas |
| `Feature/CurrencyTest` | Listado de divisas |
| `Feature/ProductTest` | CRUD, búsqueda, paginación, validaciones, PUT vs PATCH, soft delete |
| `Feature/ProductPriceTest` | Precio manual y calculado, divisa base, duplicados, 404 |

## Mejoras futuras

- Actualizar las tasas de cambio desde una API externa, con caché.
- Roles y permisos (por ejemplo, solo administradores eliminan productos).
- Búsqueda FULLTEXT o Laravel Scout para catálogos grandes.
- Docker y pipeline de CI que ejecute los tests en cada PR.
