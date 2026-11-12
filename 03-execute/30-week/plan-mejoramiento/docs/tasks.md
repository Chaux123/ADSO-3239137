# Plan de Tareas — Toy Library

**Proyecto:** Toy Library — Juegoteca Comunitaria de Juguetes  
**Identificador:** `toy-library`  
**Programa:** Análisis y Desarrollo de Software (ADSO)  
**Ficha:** 3239137  
**Aprendiz:** A13  
**Versión:** 1.0  
**Estado:** Pendiente de implementación

## 1. Propósito

Este documento organiza las 23 tareas de desarrollo del proyecto Toy Library y establece las evidencias necesarias para comprobar su cumplimiento.

El sistema permitirá administrar juguetes, familias, promotores, préstamos, devoluciones e inventario dentro de una juegoteca comunitaria.

Las tareas deberán desarrollarse de acuerdo con los requisitos de `spec.md`, el modelo de datos de `data-model.md`, los componentes de `architecture.md` y los endpoints de `api-contract.md`.

Cada tarea contará con una descripción, dependencias y criterios de verificación.

## 2. Condiciones generales

Para completar el proyecto se deberán cumplir las siguientes condiciones:

1. Implementar las diez historias de usuario.
2. Cumplir las seis reglas de negocio.
3. Cumplir los cinco requisitos no funcionales.
4. Implementar todos los endpoints del contrato HTTP.
5. Completar las 23 tareas de desarrollo.
6. Crear un mínimo de quince archivos de pruebas automatizadas.
7. Ejecutar el frontend utilizando React.
8. Desarrollar el backend utilizando PHP con Laravel.
9. Utilizar PostgreSQL para la persistencia.
10. Permitir iniciar los servicios con un solo comando.
11. Implementar un sembrador que utilice la API.
12. Crear un sitio público estático.
13. Mantener los nombres técnicos en inglés y los mensajes para usuarios en español.
14. Registrar evidencias verificables de las funcionalidades.

Las tareas inicialmente tendrán estado **pendiente**.

Una tarea solamente podrá marcarse como verificada cuando se haya ejecutado correctamente su evidencia.

## 3. Resumen de las 23 tareas

| ID | Tarea | Estado |
|---|---|---|
| T-01 | Inicialización del proyecto y endpoint de salud | Pendiente |
| T-02 | Migraciones y estructura de base de datos | Pendiente |
| T-03 | Objetos de valor del dominio | Pendiente |
| T-04 | Dominio de juguetes e inventario | Pendiente |
| T-05 | Dominio de préstamos y vencimientos | Pendiente |
| T-06 | Dominio de familias y promotores | Pendiente |
| T-07 | Casos de uso de autenticación y promotores | Pendiente |
| T-08 | Gestión del catálogo de juguetes | Pendiente |
| T-09 | Registro de préstamos | Pendiente |
| T-10 | Devoluciones y préstamos vencidos | Pendiente |
| T-11 | Reporte de juguetes más solicitados | Pendiente |
| T-12 | Persistencia de juguetes, familias y usuarios | Pendiente |
| T-13 | Persistencia de préstamos y concurrencia | Pendiente |
| T-14 | Seguridad, contraseñas y JWT | Pendiente |
| T-15 | Gestión de imágenes de juguetes | Pendiente |
| T-16 | Contrato HTTP y autorización | Pendiente |
| T-17 | Inicialización del administrador | Pendiente |
| T-18 | Restricciones de integridad PostgreSQL | Pendiente |
| T-19 | Validación de la arquitectura | Pendiente |
| T-20 | Infraestructura con Docker | Pendiente |
| T-21 | Sembrador de datos mediante API | Pendiente |
| T-22 | Aplicación frontend React | Pendiente |
| T-23 | Página pública estática | Pendiente |

## 4. Descripción y evidencia de las tareas

### T-01. Inicialización del proyecto y endpoint de salud

