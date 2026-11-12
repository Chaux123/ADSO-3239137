# Contrato de API REST — Toy Library

**Proyecto:** Toy Library — Juegoteca Comunitaria de Juguetes  
**Identificador:** `toy-library`  
**Programa:** Análisis y Desarrollo de Software (ADSO)  
**Ficha:** 3239137  
**Aprendiz:** A13  
**Versión:** 1.0  
**Estado:** Contrato propuesto

## 1. Propósito

Este documento establece el contrato HTTP de la API REST del sistema Toy Library.

La API permitirá que el frontend React interactúe con el backend Laravel para administrar juguetes, familias, promotores, préstamos, devoluciones y reportes.

El contrato define los recursos disponibles, métodos HTTP, parámetros, formatos de solicitudes y respuestas, permisos de acceso, validaciones y códigos de error.

La implementación deberá cumplir los requisitos definidos en `spec.md`, las restricciones establecidas en `data-model.md` y los principios documentados en `constitution.md` y `architecture.md`.

## 2. Convenciones generales

### 2.1. Ruta base

Todos los endpoints de negocio utilizarán la siguiente ruta base:

`/api/v1`

Por ejemplo:

`GET /api/v1/toys`

El endpoint de salud se expondrá dentro de la misma ruta base:

`GET /api/v1/health`

### 2.2. Formato de comunicación

Las solicitudes y respuestas utilizarán JSON con codificación UTF-8, excepto las operaciones relacionadas con imágenes.

Encabezados:

```http
Content-Type: application/json
Accept: application/json
```

Los nombres de los campos utilizarán inglés y formato `snake_case`.

Los textos visibles para el usuario estarán en español.

### 2.3. Identificadores

Los recursos utilizarán UUID versión 4.

Ejemplo:

`550e8400-e29b-41d4-a716-446655440000`

### 2.4. Fechas

Las fechas se almacenarán en UTC y se representarán en formato ISO 8601.

Ejemplo:

`2026-11-11T15:30:00Z`

Los campos temporales incluirán:

- `loaned_at`
- `due_at`
- `returned_at`
- `deleted_at`

### 2.5. Autenticación

Se utilizarán tokens JWT para proteger los recursos.

Las solicitudes autenticadas deberán incluir:

```http
Authorization: Bearer <token>
```

Los únicos endpoints públicos serán:

- `POST /api/v1/auth/login`
- `GET /api/v1/health`

Los demás requerirán autenticación y los permisos correspondientes.

## 3. Roles y permisos

El sistema contará con dos roles autenticados:

- `admin`: administrador.
- `promoter`: promotor.

Las familias serán registros de beneficiarios y no iniciarán sesión.

### 3.1. Matriz de permisos

| Recurso | Administrador | Promotor |
|---|---|---|
| Iniciar sesión | Sí | Sí |
| Consultar categorías | Sí | Sí |
| Registrar promotores | Sí | No |
| Consultar juguetes | Sí | Sí |
| Crear juguetes | Sí | No |
| Actualizar juguetes | Sí | No |
| Dar de baja juguetes | Sí | No |
| Gestionar imágenes | Sí | Solo consultar |
| Registrar familias | Sí | Sí |
| Consultar familias | Sí | Sí |
| Registrar préstamos | Sí | Sí |
| Registrar devoluciones | Sí | Sí |
| Consultar préstamos | Sí | Sí |
| Consultar vencidos | Sí | Sí |
| Consultar reportes | Sí | No |

Cuando un usuario autenticado intente acceder a una operación sin permisos, la API devolverá HTTP 403.

## 4. Códigos HTTP

La API utilizará los siguientes códigos:

| Código | Significado |
|---|---|
| `200` | Solicitud exitosa |
| `201` | Recurso creado correctamente |
| `204` | Operación exitosa sin contenido |
| `401` | Credenciales incorrectas o token inválido |
| `403` | Usuario sin permisos |
| `404` | Recurso no encontrado |
| `409` | Conflicto de negocio o disponibilidad |
| `422` | Datos inválidos o incumplimiento de validación |
| `500` | Error interno del servidor |

## 5. Estructura uniforme de errores

Todas las respuestas de error deberán mantener la siguiente estructura:

