# ADR-001 — Gestión de la Base de Datos mediante Migraciones Laravel

**Proyecto:** Toy Library — Juegoteca Comunitaria de Juguetes  
**Identificador:** `toy-library`  
**Código del aprendiz:** A13  
**Ficha:** ADSO 3239137  
**Fecha:** 11 de noviembre de 2026  
**Estado:** Aceptado  
**Categoría:** Persistencia y arquitectura de datos

## 1. Contexto

Toy Library es una aplicación web destinada a gestionar el inventario y los préstamos de juguetes de una juegoteca comunitaria.

El sistema necesita almacenar información de las categorías, juguetes, familias, promotores, préstamos y devoluciones.

La información debe mantenerse organizada y cumplir las restricciones de integridad establecidas en el modelo de datos.

El proyecto utiliza PHP con Laravel para el backend y PostgreSQL como motor de base de datos.

Para garantizar que la estructura de persistencia sea reproducible y esté controlada mediante el código fuente, es necesario definir un mecanismo único de creación y actualización de tablas.

## 2. Problema identificado

Si las tablas se crean manualmente o mediante scripts SQL independientes de Laravel, podrían presentarse las siguientes dificultades:

- Diferencias entre la base de datos local y la utilizada en otros entornos.
- Cambios en tablas sin un historial de versiones.
- Duplicación de configuraciones.
- Problemas al ejecutar el proyecto desde cero.
- Inconsistencias entre el código y la estructura real de PostgreSQL.
- Dificultades para reproducir el entorno durante las pruebas automatizadas.

Por estas razones, se requiere un mecanismo controlado para gestionar la estructura de la base de datos.

## 3. Decisión arquitectónica

Se decide utilizar **migraciones de Laravel como único mecanismo para crear y modificar el esquema de negocio de PostgreSQL**.

Las migraciones estarán ubicadas en:

`api/database/migrations/`

Las tablas, relaciones, restricciones e índices serán definidos mediante estas migraciones.

No se utilizarán scripts SQL independientes para crear o modificar las tablas del dominio fuera del proceso de migraciones.

Cuando una característica de PostgreSQL requiera SQL específico, podrá ejecutarse mediante las propias migraciones Laravel.

## 4. Tablas del sistema

Las migraciones deberán crear seis tablas principales:

| Tabla | Responsabilidad |
|---|---|
| `categories` | Almacenar categorías de juguetes |
| `toys` | Registrar información e inventario de juguetes |
| `families` | Registrar familias beneficiarias |
| `promoters` | Registrar usuarios y roles |
| `loans` | Registrar préstamos realizados |
| `loan_items` | Registrar juguetes prestados y devoluciones |

Cada tabla utilizará una clave primaria `id` de tipo UUID.

Las relaciones estarán definidas mediante claves foráneas.

## 5. Restricciones obligatorias

### 5.1. Integridad del inventario

La tabla `toys` deberá garantizar:

`total_units >= 1`

`available_units >= 0`

`available_units <= total_units`

Estas condiciones deberán implementarse mediante restricciones `CHECK` en PostgreSQL.

### 5.2. Referencias únicas

La referencia de cada juguete deberá ser única entre los juguetes activos.

Se utilizará un índice único parcial que considere únicamente los registros cuyo campo `deleted_at` sea nulo.

Esto permitirá conservar el historial de juguetes retirados sin afectar la unicidad del catálogo activo.

### 5.3. Documentos de familias

El campo `document_number` de la tabla `families` deberá tener una restricción de unicidad.

No se permitirá registrar dos familias con el mismo documento.

### 5.4. Usuarios

El campo `username` de la tabla `promoters` será único.

Los roles permitidos serán:

- `admin`
- `promoter`

La base de datos deberá rechazar valores distintos mediante una restricción.

### 5.5. Líneas de préstamo

Cada préstamo podrá contener un juguete determinado una sola vez.

Se implementará una restricción única sobre:

`loan_id, toy_id`

Las claves foráneas impedirán crear líneas relacionadas con préstamos o juguetes inexistentes.

### 5.6. Conservación del historial

No se utilizará eliminación en cascada para borrar préstamos ni sus líneas.

Los préstamos y devoluciones se conservarán como registros históricos.

Los juguetes se retirarán mediante baja lógica.

## 6. Datos iniciales

Toy Library requiere cinco categorías iniciales:

1. Bebés.
2. Construcción.
3. Didácticos.
4. Exterior.
5. Simbólicos.

Estas categorías se registrarán durante la inicialización del esquema mediante una migración Laravel.

Se utilizarán identificadores UUID fijos para obtener resultados reproducibles.

La operación deberá evitar registros duplicados cuando sea ejecutada nuevamente.

Los datos de demostración, como juguetes, familias y préstamos, serán creados mediante el sembrador que utiliza la API.

## 7. Estrategia de implementación

### 7.1. Creación de migraciones

Se crearán migraciones para definir las seis tablas y sus restricciones.

El orden de creación deberá respetar las relaciones entre entidades:

1. `categories`
2. `families`
3. `promoters`
4. `toys`
5. `loans`
6. `loan_items`

### 7.2. Aplicación del esquema

Las migraciones se ejecutarán mediante:

```bash
php artisan migrate
```

Para consultar su estado:

```bash
php artisan migrate:status
```