**Objetivo:** preparar la estructura inicial del backend Laravel y comprobar su funcionamiento.

**Actividades:**

- Crear la estructura del backend en `api/`.
- Configurar las dependencias del proyecto.
- Preparar las rutas de Laravel.
- Implementar el endpoint de salud.
- Configurar las variables de entorno de ejemplo.
- Preparar el entorno para las pruebas automatizadas.

**Dependencias:** ninguna.

**Evidencias:**

- El backend inicia correctamente.
- El endpoint `GET /health` responde HTTP 200.
- La respuesta contiene:

```json
{
  "status": "ok"
}
```

**Estado:** Pendiente.

### T-02. Migraciones y estructura de base de datos

**Objetivo:** crear el esquema de PostgreSQL utilizando migraciones Laravel.

**Actividades:**

- Crear la migración de `categories`.
- Crear la migración de `toys`.
- Crear la migración de `families`.
- Crear la migración de `promoters`.
- Crear la migración de `loans`.
- Crear la migración de `loan_items`.
- Definir claves primarias y foráneas.
- Configurar los índices y restricciones.
- Registrar las cinco categorías iniciales.

**Dependencias:** T-01.

**Evidencias:**

- Las seis tablas existen en PostgreSQL.
- Las cinco categorías iniciales están registradas.
- Las relaciones y restricciones coinciden con `data-model.md`.
- Las migraciones se ejecutan correctamente.
- El comando `php artisan migrate:status` muestra las migraciones correspondientes.

**Estado:** Pendiente.

### T-03. Objetos de valor del dominio

**Objetivo:** implementar objetos de valor para representar datos sujetos a reglas del negocio.

**Actividades:**

- Implementar `ToyReference`.
- Implementar `Quantity`.
- Implementar `DateRange`.
- Aplicar validaciones de los valores recibidos.
- Crear pruebas unitarias para los casos válidos e inválidos.

**Dependencias:** T-01.

**Evidencias:**

- Los objetos de valor validan sus condiciones.
- No se aceptan cantidades inválidas.
- Los intervalos de fechas no permiten una fecha final anterior a la inicial.
- Las referencias se validan según la especificación.
- Las pruebas unitarias se ejecutan correctamente.

**Estado:** Pendiente.

### T-04. Dominio de juguetes e inventario

**Objetivo:** implementar las reglas de gestión de juguetes y control de existencias.

**Actividades:**

- Implementar la entidad `Toy`.
- Registrar la información del juguete.
- Controlar el total de unidades.
- Validar las unidades disponibles.
- Implementar las operaciones de actualización del inventario.
- Aplicar las restricciones de baja lógica.

**Dependencias:** T-03.

**Evidencias:**

- El total de unidades es mayor o igual a uno.
- Las unidades disponibles nunca son negativas.
- Las unidades disponibles nunca superan el total.
- No se permiten actualizaciones que incumplan las restricciones.
- Las pruebas unitarias verifican estas condiciones.

**Estado:** Pendiente.

### T-05. Dominio de préstamos y vencimientos

**Objetivo:** implementar las reglas de préstamos, fechas de devolución y estados calculados.

**Actividades:**

- Implementar las entidades `Loan` y `LoanItem`.
- Definir el plazo de nueve días.
- Aplicar el límite de tres juguetes pendientes por familia.
- Identificar líneas de préstamo vencidas.
- Calcular los estados `open`, `overdue` y `closed`.
- Conservar los datos históricos de las líneas.

**Dependencias:** T-03 y T-04.

**Evidencias:**

- El vencimiento se calcula nueve días después del préstamo.
- No se permiten más de tres juguetes pendientes por familia.
- Los estados se calculan correctamente.
- Las pruebas permiten controlar el tiempo mediante un reloj de prueba.
- Los datos históricos de cada línea permanecen inmutables.

**Estado:** Pendiente.

### T-06. Dominio de familias y promotores