```json
{
  "code": "TOY_NOT_AVAILABLE",
  "message": "No hay unidades disponibles.",
  "details": []
}
```

### 5.1. Campos

| Campo | Tipo | Descripción |
|---|---|---|
| `code` | string | Identificador del error |
| `message` | string | Mensaje en español |
| `details` | array | Información adicional de validación |

No se expondrán consultas SQL, contraseñas, tokens, claves ni trazas internas.

### 5.2. Códigos de error

| Código | HTTP | Descripción |
|---|---|---|
| `INVALID_CREDENTIALS` | 401 | Credenciales incorrectas |
| `FORBIDDEN_ROLE` | 403 | Rol sin permisos |
| `NOT_FOUND` | 404 | Recurso inexistente |
| `USERNAME_TAKEN` | 409 | Nombre de usuario duplicado |
| `REFERENCE_TAKEN` | 409 | Referencia activa duplicada |
| `DOCUMENT_TAKEN` | 409 | Documento de familia duplicado |
| `TOY_HAS_OPEN_LOANS` | 409 | Juguete con préstamos pendientes |
| `TOY_NOT_AVAILABLE` | 409 | No existen unidades disponibles |
| `FAMILY_HAS_OVERDUE` | 409 | Familia con juguetes vencidos |
| `ITEM_ALREADY_RETURNED` | 409 | Línea ya devuelta |
| `FAMILY_LIMIT_EXCEEDED` | 422 | Límite de juguetes superado |
| `DUPLICATE_TOY_IN_LOAN` | 422 | Juguete repetido en el préstamo |
| `VALIDATION_ERROR` | 422 | Datos inválidos |
| `INVALID_DATE_RANGE` | 422 | Intervalo de fechas incorrecto |

## 6. Paginación

Los listados paginados aceptarán:

- `page`: número de página.
- `size`: cantidad de registros por página.

Valores:

- Página inicial: `1`.
- Tamaño predeterminado: `20`.
- Tamaño máximo: `30`.

Ejemplo:

`GET /api/v1/toys?page=1&size=20`

Formato de respuesta:

```json
{
  "items": [],
  "page": 1,
  "size": 20,
  "total": 0
}
```

## 7. Autenticación

### 7.1. Iniciar sesión

**Endpoint:** `POST /api/v1/auth/login`

**Acceso:** público.

**Solicitud:**

```json
{
  "username": "promoter01",
  "password": "example_password"
}
```

**Respuesta HTTP 200:**

```json
{
  "access_token": "<jwt_token>",
  "token_type": "Bearer",
  "expires_in": 28800,
  "role": "promoter"
}
```

El token tendrá una duración de ocho horas.

**Errores:**

- `401 INVALID_CREDENTIALS`
- `422 VALIDATION_ERROR`

### 7.2. Consultar estado del servicio

**Endpoint:** `GET /api/v1/health`

**Acceso:** público.

**Respuesta HTTP 200:**

```json
{
  "status": "ok"
}
```

## 8. Gestión de promotores

### 8.1. Registrar promotor

**Endpoint:** `POST /api/v1/users`

**Acceso:** administrador.

**Solicitud:**

```json
{
  "username": "promoter02",
  "password": "example_password",
  "role": "promoter"
}
```

