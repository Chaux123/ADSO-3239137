# ADR-003 — Conservación de Datos Históricos mediante Snapshots

**Proyecto:** Toy Library — Juegoteca Comunitaria de Juguetes  
**Identificador:** `toy-library`  
**Código del aprendiz:** A13  
**Ficha:** ADSO 3239137  
**Fecha:** 11 de noviembre de 2026  
**Estado:** Aceptado  
**Categoría:** Persistencia e integridad histórica

## 1. Contexto

Toy Library es una aplicación web para administrar juguetes, familias, promotores, préstamos y devoluciones de una juegoteca comunitaria.

Cada juguete pertenece a una categoría y contiene información como nombre, marca, referencia y cantidad de unidades.

Durante el funcionamiento del sistema, los administradores pueden actualizar la información del catálogo.

Sin embargo, los préstamos realizados anteriormente deben conservar los datos correspondientes al momento en que se efectuó cada operación.

El Acta No. 13 establece que la información congelada en una línea de préstamo no debe cambiar cuando posteriormente se modifica el catálogo.

Para cumplir este requisito es necesario definir una estrategia de conservación de datos históricos.

## 2. Problema identificado

Si los préstamos históricos muestran únicamente los datos actuales de la tabla `toys`, los cambios realizados sobre un juguete pueden modificar indirectamente la información presentada en operaciones anteriores.

Por ejemplo:

1. Un juguete está registrado como "Bloques de construcción".
2. Una familia recibe ese juguete mediante un préstamo.
3. El administrador cambia su nombre a "Bloques educativos".
4. El sistema consulta el préstamo anterior.
5. Si únicamente utiliza los datos actuales del catálogo, mostrará incorrectamente el nuevo nombre.

Esto genera inconsistencias históricas porque el registro deja de representar la información existente cuando se realizó el préstamo.

El mismo problema puede presentarse si cambia la referencia o la categoría del juguete.

## 3. Decisión arquitectónica

Se decide utilizar **snapshots de datos históricos en cada línea de préstamo**.

Un snapshot es una copia de determinados datos de un recurso en el momento en que se registra una operación.

En Toy Library, cada registro de `loan_items` conservará una copia del nombre, la referencia y la categoría del juguete.

Esta información será independiente de los cambios posteriores en la tabla `toys`.

### 3.1. Campos congelados

Los siguientes campos almacenarán la información histórica:

| Campo | Tipo | Descripción |
|---|---|---|
| `toy_name` | VARCHAR(250) | Nombre del juguete al momento del préstamo |
| `reference` | VARCHAR(50) | Referencia al momento del préstamo |
| `category_name` | VARCHAR(120) | Categoría al momento del préstamo |

Estos campos estarán ubicados en la tabla `loan_items`.

### 3.2. Referencia al juguete original

Cada línea también conservará:

`toy_id`

Este campo será una clave foránea hacia `toys(id)`.

La relación permitirá identificar el juguete original, mientras que los campos congelados conservarán su información histórica.

## 4. Funcionamiento de los snapshots

### 4.1. Registro del préstamo

Cuando un promotor registre un préstamo:

1. El sistema verificará que el juguete exista y esté activo.
2. Comprobará que exista disponibilidad.
3. Obtendrá el nombre, la referencia y la categoría actuales.
4. Creará el préstamo correspondiente.
5. Registrará una línea en `loan_items`.
6. Copiará los datos del catálogo en los campos históricos.
7. Descontará una unidad disponible.
8. Confirmará la transacción.

La copia de los datos históricos deberá realizarse dentro de la misma transacción que registra el préstamo.

### 4.2. Consulta del historial

Cuando se consulte un préstamo anterior, el sistema utilizará los campos congelados de `loan_items`.

No utilizará el nombre ni la categoría actual del catálogo para reemplazar esos valores históricos.

### 4.3. Actualización del catálogo

Cuando un administrador modifique un juguete:

- Se actualizarán los campos correspondientes de `toys`.
- No se modificarán los snapshots existentes.
- Los préstamos anteriores conservarán su información.
- Los nuevos préstamos utilizarán los datos actualizados del catálogo.