**Objetivo:** implementar los componentes del dominio correspondientes a familias y usuarios.

**Actividades:**

- Implementar la entidad `Family`.
- Implementar la entidad `Promoter`.
- Validar los documentos de las familias.
- Normalizar nombres de usuario.
- Definir los roles `admin` y `promoter`.
- Aplicar las reglas de identificación.

**Dependencias:** T-03.

**Evidencias:**

- Los nombres de usuario se normalizan correctamente.
- Los roles se validan.
- No se aceptan valores inválidos.
- Las pruebas unitarias verifican las reglas del dominio.

**Estado:** Pendiente.

### T-07. Casos de uso de autenticación y promotores

**Objetivo:** implementar los casos de uso relacionados con el acceso al sistema y la gestión de promotores.

**Actividades:**

- Implementar el inicio de sesión.
- Validar credenciales.
- Crear promotores desde una cuenta administradora.
- Comprobar permisos según el rol.
- Controlar nombres de usuario duplicados.
- Aplicar las validaciones de entrada.

**Dependencias:** T-06 y T-14.

**Evidencias:**

- El inicio de sesión funciona con credenciales válidas.
- Las credenciales inválidas son rechazadas.
- Un promotor no puede crear otros usuarios.
- Los errores de permisos devuelven HTTP 403.
- Los datos inválidos devuelven HTTP 422.
- Los usuarios duplicados generan el conflicto documentado.

**Estado:** Pendiente.

### T-08. Gestión del catálogo de juguetes

**Objetivo:** implementar las operaciones necesarias para administrar el catálogo.

**Actividades:**

- Registrar juguetes.
- Consultar juguetes.
- Buscar por nombre o marca.
- Filtrar por categoría.
- Actualizar información del catálogo.
- Aplicar la baja lógica.
- Validar referencias duplicadas.
- Integrar la información de imágenes.

**Dependencias:** T-04, T-12 y T-15.

**Evidencias:**

- Los juguetes se registran correctamente.
- Las consultas permiten búsqueda y paginación.
- No se permiten referencias activas duplicadas.
- Las actualizaciones conservan la integridad del inventario.
- Los juguetes retirados no aparecen en las búsquedas activas.
- Las pruebas comprueban las operaciones del catálogo.

**Estado:** Pendiente.

### T-09. Registro de préstamos

**Objetivo:** implementar el caso de uso para prestar juguetes a las familias.

**Actividades:**

- Validar la familia.
- Verificar los juguetes solicitados.
- Comprobar las unidades disponibles.
- Aplicar el límite de tres juguetes pendientes.
- Bloquear nuevas entregas cuando existan préstamos vencidos.
- Calcular la fecha de devolución.
- Descontar las unidades del inventario.
- Registrar el préstamo y sus líneas.
- Conservar la información histórica del juguete.

**Dependencias:** T-05, T-06 y T-13.

**Evidencias:**

- Los préstamos válidos se registran correctamente.
- El inventario se actualiza.
- No se supera el límite de tres juguetes.
- Las familias con juguetes vencidos no reciben nuevos préstamos.
- Los juguetes sin disponibilidad generan HTTP 409.
- No se registran operaciones parciales.

**Estado:** Pendiente.

### T-10. Devoluciones y préstamos vencidos

**Objetivo:** implementar el registro de devoluciones y la consulta de juguetes vencidos.

**Actividades:**

- Identificar la línea del préstamo.
- Comprobar que el juguete no fue devuelto.
- Registrar la fecha de devolución en UTC.
- Incrementar las unidades disponibles.
- Calcular préstamos vencidos.
- Calcular días de retraso.
- Evitar devoluciones duplicadas.

**Dependencias:** T-09.

**Evidencias:**

- Las devoluciones actualizan el inventario.
- Las fechas de devolución quedan registradas.
- Los préstamos vencidos se identifican correctamente.
- Una devolución duplicada genera HTTP 409.
- Los datos históricos no se eliminan ni modifican indebidamente.