**Respuesta HTTP 201:**

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440001",
  "username": "promoter02",
  "role": "promoter"
}
```

**Validaciones:**

- El nombre de usuario será obligatorio.
- Se normalizará a minúsculas y se eliminarán espacios extremos.
- No podrá existir otro usuario con el mismo nombre.
- El único rol que podrá crearse mediante este endpoint será `promoter`.
- La contraseña deberá almacenarse mediante hash seguro.
- No se devolverán contraseñas ni hashes.

**Errores:**

- `401` sin autenticación.
- `403` si el usuario no es administrador.
- `409 USERNAME_TAKEN`.
- `422 VALIDATION_ERROR`.

## 9. Categorías

### 9.1. Consultar categorías

**Endpoint:** `GET /api/v1/categories`

**Acceso:** administrador y promotor.

**Respuesta HTTP 200:**

```json
[
  {
    "id": "11111111-1111-4111-8111-111111111111",
    "name": "bebés"
  },
  {
    "id": "22222222-2222-4222-8222-222222222222",
    "name": "construcción"
  },
  {
    "id": "33333333-3333-4333-8333-333333333333",
    "name": "didácticos"
  },
  {
    "id": "44444444-4444-4444-8444-444444444444",
    "name": "exterior"
  },
  {
    "id": "55555555-5555-4555-8555-555555555555",
    "name": "simbólicos"
  }
]
```

Las cinco categorías estarán registradas inicialmente en PostgreSQL.

## 10. Gestión de juguetes

### 10.1. Listar y buscar juguetes

**Endpoint:** `GET /api/v1/toys`

**Acceso:** administrador y promotor.

**Parámetros opcionales:**

| Parámetro | Descripción |
|---|---|
| `q` | Buscar por nombre o marca |
| `category_id` | Filtrar por categoría |
| `page` | Página solicitada |
| `size` | Registros por página |

Ejemplo:

`GET /api/v1/toys?q=bloques&page=1&size=20`

**Respuesta HTTP 200:**

```json
{
  "items": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440002",
      "name": "Bloques de construcción",
      "brand": "EduPlay",
      "reference": "TOY-001",
      "category": {
        "id": "22222222-2222-4222-8222-222222222222",
        "name": "construcción"
      },
      "total_units": 5,
      "available_units": 3,
      "cover_key": null
    }
  ],
  "page": 1,
  "size": 20,
  "total": 1
}
```

Los resultados incluirán únicamente juguetes activos y estarán ordenados por nombre.

### 10.2. Consultar juguete

**Endpoint:** `GET /api/v1/toys/{id}`

**Acceso:** administrador y promotor.

**Respuesta HTTP 200:**

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440002",
  "name": "Bloques de construcción",
  "brand": "EduPlay",
  "reference": "TOY-001",
  "category": {
    "id": "22222222-2222-4222-8222-222222222222",
    "name": "construcción"
  },
  "total_units": 5,
  "available_units": 3,
  "cover_key": null
}
```

Si el juguete no existe o está inactivo, devolverá HTTP 404.

### 10.3. Registrar juguete

**Endpoint:** `POST /api/v1/toys`

**Acceso:** administrador.

**Solicitud:**

```json
{
  "name": "Bloques de construcción",
  "brand": "EduPlay",
  "reference": "TOY-001",
  "category_id": "22222222-2222-4222-8222-222222222222",
  "total_units": 5
}
```

**Respuesta:** HTTP 201 con el objeto del juguete creado.

Al registrar el juguete, `available_units` será igual a `total_units`.

**Validaciones:**

- El nombre será obligatorio.
- La marca será obligatoria.
- La referencia será obligatoria y única entre juguetes activos.
- La categoría deberá existir.
- El total de unidades deberá ser mayor o igual a uno.

**Errores:**

- `403 FORBIDDEN_ROLE`.
- `409 REFERENCE_TAKEN`.
- `422 VALIDATION_ERROR`.

### 10.4. Actualizar juguete

**Endpoint:** `PUT /api/v1/toys/{id}`

**Acceso:** administrador.

**Solicitud:**

```json
{
  "name": "Bloques educativos",
  "brand": "EduPlay",
  "category_id": "22222222-2222-4222-8222-222222222222",
  "total_units": 6
}
```

**Respuesta:** HTTP 200 con el juguete actualizado.

Si cambia la cantidad total, las unidades disponibles se ajustarán según la diferencia, sin permitir inventario negativo.

Los datos históricos de los préstamos no se modificarán.

### 10.5. Dar de baja juguete

**Endpoint:** `DELETE /api/v1/toys/{id}`

**Acceso:** administrador.

**Respuesta exitosa:** HTTP 204.

La operación será una baja lógica mediante `deleted_at`.

Si el juguete tiene préstamos pendientes, devolverá:

`409 TOY_HAS_OPEN_LOANS`

## 11. Imágenes de juguetes

### 11.1. Subir o reemplazar imagen

**Endpoint:** `PUT /api/v1/toys/{id}/cover`

**Acceso:** administrador.

**Content-Type:** `multipart/form-data`

Campo obligatorio:

`file`

Formatos admitidos:

- JPG.
- PNG.

Tamaño máximo: 2 MB.

**Respuesta HTTP 200:**

```json
{
  "cover_key": "toys/example-image.jpg"
}
```

