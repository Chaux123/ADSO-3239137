# Especificación del Producto — Toy Library

**Proyecto:** Toy Library — Juegoteca Comunitaria de Juguetes  
**Identificador:** `toy-library`  
**Programa:** Análisis y Desarrollo de Software (ADSO)  
**Ficha:** 3239137  
**Aprendiz:** A13  
**Versión:** 1.0  
**Estado:** Especificación inicial

## 1. Propósito del proyecto

Toy Library es una aplicación web diseñada para administrar los juguetes disponibles en una juegoteca comunitaria y controlar los préstamos realizados a las familias.

El sistema permitirá registrar juguetes, consultar su disponibilidad, gestionar familias y promotores, realizar préstamos, registrar devoluciones y generar reportes.

El objetivo es facilitar la administración de los juguetes y garantizar que cada operación cumpla las reglas establecidas, evitando préstamos indebidos, pérdidas de información e inconsistencias en el inventario.

El proyecto se desarrollará con React para el frontend, PHP con Laravel para el backend y PostgreSQL para la persistencia de los datos.

## 2. Actores del sistema

El sistema contará con los siguientes actores:

### 2.1. Administrador (`admin`)

Es el usuario responsable de administrar los recursos y usuarios del sistema.

Sus funciones son:

- Registrar promotores.
- Crear y actualizar juguetes.
- Gestionar las categorías disponibles.
- Realizar la baja lógica de juguetes.
- Consultar familias y préstamos.
- Registrar préstamos y devoluciones.
- Consultar reportes de los juguetes más solicitados.

El administrador se configurará inicialmente mediante variables de entorno y no podrá crearse mediante la API.

### 2.2. Promotor (`promoter`)

Es el usuario encargado de atender a las familias y realizar las operaciones de préstamo y devolución.

Sus funciones son:

- Consultar juguetes.
- Verificar disponibilidad.
- Registrar familias.
- Consultar información de las familias.
- Registrar préstamos.
- Registrar devoluciones.
- Consultar préstamos activos y vencidos.

No tendrá permisos para administrar promotores, modificar el catálogo ni consultar reportes administrativos.

### 2.3. Familia (`family`)

Representa a las familias beneficiarias del servicio comunitario.

Cada familia tendrá información de identificación y un historial de los juguetes recibidos.

Las familias no iniciarán sesión en el sistema. Sus operaciones serán registradas por los usuarios autorizados.

## 3. Alcance del sistema

### 3.1. Funcionalidades incluidas

El sistema deberá implementar:

1. Autenticación mediante credenciales.
2. Administración de promotores.
3. Gestión del catálogo de juguetes.
4. Búsqueda y consulta de juguetes.
5. Registro y consulta de familias.
6. Registro de préstamos.
7. Registro de devoluciones.
8. Consulta del historial de préstamos.
9. Identificación de préstamos vencidos.
10. Reporte de juguetes más solicitados.

También incluirá cinco categorías iniciales:

- Bebés.
- Construcción.
- Didácticos.
- Exterior.
- Simbólicos.

Los juguetes podrán tener una fotografía opcional.

### 3.2. Funcionalidades excluidas

No se desarrollarán funcionalidades que estén fuera de la especificación, tales como:

- Pagos.
- Multas económicas.
- Reservas de juguetes.
- Renovaciones de préstamos.
- Notificaciones automáticas.
- Importación masiva de información.
- Administración de múltiples sedes.

## 4. Historias de usuario y criterios de aceptación

### HU-01. Autenticación de usuarios

**Como** administrador o promotor,  
**quiero** iniciar sesión con mis credenciales,  
**para** acceder únicamente a las funcionalidades permitidas según mi rol.

**Criterios de aceptación:**

- CA-01.1: El sistema permitirá iniciar sesión mediante nombre de usuario y contraseña.
- CA-01.2: Las credenciales correctas generarán un token JWT con duración de ocho horas e información del rol.
- CA-01.3: Las credenciales incorrectas producirán una respuesta HTTP 401 sin revelar cuál de los datos es incorrecto.
- CA-01.4: Los endpoints protegidos requerirán un token válido.
- CA-01.5: Los usuarios sin permisos recibirán HTTP 403.
- CA-01.6: El inicio de sesión y el endpoint `/health` serán públicos.

### HU-02. Registro de promotores

**Como** administrador,  
**quiero** registrar promotores,  
**para** permitir que el personal autorizado gestione los préstamos y las devoluciones.

**Criterios de aceptación:**