### 7.3. Cambios posteriores

Cuando sea necesario modificar una tabla, se creará una nueva migración.

No se modificarán migraciones ya aplicadas en entornos compartidos.

Los cambios deberán actualizar también `data-model.md` y las pruebas correspondientes.

### 7.4. Control de versiones

Las migraciones formarán parte del repositorio Git.

Cada cambio deberá utilizar commits pequeños y descriptivos.

Las migraciones no incluirán contraseñas, credenciales ni información privada.

## 8. Alternativas consideradas

### Alternativa 1. Crear tablas manualmente en PostgreSQL

**Ventajas:**

- Permite configurar directamente las tablas.
- Facilita ejecutar consultas SQL específicas.

**Desventajas:**

- No mantiene un historial de cambios integrado con Laravel.
- Puede generar diferencias entre entornos.
- Requiere procedimientos manuales.

**Resultado:** descartada.

### Alternativa 2. Utilizar scripts SQL independientes

**Ventajas:**

- Permite controlar instrucciones específicas de PostgreSQL.
- Puede ejecutarse mediante herramientas de base de datos.

**Desventajas:**

- Mantiene la estructura fuera del sistema de migraciones.
- Puede duplicar responsabilidades.
- Dificulta coordinar cambios con el código Laravel.

**Resultado:** descartada como mecanismo independiente de administración del esquema.

### Alternativa 3. Utilizar migraciones Laravel

**Ventajas:**

- Mantiene el historial de cambios del esquema.
- Facilita la ejecución en diferentes entornos.
- Permite utilizar herramientas propias de Laravel.
- Facilita la integración con las pruebas automatizadas.
- Permite incluir instrucciones SQL específicas cuando sean necesarias.

**Desventajas:**

- Algunas restricciones avanzadas requieren SQL propio de PostgreSQL.
- Es necesario controlar el orden de las migraciones.
- Los cambios en entornos con información existente deben planificarse cuidadosamente.

**Resultado:** alternativa seleccionada.

## 9. Consecuencias de la decisión

### 9.1. Consecuencias positivas

- La estructura de la base de datos será reproducible.
- Las modificaciones quedarán registradas en Git.
- Se reducirá el riesgo de diferencias entre entornos.
- Las restricciones podrán verificarse mediante pruebas.
- La base de datos podrá inicializarse durante el despliegue con Docker.

### 9.2. Consecuencias negativas

- Será necesario crear y mantener las migraciones.
- Algunas características de PostgreSQL requerirán instrucciones SQL específicas.
- Los cambios estructurales deberán coordinarse con la implementación y documentación.

### 9.3. Compromisos adquiridos

El desarrollo deberá:

- Mantener las migraciones actualizadas.
- Evitar crear tablas manualmente.
- Conservar las seis tablas de negocio definidas.
- Implementar las restricciones de integridad.
- Ejecutar pruebas de persistencia.
- Mantener actualizado `data-model.md`.

## 10. Verificación de la decisión

Para comprobar que esta decisión fue implementada correctamente se ejecutarán las siguientes verificaciones.

### 10.1. Existencia de las seis tablas

```sql
SELECT table_name
FROM information_schema.tables
WHERE table_schema = 'public'
AND table_name IN (
    'categories',
    'toys',
    'families',
    'promoters',
    'loans',
    'loan_items'
)
ORDER BY table_name;
```

**Resultado esperado:** seis tablas.

### 10.2. Existencia de las categorías

```sql
SELECT name
FROM categories
ORDER BY name;
```

**Resultado esperado:** cinco categorías.

### 10.3. Integridad del inventario

```sql
SELECT id, total_units, available_units
FROM toys
WHERE total_units < 1
   OR available_units < 0
   OR available_units > total_units;
```

**Resultado esperado:** cero filas.

### 10.4. Columnas con valores predeterminados

```sql
SELECT table_name, column_name, column_default
FROM information_schema.columns
WHERE table_schema = 'public'
AND table_name IN (
    'categories',
    'toys',
    'families',
    'promoters',
    'loans',
    'loan_items'
)
AND column_default IS NOT NULL;
```

**Resultado esperado:** cero filas.

### 10.5. Ejecución de migraciones

Se comprobará que:

- Las migraciones se ejecuten correctamente.
- Las relaciones se creen sin errores.
- Las restricciones funcionen.
- Las categorías iniciales queden registradas.
- Las pruebas de integración se ejecuten utilizando PostgreSQL.

## 11. Relación con otros documentos

Este ADR deberá mantenerse alineado con:

- `constitution.md`: principios de persistencia.
- `spec.md`: reglas de negocio.
- `plan.md`: estrategia de implementación.
- `architecture.md`: arquitectura de persistencia.
- `data-model.md`: tablas, atributos y restricciones.
- `api-contract.md`: contratos relacionados con los datos.
- `tasks.md`: tareas de migración y persistencia.

## 12. Estado y seguimiento

**Estado de la decisión:** Aceptado.

La decisión se considera adoptada para el diseño del sistema.

Su implementación deberá verificarse durante el desarrollo de las migraciones y la ejecución de las pruebas.

La aceptación de este ADR no significa que las migraciones ya estén implementadas.

---

**ADR-001 — Gestión de la Base de Datos mediante Migraciones Laravel — Toy Library A13.**