### 11.2. Consultar imagen

**Endpoint:** `GET /api/v1/toys/{id}/cover`

**Acceso:** administrador y promotor.

**Respuesta:** contenido binario de imagen con el tipo MIME correspondiente.

Si no existe una imagen, devolverá HTTP 404.

### 11.3. Eliminar imagen

**Endpoint:** `DELETE /api/v1/toys/{id}/cover`

**Acceso:** administrador.

**Respuesta exitosa:** HTTP 204.

La eliminación de la imagen no afectará el juguete ni los préstamos históricos.

## 12. Gestión de familias

### 12.1. Registrar familia

**Endpoint:** `POST /api/v1/families`

**Acceso:** administrador y promotor.

**Solicitud:**

```json
{
  "full_name": "Familia Rodríguez",
  "document_number": "1234567890"
}
```

**Respuesta HTTP 201:**

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440003",
  "full_name": "Familia Rodríguez",
  "document_number": "1234567890"
}
```

**Errores:**

- `409 DOCUMENT_TAKEN`.
- `422 VALIDATION_ERROR`.

### 12.2. Listar familias

**Endpoint:** `GET /api/v1/families`

**Acceso:** administrador y promotor.

**Parámetros:**

- `q`
- `page`
- `size`

Se permitirá buscar por nombre o documento.

**Respuesta HTTP 200:**

```json
{
  "items": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440003",
      "full_name": "Familia Rodríguez",
      "document_number": "1234567890"
    }
  ],
  "page": 1,
  "size": 20,
  "total": 1
}
```

### 12.3. Consultar familia

**Endpoint:** `GET /api/v1/families/{id}`

**Acceso:** administrador y promotor.

**Respuesta HTTP 200:**

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440003",
  "full_name": "Familia Rodríguez",
  "document_number": "1234567890",
  "active_loans": []
}
```

`active_loans` incluirá las líneas de préstamo pendientes de devolución cuando existan.

Las familias no podrán eliminarse físicamente.

## 13. Gestión de préstamos

### 13.1. Registrar préstamo

**Endpoint:** `POST /api/v1/loans`

**Acceso:** administrador y promotor.

**Solicitud:**

```json
{
  "family_id": "550e8400-e29b-41d4-a716-446655440003",
  "items": [
    {
      "toy_id": "550e8400-e29b-41d4-a716-446655440002"
    }
  ]
}
```

**Respuesta HTTP 201:**

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440004",
  "family_id": "550e8400-e29b-41d4-a716-446655440003",
  "loaned_at": "2026-11-11T15:30:00Z",
  "status": "open",
  "items": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440005",
      "toy_id": "550e8400-e29b-41d4-a716-446655440002",
      "toy_name": "Bloques de construcción",
      "reference": "TOY-001",
      "category_name": "construcción",
      "due_at": "2026-11-20T15:30:00Z",
      "returned_at": null
    }
  ]
}
```

### 13.2. Validaciones del préstamo

El sistema deberá garantizar:

1. La familia existe.
2. El usuario está autenticado.
3. El préstamo contiene entre uno y tres juguetes.
4. No se repite el mismo juguete dentro del préstamo.
5. La familia no supera tres juguetes pendientes en total.
6. La familia no tiene juguetes vencidos pendientes.
7. Existen unidades disponibles.
8. Cada juguete prestado descuenta una unidad.
9. El vencimiento será nueve días después del préstamo.
10. Los nombres, referencias y categorías quedarán congelados.
11. La operación se ejecutará mediante una transacción.

Si alguna validación falla, no se registrará parcialmente el préstamo.

### 13.3. Errores del préstamo

| Condición | HTTP | Código |
|---|---|---|
| Juguete repetido | 422 | `DUPLICATE_TOY_IN_LOAN` |
| Límite de tres superado | 422 | `FAMILY_LIMIT_EXCEEDED` |
| Familia con vencidos | 409 | `FAMILY_HAS_OVERDUE` |
| Sin unidades disponibles | 409 | `TOY_NOT_AVAILABLE` |
| Datos incorrectos | 422 | `VALIDATION_ERROR` |

### 13.4. Regla de concurrencia

Cuando dos promotores intenten prestar simultáneamente la última unidad disponible:

- Una solicitud se completará con HTTP 201.
- La otra recibirá HTTP 409.
- No se permitirá inventario negativo.
- No se registrarán préstamos parciales.

Este comportamiento deberá probarse utilizando PostgreSQL real.

## 14. Consulta de préstamos

### 14.1. Listar préstamos

**Endpoint:** `GET /api/v1/loans`

**Acceso:** administrador y promotor.

**Parámetros opcionales:**

| Parámetro | Descripción |
|---|---|
| `from` | Fecha inicial |
| `to` | Fecha final |
| `status` | `open`, `overdue` o `closed` |
| `family_id` | Familia |
| `page` | Página |
| `size` | Cantidad por página |

Los resultados estarán ordenados desde los préstamos más recientes.

**Respuesta:** objeto paginado con `items`, `page`, `size` y `total`.

### 14.2. Consultar préstamo

**Endpoint:** `GET /api/v1/loans/{id}`

**Acceso:** administrador y promotor.

**Respuesta HTTP 200:**

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440004",
  "family_id": "550e8400-e29b-41d4-a716-446655440003",
  "loaned_at": "2026-11-11T15:30:00Z",
  "status": "open",
  "items": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440005",
      "toy_id": "550e8400-e29b-41d4-a716-446655440002",
      "toy_name": "Bloques de construcción",
      "reference": "TOY-001",
      "category_name": "construcción",
      "due_at": "2026-11-20T15:30:00Z",
      "returned_at": null
    }
  ]
}
```