**Estado:** Pendiente.

### T-11. Reporte de juguetes más solicitados

**Objetivo:** implementar el reporte que identifica los juguetes con mayor cantidad de préstamos.

**Actividades:**

- Consultar los préstamos históricos.
- Filtrar por intervalo de fechas.
- Contar las líneas de préstamo por juguete.
- Ordenar por número de solicitudes.
- Utilizar los datos históricos congelados.
- Restringir el acceso al administrador.

**Dependencias:** T-09 y T-13.

**Evidencias:**

- El reporte muestra los juguetes más solicitados.
- Las cantidades corresponden a las operaciones registradas.
- Los resultados están ordenados de mayor a menor.
- Se utilizan los nombres y categorías congelados.
- Los intervalos inválidos se rechazan.
- Las pruebas verifican los resultados.

**Estado:** Pendiente.

### T-12. Persistencia de juguetes, familias y usuarios

**Objetivo:** implementar el acceso a PostgreSQL para las entidades principales del sistema.

**Actividades:**

- Implementar repositorios de juguetes.
- Implementar repositorios de familias.
- Implementar repositorios de promotores.
- Configurar las consultas.
- Integrar los repositorios con los servicios de aplicación.
- Comprobar las relaciones y restricciones.

**Dependencias:** T-02, T-04 y T-06.

**Evidencias:**

- Los datos se guardan en PostgreSQL.
- Las consultas recuperan información correcta.
- Las restricciones de unicidad funcionan.
- Los repositorios respetan las reglas de persistencia.
- Las pruebas de integración se ejecutan con PostgreSQL.

**Estado:** Pendiente.

### T-13. Persistencia de préstamos y concurrencia

**Objetivo:** implementar las operaciones transaccionales de préstamos y devoluciones.

**Actividades:**

- Implementar la persistencia de préstamos.
- Registrar las líneas de préstamo.
- Utilizar transacciones PostgreSQL.
- Aplicar bloqueos de filas mediante `SELECT FOR UPDATE`.
- Comprobar la disponibilidad antes de descontar unidades.
- Evitar conflictos de inventario.
- Garantizar la atomicidad de las operaciones.

**Dependencias:** T-02, T-05 y T-12.

**Evidencias:**

- Los préstamos se registran mediante transacciones.
- Las operaciones incompletas se revierten.
- Las unidades disponibles no quedan negativas.
- Dos solicitudes simultáneas sobre la última unidad producen una respuesta HTTP 201 y otra HTTP 409.
- La prueba de concurrencia se ejecuta utilizando PostgreSQL real.

**Estado:** Pendiente.

### T-14. Seguridad, contraseñas y JWT

**Objetivo:** implementar los mecanismos de autenticación y protección de información.

**Actividades:**

- Configurar el almacenamiento seguro de contraseñas.
- Implementar la validación de credenciales.
- Generar tokens JWT.
- Establecer una duración de ocho horas para los tokens.
- Verificar tokens en las rutas protegidas.
- Evitar la exposición de secretos.
- Aplicar controles de autorización.

**Dependencias:** T-06 y T-12.

**Evidencias:**

- Las contraseñas se almacenan mediante hashes seguros.
- Las credenciales no aparecen en las respuestas.
- Los tokens tienen la duración configurada.
- Los tokens inválidos son rechazados.
- Los usuarios sin permisos no pueden ejecutar operaciones restringidas.

**Estado:** Pendiente.

### T-15. Gestión de imágenes de juguetes

**Objetivo:** permitir almacenar y consultar imágenes asociadas a los juguetes.

**Actividades:**

- Implementar el adaptador de almacenamiento.
- Aceptar imágenes JPG y PNG.
- Validar el tamaño máximo permitido.
- Almacenar la referencia mediante `cover_key`.
- Permitir reemplazar una imagen.
- Permitir eliminar una imagen.
- Permitir consultar la imagen almacenada.