- CA-02.1: El administrador podrá registrar un promotor con nombre de usuario y contraseña.
- CA-02.2: Los nombres de usuario deberán normalizarse a minúsculas y eliminar espacios al inicio y al final.
- CA-02.3: No podrán existir dos promotores con el mismo nombre de usuario.
- CA-02.4: Un usuario repetido generará HTTP 409.
- CA-02.5: No se permitirá crear administradores mediante la API.
- CA-02.6: Las contraseñas y sus hashes no aparecerán en las respuestas del sistema.

### HU-03. Gestión del catálogo de juguetes

**Como** administrador,  
**quiero** registrar y actualizar los juguetes,  
**para** mantener actualizado el inventario de la juegoteca.

**Criterios de aceptación:**

- CA-03.1: Cada juguete tendrá nombre, marca, referencia, categoría y cantidad total de unidades.
- CA-03.2: Al registrar un juguete, las unidades disponibles serán iguales a las unidades totales.
- CA-03.3: La referencia de cada juguete deberá ser única entre los juguetes activos.
- CA-03.4: Las referencias duplicadas generarán HTTP 409.
- CA-03.5: La cantidad total de unidades deberá ser mayor o igual a uno.
- CA-03.6: Al modificar el total de unidades se ajustará la disponibilidad según la diferencia.
- CA-03.7: No se permitirá una modificación que produzca inventario negativo.
- CA-03.8: Se permitirá asociar una imagen JPG o PNG de máximo 2 MB.
- CA-03.9: Los juguetes se retirarán mediante baja lógica.
- CA-03.10: No se permitirá dar de baja juguetes con préstamos pendientes de devolución.

### HU-04. Búsqueda y consulta de juguetes

**Como** administrador o promotor,  
**quiero** buscar juguetes disponibles,  
**para** consultar sus características y disponibilidad antes de realizar un préstamo.

**Criterios de aceptación:**

- CA-04.1: El sistema permitirá buscar juguetes mediante coincidencias parciales por nombre o marca.
- CA-04.2: Se podrán filtrar los juguetes por categoría.
- CA-04.3: Las consultas mostrarán únicamente juguetes activos.
- CA-04.4: Los resultados estarán ordenados por nombre.
- CA-04.5: Se utilizará paginación con página inicial 1, tamaño predeterminado de 20 y máximo de 30 registros por página.
- CA-04.6: La respuesta incluirá la cantidad total de registros encontrados.
- CA-04.7: Consultar un juguete inexistente o inactivo devolverá HTTP 404.

### HU-05. Gestión de familias

**Como** administrador o promotor,  
**quiero** registrar y consultar familias,  
**para** identificar a los beneficiarios y controlar los juguetes que reciben.

**Criterios de aceptación:**

- CA-05.1: Cada familia tendrá nombre completo y número de documento.
- CA-05.2: El documento será único.
- CA-05.3: Intentar registrar un documento duplicado devolverá HTTP 409.
- CA-05.4: Se permitirá buscar familias por nombre o documento.
- CA-05.5: Los resultados de búsqueda serán paginados.
- CA-05.6: Se podrá consultar el detalle de una familia y sus líneas de préstamo pendientes.
- CA-05.7: Los registros de familias no podrán eliminarse.

### HU-06. Registro de préstamos

**Como** promotor o administrador,  
**quiero** registrar los juguetes entregados a una familia,  
**para** controlar el inventario y las fechas de devolución.

**Criterios de aceptación:**

- CA-06.1: Cada préstamo estará asociado a una familia y al usuario autenticado que realiza la operación.
- CA-06.2: Un préstamo tendrá entre una y tres líneas de juguetes.
- CA-06.3: No se permitirá repetir el mismo juguete dentro de un préstamo.
- CA-06.4: Cada línea de préstamo descontará una unidad disponible.
- CA-06.5: Una familia no podrá tener más de tres juguetes pendientes de devolución, considerando todos sus préstamos.
- CA-06.6: Si una familia tiene juguetes vencidos, no podrá recibir nuevos préstamos hasta devolverlos.
- CA-06.7: El plazo de devolución será de nueve días a partir de la fecha del préstamo.
- CA-06.8: Cada línea conservará el nombre, referencia y categoría del juguete en el momento del préstamo.
- CA-06.9: Un préstamo registrado no podrá modificarse ni eliminarse.
- CA-06.10: Si no existe disponibilidad suficiente, el sistema devolverá HTTP 409 y no guardará parcialmente la operación.
- CA-06.11: Superar el límite de tres juguetes generará HTTP 422 e informará el conteo correspondiente.
- CA-06.12: Las operaciones deberán garantizar consistencia incluso ante solicitudes simultáneas.

### HU-07. Registro de devoluciones

**Como** administrador o promotor,  
**quiero** registrar la devolución de un juguete,  
**para** actualizar las existencias disponibles y conservar el historial.

**Criterios de aceptación:**