### 14.3. Estados calculados

Los estados serán:

- `open`: préstamo con juguetes pendientes y sin vencidos.
- `overdue`: préstamo con al menos un juguete vencido pendiente.
- `closed`: préstamo con todas sus líneas devueltas.

Los estados serán calculados y no se almacenarán directamente en la base de datos.

## 15. Consulta de vencidos

**Endpoint:** `GET /api/v1/loans/overdue`

**Acceso:** administrador y promotor.

La API identificará las líneas con:

`returned_at IS NULL`

y

`due_at < current_utc_time`

**Ejemplo de respuesta HTTP 200:**

```json
[
  {
    "loan_id": "550e8400-e29b-41d4-a716-446655440004",
    "loan_item_id": "550e8400-e29b-41d4-a716-446655440005",
    "family_id": "550e8400-e29b-41d4-a716-446655440003",
    "toy_name": "Bloques de construcción",
    "due_at": "2026-11-01T15:30:00Z",
    "days_late": 10
  }
]
```

Los resultados mostrarán los préstamos con mayor retraso primero.

`days_late` será calculado a partir de la fecha actual y no se almacenará como columna.

## 16. Registro de devoluciones

### 16.1. Devolver un juguete

**Endpoint:** `POST /api/v1/loans/{loan_id}/items/{item_id}/return`

**Acceso:** administrador y promotor.

La operación no requiere modificar manualmente la fecha de devolución.

Laravel utilizará la fecha actual en UTC.