**Dependencias:** T-04 y T-12.

**Evidencias:**

- Una imagen válida de hasta 2 MB se acepta.
- Una imagen de 3 MB se rechaza.
- Los formatos no permitidos son rechazados.
- Las imágenes se pueden consultar y reemplazar.
- Las pruebas comprueban los límites establecidos.

**Estado:** Pendiente.

### T-16. Contrato HTTP y autorización

**Objetivo:** implementar los endpoints definidos en `api-contract.md`.

**Actividades:**

- Crear los controladores HTTP.
- Registrar las rutas de la API.
- Validar los datos de entrada.
- Aplicar autenticación y autorización.
- Implementar las respuestas JSON.
- Utilizar los códigos HTTP correspondientes.
- Mantener una estructura uniforme de errores.

**Dependencias:** T-07, T-08, T-09, T-10, T-11, T-14 y T-15.

**Evidencias:**

- Todos los endpoints definidos en el contrato funcionan.
- Los cuerpos de respuesta coinciden con la documentación.
- Las rutas protegidas validan los permisos.
- Los errores utilizan `code`, `message` y `details`.
- Los mensajes dirigidos al usuario aparecen en español.
- Las pruebas verifican los contratos HTTP.

**Estado:** Pendiente.

### T-17. Inicialización del administrador

**Objetivo:** permitir la creación del administrador inicial mediante configuración de entorno.

**Actividades:**

- Definir las variables de entorno necesarias.
- Configurar el usuario administrador inicial.
- Proteger sus credenciales.
- Crear el registro en una base de datos inicializada.
- Comprobar el acceso mediante el endpoint de autenticación.
- Impedir la creación de administradores mediante la API.

**Dependencias:** T-02, T-07 y T-14.

**Evidencias:**

- El administrador puede iniciar sesión después de inicializar una base de datos vacía.
- La contraseña se almacena mediante hash.
- No existen credenciales reales publicadas.
- El endpoint de creación de usuarios no permite registrar administradores adicionales.

**Estado:** Pendiente.

### T-18. Restricciones de integridad PostgreSQL

**Objetivo:** comprobar que PostgreSQL protege las reglas fundamentales de integridad.

**Actividades:**

- Implementar las restricciones `CHECK`.
- Validar las unidades totales.
- Validar las unidades disponibles.
- Restringir los roles permitidos.
- Comprobar claves foráneas.
- Comprobar índices únicos.
- Ejecutar pruebas que intenten guardar información inválida.

**Dependencias:** T-02.

**Evidencias:**

- No se permite `total_units` menor que uno.
- No se permiten unidades disponibles negativas.
- No se permiten unidades disponibles superiores al total.
- Los roles diferentes de `admin` y `promoter` son rechazados.
- Las referencias duplicadas y relaciones inválidas son rechazadas según sus restricciones.

**Estado:** Pendiente.

### T-19. Validación de arquitectura

**Objetivo:** comprobar que el código respeta la arquitectura definida.

**Actividades:**

- Verificar la separación entre dominio y framework.
- Comprobar las dependencias entre capas.
- Evitar referencias directas desde el dominio hacia infraestructura.
- Revisar la responsabilidad de los controladores.
- Implementar pruebas de reglas arquitectónicas.

**Dependencias:** T-04, T-05, T-06 y T-12.

**Evidencias:**

- Las reglas de negocio pueden probarse de manera independiente.
- El dominio no depende directamente de Laravel.
- Las pruebas arquitectónicas detectan dependencias prohibidas.
- La implementación coincide con `architecture.md`.

**Estado:** Pendiente.

### T-20. Infraestructura con Docker

**Objetivo:** permitir ejecutar el proyecto mediante contenedores.

**Actividades:**

- Crear la configuración Docker.
- Configurar el contenedor Laravel.
- Configurar el contenedor React.
- Configurar PostgreSQL.
- Configurar las variables de entorno.
- Preparar la comunicación entre servicios.
- Permitir iniciar el sistema con un solo comando.