## 5. Ejemplo de conservación histórica

### 5.1. Estado inicial del catálogo

| Campo | Valor |
|---|---|
| `name` | Bloques de construcción |
| `reference` | TOY-001 |
| `category` | Construcción |
| `total_units` | 5 |
| `available_units` | 5 |

### 5.2. Registro del préstamo

Una familia recibe una unidad del juguete.

La línea del préstamo almacena:

```json
{
  "toy_id": "550e8400-e29b-41d4-a716-446655440002",
  "toy_name": "Bloques de construcción",
  "reference": "TOY-001",
  "category_name": "construcción"
}
```

### 5.3. Actualización posterior

El administrador cambia el nombre del juguete a:

"Bloques educativos".

El catálogo mostrará el nombre actualizado.

Sin embargo, el préstamo anterior seguirá mostrando:

```json
{
  "toy_name": "Bloques de construcción",
  "reference": "TOY-001",
  "category_name": "construcción"
}
```

### 5.4. Resultado esperado

Los datos históricos permanecerán iguales.

Un préstamo nuevo podrá conservar el nombre actualizado, mientras que el préstamo anterior conservará el nombre original.

## 6. Diseño de persistencia

La tabla `loan_items` tendrá los siguientes campos relacionados con los snapshots:

| Campo | Tipo | Restricción |
|---|---|---|
| `id` | UUID | PK, NOT NULL |
| `loan_id` | UUID | FK, NOT NULL |
| `toy_id` | UUID | FK, NOT NULL |
| `toy_name` | VARCHAR(250) | NOT NULL |
| `reference` | VARCHAR(50) | NOT NULL |
| `category_name` | VARCHAR(120) | NOT NULL |
| `due_at` | TIMESTAMPTZ | NOT NULL |
| `returned_at` | TIMESTAMPTZ | NULL |

Los datos históricos deberán registrarse durante la creación de la línea.

La actualización de `returned_at` será permitida únicamente para registrar la devolución correspondiente.

Los campos congelados no deberán modificarse después de crear el préstamo.

## 7. Estrategia de implementación

### 7.1. Capa de dominio

El dominio establecerá qué datos deben conservarse cuando se realiza un préstamo.

La información histórica formará parte de los datos de la línea de préstamo.

Las reglas deberán impedir modificaciones posteriores de esos valores.

### 7.2. Capa de aplicación

El servicio encargado de registrar préstamos deberá:

1. Validar la familia.
2. Comprobar las reglas de préstamo.
3. Obtener los juguetes solicitados.
4. Verificar la disponibilidad.
5. Capturar los datos históricos.
6. Crear el préstamo y sus líneas.
7. Actualizar el inventario.
8. Confirmar la transacción.

### 7.3. Capa de infraestructura

La infraestructura persistirá los snapshots en PostgreSQL.

Los repositorios deberán conservar los campos históricos y evitar actualizaciones indebidas.

### 7.4. Capa HTTP

Los endpoints de consulta de préstamos utilizarán los campos históricos de `loan_items`.

Las respuestas JSON mantendrán los nombres de campos definidos en `api-contract.md`.

## 8. Ejemplo de implementación en Laravel

El siguiente pseudocódigo representa la creación de una línea con datos históricos:

```php
$loanItem = LoanItemModel::create([
    'id' => (string) Str::uuid(),
    'loan_id' => $loan->id,
    'toy_id' => $toy->id,
    'toy_name' => $toy->name,
    'reference' => $toy->reference,
    'category_name' => $toy->category->name,
    'due_at' => $dueAt,
    'returned_at' => null,
]);
```

Este fragmento es ilustrativo.

La implementación deberá ejecutarse dentro de la transacción de préstamo y utilizar los repositorios o servicios definidos en `architecture.md`.

La categoría deberá obtenerse de forma consistente con la información bloqueada o validada durante la operación.

## 9. Integridad de los datos históricos

Los snapshots deberán cumplir las siguientes reglas:

- Los campos históricos serán obligatorios.
- Se copiarán al registrar cada línea.
- No se modificarán mediante operaciones de actualización del catálogo.
- No se reemplazarán con datos actuales durante las consultas.
- No podrán eliminarse junto con los préstamos.
- La devolución únicamente modificará los datos permitidos de la operación.

La implementación deberá controlar la inmutabilidad de los campos congelados.

Si se requiere protección adicional en PostgreSQL, podrán implementarse restricciones o mecanismos que impidan modificaciones directas no autorizadas.

## 10. Relación con la baja lógica

Toy Library utiliza baja lógica para retirar juguetes del catálogo.

La tabla `toys` tendrá el campo:

`deleted_at`

Cuando se retire un juguete:

- Se establecerá la fecha de baja.
- El juguete dejará de aparecer en el catálogo activo.
- No se eliminará físicamente su registro.
- Los préstamos anteriores conservarán su relación con el juguete.
- Los snapshots permanecerán disponibles.

No se permitirá dar de baja un juguete que tenga préstamos pendientes.

## 11. Uso de snapshots en reportes

El sistema deberá generar el reporte de juguetes más solicitados utilizando el historial de préstamos.

Para preservar la información histórica, utilizará los datos congelados de las líneas.

### 11.1. Datos del reporte

El reporte mostrará:

- Identificador del juguete.
- Nombre histórico.
- Categoría histórica.
- Cantidad de préstamos.

### 11.2. Agrupación

El reporte contabilizará las líneas correspondientes a los juguetes solicitados dentro del intervalo de fechas.

La agrupación deberá identificar correctamente al juguete y respetar la información histórica que se presente.

Si un mismo juguete tuvo cambios de nombre o categoría dentro del período, el reporte deberá aplicar una regla de representación histórica coherente con el contrato de API.

No se permitirá que una modificación posterior del catálogo cambie los resultados de un período histórico cerrado.

## 12. Alternativas consideradas

### Alternativa 1. Consultar siempre la información actual del catálogo

Consiste en guardar solamente `toy_id` en las líneas y consultar los datos actuales del juguete.

**Ventajas:**

- Menor duplicación de datos.
- Modelo más sencillo.

**Desventajas:**

- Los cambios del catálogo alteran la información presentada en préstamos anteriores.
- No se conserva la información original de cada operación.
- Incumple el requisito de historial congelado.

**Resultado:** descartada.

### Alternativa 2. Crear una tabla de versiones del catálogo

Consiste en guardar distintas versiones de los juguetes y relacionar cada préstamo con una versión específica.

**Ventajas:**

- Permite consultar diferentes versiones.
- Conserva un historial detallado del catálogo.

**Desventajas:**

- Aumenta la complejidad del modelo.
- Requiere tablas y relaciones adicionales.
- Añade funcionalidades que no son necesarias para el alcance del proyecto.

**Resultado:** descartada.

### Alternativa 3. Guardar snapshots en las líneas de préstamo

Consiste en copiar los datos necesarios del juguete al momento de registrar cada línea.

**Ventajas:**

- Conserva la información histórica.
- Evita depender de cambios posteriores del catálogo.
- Facilita la consulta de préstamos antiguos.
- No requiere crear tablas adicionales.
- Se integra con el modelo de seis tablas del proyecto.

**Desventajas:**

- Duplica algunos datos del catálogo.
- Requiere controlar la inmutabilidad de los campos históricos.
- Necesita mantener consistencia al crear cada préstamo.

**Resultado:** alternativa seleccionada.

## 13. Consecuencias de la decisión

### 13.1. Consecuencias positivas

- Los préstamos conservan la información original de los juguetes.
- Los cambios del catálogo no alteran operaciones anteriores.
- Se facilita la consulta del historial.
- Los reportes pueden utilizar datos históricos.
- Se cumple la regla establecida en el Acta No. 13.

### 13.2. Consecuencias negativas

- Se almacena información duplicada.
- La creación de préstamos requiere copiar los datos correctamente.
- Se deben proteger los campos históricos frente a actualizaciones indebidas.