- CA-07.1: La devolución se registrará por cada línea de préstamo.
- CA-07.2: Al registrar una devolución se almacenará su fecha.
- CA-07.3: Cada devolución incrementará en una unidad el inventario disponible.
- CA-07.4: No se permitirá devolver dos veces la misma línea.
- CA-07.5: Una devolución duplicada devolverá HTTP 409.
- CA-07.6: Las unidades disponibles nunca podrán superar el total.
- CA-07.7: Un préstamo tendrá estado `closed` cuando todas sus líneas hayan sido devueltas.
- CA-07.8: Los registros de devolución no podrán eliminarse.

### HU-08. Consulta del historial de préstamos

**Como** administrador o promotor,  
**quiero** consultar los préstamos registrados,  
**para** revisar el historial de las operaciones de la juegoteca.

**Criterios de aceptación:**

- CA-08.1: Se podrá consultar el detalle de un préstamo junto con sus juguetes.
- CA-08.2: Cada préstamo mostrará su estado calculado.
- CA-08.3: Los estados posibles serán `open`, `overdue` y `closed`.
- CA-08.4: Se permitirá filtrar por fechas, estado y familia.
- CA-08.5: Los resultados se mostrarán desde los préstamos más recientes.
- CA-08.6: Las consultas tendrán paginación.
- CA-08.7: El estado del préstamo no se almacenará como un campo permanente.

### HU-09. Consulta de préstamos vencidos

**Como** administrador o promotor,  
**quiero** identificar los juguetes cuya devolución está vencida,  
**para** realizar seguimiento a los préstamos pendientes.

**Criterios de aceptación:**

- CA-09.1: Se considerará vencida una línea que no tenga devolución y cuya fecha de vencimiento sea anterior a la fecha actual.
- CA-09.2: El sistema mostrará el juguete, la familia y la cantidad de días de retraso.
- CA-09.3: Los registros se organizarán desde el mayor retraso hasta el menor.
- CA-09.4: Solamente administradores y promotores autenticados podrán consultar esta información.

### HU-10. Reporte de juguetes más solicitados

**Como** administrador,  
**quiero** consultar un reporte de los juguetes más solicitados,  
**para** conocer cuáles tienen mayor demanda en la juegoteca.

**Criterios de aceptación:**

- CA-10.1: El sistema permitirá consultar un reporte por intervalo de fechas inclusivo.
- CA-10.2: El reporte mostrará identificador, nombre, categoría y cantidad de préstamos de cada juguete.
- CA-10.3: Los resultados se ordenarán por cantidad de préstamos de mayor a menor.
- CA-10.4: Los nombres y categorías utilizados corresponderán a la información congelada en el momento de cada préstamo.
- CA-10.5: Si la fecha final es anterior a la inicial, se devolverá HTTP 422.
- CA-10.6: Si no existen préstamos en el intervalo consultado, se devolverá una lista vacía.
- CA-10.7: El reporte no incluirá información de familias ni promotores.
- CA-10.8: Solamente el administrador podrá consultar el reporte.
- CA-10.9: Los cambios posteriores al catálogo no modificarán los resultados históricos de intervalos cerrados.

## 5. Reglas de negocio

### RN-01. Integridad del inventario

Las unidades disponibles de cada juguete deberán mantenerse siempre dentro del siguiente rango:

`0 <= available_units <= total_units`

No se permitirán préstamos ni actualizaciones que incumplan esta condición.

### RN-02. Límite y duración de los préstamos

Una familia podrá tener máximo tres juguetes pendientes de devolución.

Cada préstamo tendrá un plazo fijo de nueve días.

El límite se aplicará al total de juguetes no devueltos, aunque pertenezcan a distintos préstamos.

### RN-03. Restricción por préstamos vencidos

Si una familia tiene al menos un juguete vencido y pendiente de devolución, no podrá recibir nuevos préstamos.

La restricción permanecerá hasta que los juguetes vencidos sean devueltos.

### RN-04. Conservación de los datos históricos

Cada línea de préstamo almacenará una copia del nombre, referencia y categoría del juguete.

Esta información no cambiará cuando posteriormente se actualice el catálogo.

### RN-05. Conservación de operaciones y baja lógica

Los préstamos y devoluciones serán registros permanentes.

No se permitirá su eliminación.

Los juguetes podrán retirarse del catálogo únicamente mediante baja lógica, respetando las restricciones de préstamos abiertos.

### RN-06. Fechas, idioma y convenciones

Todas las fechas de las operaciones deberán almacenarse en UTC.

Los identificadores del código, tablas, columnas, clases y funciones se escribirán en inglés.

Los mensajes que reciba el usuario estarán en español.

## 6. Requisitos no funcionales

### RNF-01. Rendimiento