**Dependencias:** T-01 y T-02.

**Evidencias:**

El comando:

```bash
docker compose up --build
```

deberá iniciar los servicios del proyecto desde el directorio indicado en la documentación.

También se comprobará que:

- Laravel responde a las solicitudes.
- React puede comunicarse con la API.
- PostgreSQL mantiene la persistencia.
- Los servicios pueden iniciarse de forma reproducible.

**Estado:** Pendiente.

### T-21. Sembrador de datos mediante API

**Objetivo:** implementar una herramienta para cargar datos de demostración utilizando la API.

**Actividades:**

- Crear el sembrador dentro de `tool/`.
- Autenticarse mediante la API.
- Registrar juguetes de ejemplo.
- Registrar familias.
- Registrar préstamos.
- Crear escenarios de demostración.
- Verificar el resultado mediante consultas HTTP.

**Dependencias:** T-16, T-17 y T-20.

**Evidencias:**

El sembrador deberá registrar, como mínimo:

- 40 juguetes.
- 10 familias.
- 15 préstamos.

Los datos deberán registrarse mediante la API y no mediante inserciones SQL directas.

El conjunto de demostración deberá permitir verificar préstamos vencidos después de nueve días.

**Estado:** Pendiente.

### T-22. Aplicación frontend React

**Objetivo:** implementar la interfaz de usuario de Toy Library.

**Actividades:**

- Crear la aplicación React.
- Implementar el inicio de sesión.
- Gestionar la autenticación.
- Mostrar el catálogo de juguetes.
- Implementar la gestión de familias.
- Crear los formularios de préstamos.
- Implementar el registro de devoluciones.
- Mostrar los préstamos vencidos.
- Presentar el historial de préstamos.
- Mostrar el reporte de juguetes más solicitados.
- Aplicar permisos según el rol.

**Dependencias:** T-16.

**Evidencias:**

- El inicio de sesión funciona.
- El catálogo muestra información de la API.
- Se pueden registrar familias.
- Se pueden registrar préstamos y devoluciones.
- Los vencimientos aparecen correctamente.
- Los administradores pueden consultar el reporte.
- Los errores y mensajes se presentan en español.

**Estado:** Pendiente.

### T-23. Página pública estática

**Objetivo:** crear un sitio público para presentar Toy Library como producto.

**Actividades:**

- Crear la estructura del sitio estático en `page/`.
- Presentar la finalidad de Toy Library.
- Describir las funcionalidades principales.
- Implementar un diseño adaptable.
- Garantizar una visualización correcta en dispositivos móviles y computadores.
- Mantener los textos en español.

**Dependencias:** T-01.

**Evidencias:**

- La página se visualiza correctamente.
- El diseño funciona en móvil y escritorio.
- La página presenta Toy Library como producto.
- La página no depende de la API para mostrar su contenido.
- La página no está dedicada a explicar la prueba técnica o el plan de mejoramiento.

**Estado:** Pendiente.

## 5. Matriz de dependencias

Las dependencias propuestas permiten identificar las tareas necesarias antes de implementar cada funcionalidad.

| Tarea | Depende de |
|---|---|
| T-01 | Ninguna |
| T-02 | T-01 |
| T-03 | T-01 |
| T-04 | T-03 |
| T-05 | T-03, T-04 |
| T-06 | T-03 |
| T-07 | T-06, T-14 |
| T-08 | T-04, T-12, T-15 |
| T-09 | T-05, T-06, T-13 |
| T-10 | T-09 |
| T-11 | T-09, T-13 |
| T-12 | T-02, T-04, T-06 |
| T-13 | T-02, T-05, T-12 |
| T-14 | T-06, T-12 |
| T-15 | T-04, T-12 |
| T-16 | T-07 a T-11, T-14, T-15 |
| T-17 | T-02, T-07, T-14 |
| T-18 | T-02 |
| T-19 | T-04, T-05, T-06, T-12 |
| T-20 | T-01, T-02 |
| T-21 | T-16, T-17, T-20 |
| T-22 | T-16 |
| T-23 | T-01 |