### 13.3. Compromisos adquiridos

- Guardar los snapshots durante la creación de cada préstamo.
- No modificar los campos históricos después de registrarlos.
- Utilizar snapshots en consultas históricas.
- Mantener la relación con el juguete original.
- Implementar pruebas de conservación histórica.
- Mantener coherencia con el contrato HTTP.

## 14. Estrategia de pruebas

Se deberán implementar pruebas que comprueben la correcta conservación de los datos.

### 14.1. Prueba de creación del snapshot

**Preparación:**

Crear un juguete con nombre, referencia y categoría.

**Ejecución:**

Registrar un préstamo del juguete.

**Resultado esperado:**

La línea conservará los tres valores originales.

### 14.2. Prueba de actualización del nombre

**Preparación:**

Registrar un préstamo de un juguete.

**Ejecución:**

Modificar el nombre del juguete en el catálogo.

**Resultado esperado:**

El préstamo anterior conservará el nombre original.

### 14.3. Prueba de actualización de categoría

**Preparación:**

Registrar un préstamo de un juguete.

**Ejecución:**

Modificar la categoría del juguete.

**Resultado esperado:**

La línea anterior conservará la categoría original.

### 14.4. Prueba de baja lógica

**Preparación:**

Crear un juguete y registrar un préstamo histórico completamente devuelto.

**Ejecución:**

Dar de baja el juguete.

**Resultado esperado:**

- El juguete dejará de aparecer en el catálogo activo.
- El préstamo histórico seguirá disponible.
- Sus datos congelados no cambiarán.

### 14.5. Prueba de devolución

**Preparación:**

Crear un préstamo con una línea pendiente.

**Ejecución:**

Registrar la devolución.

**Resultado esperado:**

- Se actualizará `returned_at`.
- Los datos congelados permanecerán iguales.
- El historial continuará disponible.

### 14.6. Prueba de reportes históricos

**Preparación:**

Crear préstamos de un juguete y modificar posteriormente su información en el catálogo.

**Ejecución:**

Consultar el reporte de un período anterior.

**Resultado esperado:**

Los resultados históricos no cambiarán por las modificaciones posteriores del catálogo.

## 15. Criterios de verificación

Esta decisión se considerará implementada cuando:

- [ ] La tabla `loan_items` contenga los campos del snapshot.
- [ ] Cada préstamo registre nombre, referencia y categoría históricos.
- [ ] Los snapshots se creen dentro de la transacción de préstamo.
- [ ] Las actualizaciones del catálogo no modifiquen los snapshots.
- [ ] Las devoluciones mantengan los datos históricos.
- [ ] La baja lógica conserve los préstamos anteriores.
- [ ] Las consultas de préstamos utilicen los campos congelados.
- [ ] Los reportes respeten la información histórica.
- [ ] Las pruebas automatizadas demuestren la inmutabilidad.

## 16. Relación con otros documentos

Este ADR deberá mantenerse alineado con:

- `constitution.md`: principio de conservación del historial.
- `spec.md`: requisitos de préstamos y datos congelados.
- `plan.md`: estrategia de implementación.
- `architecture.md`: estructura de componentes y flujos.
- `data-model.md`: campos de la tabla `loan_items`.
- `api-contract.md`: respuestas históricas de préstamos y reportes.
- `tasks.md`: tareas de dominio, persistencia y reportes.
- `adr-001-migrations.md`: migraciones y estructura de datos.
- `adr-002-concurrency.md`: transacciones de préstamos.

## 17. Estado y seguimiento

**Estado de la decisión:** Aceptado.

Se adopta el almacenamiento de snapshots en `loan_items` como estrategia de conservación histórica.

Su implementación deberá comprobarse mediante pruebas automatizadas y consultas al historial.

La aceptación de este ADR no significa que la funcionalidad ya esté implementada ni verificada.

---

**ADR-003 — Conservación de Datos Históricos mediante Snapshots — Toy Library A13 — Ficha 3239137.**