Las búsquedas de juguetes deberán responder en menos de 300 milisegundos utilizando un conjunto de 5.000 juguetes.

El rendimiento se comprobará mediante una prueba reproducible.

### RNF-02. Concurrencia

El sistema deberá manejar correctamente las operaciones simultáneas sobre el inventario.

Cuando dos usuarios intenten prestar la última unidad disponible del mismo juguete:

- Solamente una operación podrá completarse correctamente.
- La operación exitosa deberá responder HTTP 201.
- La segunda operación deberá responder HTTP 409.
- El inventario no podrá quedar negativo.

La prueba deberá ejecutarse utilizando PostgreSQL real.

### RNF-03. Seguridad

El sistema deberá proteger las credenciales y operaciones.

- Las contraseñas se almacenarán utilizando hashes seguros.
- Las contraseñas no aparecerán en respuestas ni registros de eventos.
- La autenticación utilizará tokens JWT.
- La transmisión de credenciales deberá realizarse mediante HTTPS en los entornos donde corresponda.
- Las operaciones protegidas validarán los permisos de cada usuario.

### RNF-04. Consistencia de respuestas HTTP

Todas las respuestas de error deberán mantener una estructura uniforme:

```json
{
  "code": "TOY_NOT_AVAILABLE",
  "message": "No hay unidades disponibles.",
  "details": []
}
```

Esta estructura facilitará el manejo de errores desde React y permitirá mostrar mensajes claros en español.

No se expondrán consultas SQL, contraseñas, claves ni trazas internas.

### RNF-05. Despliegue reproducible

El proyecto deberá poder ejecutarse mediante contenedores Docker.

Se utilizará el comando:

```bash
docker compose up --build
```

El sistema deberá iniciar los servicios definidos en la infraestructura y utilizar migraciones Laravel para crear la estructura de la base de datos.

La base de datos iniciará sin un esquema de negocio precreado.

El proyecto también deberá disponer de un sembrador de datos que utilice la API.

## 7. Modelo de información

Las principales entidades serán:

| Entidad | Descripción |
|---|---|
| `Category` | Clasificación de los juguetes |
| `Toy` | Información del juguete y su inventario |
| `Family` | Familia beneficiaria del servicio |
| `Promoter` | Usuario autorizado para operar el sistema |
| `Loan` | Registro general de un préstamo |
| `LoanItem` | Juguete individual incluido en un préstamo |

La base de datos contará con seis tablas principales:

- `categories`
- `toys`
- `families`
- `promoters`
- `loans`
- `loan_items`

Los atributos, restricciones y relaciones completas se describirán en `data-model.md`.

## 8. Contrato de comunicación

El frontend utilizará la API REST desarrollada con Laravel.

La API tendrá como ruta base:

`/api/v1`

Los endpoints estarán organizados por:

- Autenticación.
- Promotores.
- Categorías.
- Juguetes.
- Familias.
- Préstamos.
- Devoluciones.
- Reportes.

Las solicitudes y respuestas utilizarán JSON, excepto las operaciones de carga y consulta de imágenes.

Los campos de la API utilizarán nombres en inglés con formato `snake_case`.

El detalle de métodos HTTP, rutas, permisos y respuestas se encontrará en `api-contract.md`.

## 9. Restricciones del proyecto

El desarrollo deberá cumplir las siguientes condiciones:

1. Utilizar React para la interfaz de usuario.
2. Implementar el backend con PHP y Laravel.
3. Utilizar PostgreSQL para la persistencia.
4. Ejecutar los servicios mediante contenedores.
5. Implementar las diez historias de usuario.
6. Cumplir las seis reglas de negocio.
7. Cumplir los cinco requisitos no funcionales.
8. Implementar todos los endpoints del contrato.
9. Completar las 23 tareas definidas.
10. Contar con al menos quince archivos de pruebas automatizadas.
11. Mantener la documentación actualizada.
12. Evitar funcionalidades fuera de la especificación.

## 10. Criterios de finalización

El producto se considerará funcionalmente completo cuando:

- Las diez historias de usuario estén implementadas.
- Las reglas de negocio funcionen correctamente.
- Los endpoints del contrato respondan según lo documentado.
- La base de datos respete las restricciones de integridad.
- Las pruebas automatizadas se ejecuten correctamente.
- El proyecto pueda iniciarse mediante Docker.
- Los datos de demostración puedan crearse mediante la API.
- El frontend permita realizar las operaciones correspondientes a cada rol.
- La página pública presente Toy Library como producto.
- La documentación coincida con la implementación.

La finalización técnica deberá acompañarse de las evidencias y los criterios académicos establecidos en el Acta No. 13.

---

**Toy Library — Especificación del Producto — Plan de Mejoramiento A13 — Ficha 3239137.**