## 6. Estrategia de pruebas

El proyecto deberá contar con al menos quince archivos de pruebas automatizadas.

Las pruebas se organizarán en las siguientes categorías:

### 6.1. Pruebas unitarias

- Objetos de valor.
- Inventario.
- Fechas y vencimientos.
- Límite de préstamos.
- Estados de préstamos.
- Validación de entidades.

### 6.2. Pruebas de integración

- Persistencia de juguetes.
- Persistencia de familias.
- Persistencia de promotores.
- Registro de préstamos.
- Registro de devoluciones.
- Restricciones PostgreSQL.

### 6.3. Pruebas HTTP

- Autenticación.
- Permisos y roles.
- Gestión del catálogo.
- Gestión de familias.
- Préstamos.
- Devoluciones.
- Reportes.
- Respuestas de error.

### 6.4. Pruebas de concurrencia

Se comprobará que dos solicitudes simultáneas sobre la última unidad disponible produzcan exactamente una operación exitosa y otra rechazada mediante HTTP 409.

Esta prueba deberá ejecutarse con PostgreSQL real.

## 7. Control de versiones

El desarrollo seguirá las convenciones Git establecidas para el repositorio.

Los commits utilizarán el formato:

`<type>(<scope>): <description>`

Se deberán cumplir las siguientes condiciones:

- Realizar commits pequeños.
- Distribuir los commits en al menos ocho días diferentes.
- Registrar como mínimo cuatro commits por cada subcarpeta exigida.
- No reescribir el historial.
- No publicar archivos `.env`.
- No incluir carpetas `node_modules`.
- Utilizar ramas y Pull Requests según las convenciones aplicables.

Cada commit deberá representar un cambio real y verificable.

## 8. Seguimiento y evidencias

El progreso deberá registrarse mediante los siguientes estados:

- **Pendiente:** la tarea no ha sido implementada.
- **En desarrollo:** la implementación está en proceso.
- **Verificada:** la evidencia se ha ejecutado satisfactoriamente.

Una tarea no podrá marcarse como verificada únicamente porque sus archivos existan.

Se deberán registrar los resultados de las pruebas y comprobar el funcionamiento real.

## 9. Criterios de finalización

El desarrollo estará técnicamente completo cuando:

1. Las 23 tareas estén verificadas.
2. Las diez historias de usuario estén implementadas.
3. Se cumplan las seis reglas de negocio.
4. Se cumplan los cinco requisitos no funcionales.
5. Todos los endpoints respondan según el contrato.
6. Existan al menos quince archivos de pruebas automatizadas.
7. Las pruebas se ejecuten correctamente.
8. La aplicación React funcione con Laravel.
9. PostgreSQL mantenga la integridad de los datos.
10. Docker permita iniciar los servicios con un comando.
11. El sembrador registre datos mediante la API.
12. La página pública presente Toy Library.
13. La documentación coincida con el código.
14. El historial de commits cumpla las condiciones del acta.

El cumplimiento académico requerirá además aprobar las dos revisiones y realizar la sustentación individual establecida en el Acta No. 13.

## 10. Referencias

- `constitution.md`: principios de desarrollo.
- `spec.md`: requisitos del producto.
- `plan.md`: estrategia de implementación.
- `architecture.md`: arquitectura del sistema.
- `data-model.md`: estructura de PostgreSQL.
- `api-contract.md`: endpoints y respuestas HTTP.
- `adr/`: decisiones técnicas.
- Acta No. 13 de 2026: plan de mejoramiento individual A13.

---

**Toy Library — Plan de 23 Tareas — Plan de Mejoramiento A13 — Ficha 3239137.**