**Respuesta HTTP 200:**

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440005",
  "loan_id": "550e8400-e29b-41d4-a716-446655440004",
  "toy_id": "550e8400-e29b-41d4-a716-446655440002",
  "returned_at": "2026-11-15T15:30:00Z"
}
```

### 16.2. Reglas

- La línea deberá pertenecer al préstamo.
- La línea no podrá estar devuelta previamente.
- Se registrará `returned_at`.
- Se aumentará una unidad disponible.
- La operación será transaccional.
- No se eliminará el historial.

Si se intenta devolver dos veces la misma línea:

**HTTP 409**

```json
{
  "code": "ITEM_ALREADY_RETURNED",
  "message": "Este juguete ya fue devuelto.",
  "details": []
}
```

## 17. Reporte de juguetes más solicitados

### 17.1. Consultar reporte

**Endpoint:** `GET /api/v1/reports/most-loaned`

**Acceso:** administrador.

**Parámetros obligatorios:**

- `from`: fecha inicial.
- `to`: fecha final.

Ejemplo:

`GET /api/v1/reports/most-loaned?from=2026-11-01&to=2026-11-30`

**Respuesta HTTP 200:**

```json
{
  "from": "2026-11-01",
  "to": "2026-11-30",
  "rows": [
    {
      "toy_id": "550e8400-e29b-41d4-a716-446655440002",
      "toy_name": "Bloques de construcción",
      "category_name": "construcción",
      "times_loaned": 8
    }
  ]
}
```

### 17.2. Reglas

- El intervalo de fechas será inclusivo.
- Los juguetes se ordenarán de mayor a menor cantidad de préstamos.
- El reporte utilizará la información histórica congelada.
- No se incluirán datos de familias ni promotores.
- Si no existen resultados, `rows` será una lista vacía.
- Si `to` es anterior a `from`, devolverá HTTP 422.
- Solo los administradores podrán acceder al reporte.

### 17.3. Error por fechas inválidas

```json
{
  "code": "INVALID_DATE_RANGE",
  "message": "La fecha final no puede ser anterior a la fecha inicial.",
  "details": []
}
```

## 18. Resumen de endpoints

| Método | Endpoint | Acceso |
|---|---|---|
| POST | `/auth/login` | Público |
| GET | `/health` | Público |
| POST | `/users` | Admin |
| GET | `/categories` | Autenticado |
| GET | `/toys` | Autenticado |
| GET | `/toys/{id}` | Autenticado |
| POST | `/toys` | Admin |
| PUT | `/toys/{id}` | Admin |
| DELETE | `/toys/{id}` | Admin |
| PUT | `/toys/{id}/cover` | Admin |
| GET | `/toys/{id}/cover` | Autenticado |
| DELETE | `/toys/{id}/cover` | Admin |
| GET | `/families` | Admin/Promoter |
| GET | `/families/{id}` | Admin/Promoter |
| POST | `/families` | Admin/Promoter |
| POST | `/loans` | Admin/Promoter |
| GET | `/loans` | Admin/Promoter |
| GET | `/loans/overdue` | Admin/Promoter |
| GET | `/loans/{id}` | Admin/Promoter |
| POST | `/loans/{loan_id}/items/{item_id}/return` | Admin/Promoter |
| GET | `/reports/most-loaned` | Admin |

Todas las rutas anteriores se encuentran bajo `/api/v1`.

## 19. Pruebas del contrato

La implementación deberá incluir pruebas automatizadas para comprobar:

1. Inicio de sesión correcto e incorrecto.
2. Validación de tokens.
3. Restricciones por roles.
4. Registro de promotores.
5. Consulta de categorías.
6. Registro y consulta de juguetes.
7. Actualización y baja lógica.
8. Gestión de imágenes.
9. Registro y consulta de familias.
10. Registro de préstamos.
11. Límite de tres juguetes por familia.
12. Bloqueo por vencimientos.
13. Control del inventario.
14. Registro de devoluciones.
15. Prevención de devoluciones duplicadas.
16. Consulta del historial.
17. Consulta de vencidos.
18. Reporte de juguetes más solicitados.
19. Formato uniforme de errores.
20. Concurrencia sobre la última unidad disponible.

Las pruebas deberán verificar tanto los códigos HTTP como la estructura y contenido de las respuestas.

## 20. Criterios de cumplimiento

El contrato HTTP se considerará implementado cuando:

- Todos los endpoints documentados estén disponibles.
- Las rutas protegidas validen autenticación y permisos.
- Los datos de entrada cumplan las validaciones.
- Los préstamos respeten las reglas de negocio.
- Las devoluciones actualicen correctamente el inventario.
- Las operaciones concurrentes no produzcan inconsistencias.
- Las respuestas de error mantengan el formato uniforme.
- Las fechas estén expresadas en UTC.
- Los mensajes para usuarios estén en español.
- Las pruebas automatizadas demuestren el comportamiento establecido.

## 21. Relación con otros documentos

Este contrato deberá mantenerse alineado con:

- `constitution.md`: principios técnicos.
- `spec.md`: historias de usuario y criterios de aceptación.
- `plan.md`: estrategia de implementación.
- `architecture.md`: componentes y flujos.
- `data-model.md`: estructura de datos.
- `tasks.md`: tareas y evidencias.
- `adr/`: decisiones arquitectónicas.

Cualquier modificación de rutas, campos, permisos o respuestas deberá reflejarse en los documentos relacionados y en las pruebas correspondientes.

---

**Toy Library — Contrato de API REST — Plan de Mejoramiento A13 — Ficha 3239137.**
