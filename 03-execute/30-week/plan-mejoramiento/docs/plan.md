# Plan de Implementación — Toy Library

**Proyecto:** Toy Library — Juegoteca Comunitaria de Juguetes  
**Identificador:** `toy-library`  
**Programa:** Análisis y Desarrollo de Software (ADSO)  
**Ficha:** 3239137  
**Aprendiz:** A13  
**Versión:** 1.0  
**Estado:** En desarrollo

## 1. Propósito

Este documento establece el plan técnico para desarrollar Toy Library, una aplicación web destinada a gestionar juguetes, familias, promotores, préstamos, devoluciones e inventario de una juegoteca comunitaria.

El desarrollo seguirá los principios establecidos en `constitution.md` y los requisitos funcionales y no funcionales definidos en `spec.md`.

El objetivo es construir una solución completa, verificable y organizada, manteniendo la coherencia entre la documentación, el código y las pruebas automatizadas.

## 2. Alcance de la implementación

El proyecto deberá desarrollar las siguientes funcionalidades:

1. Autenticación y autorización de usuarios.
2. Registro y gestión de promotores.
3. Administración del catálogo de juguetes.
4. Búsqueda y consulta de juguetes.
5. Registro y consulta de familias.
6. Registro de préstamos.
7. Registro de devoluciones.
8. Consulta del historial de préstamos.
9. Identificación de préstamos vencidos.
10. Reporte de juguetes más solicitados.

Adicionalmente, deberá cumplir las seis reglas de negocio, los cinco requisitos no funcionales, los endpoints definidos en `api-contract.md` y las 23 tareas descritas en `tasks.md`.

## 3. Tecnologías seleccionadas

| Componente | Tecnología | Responsabilidad |
|---|---|---|
| Frontend | React | Interfaz de usuario |
| Lenguaje backend | PHP | Implementación del servidor |
| Framework backend | Laravel | API REST y lógica de aplicación |
| Base de datos | PostgreSQL | Persistencia de información |
| Contenedores | Docker | Ejecución de servicios |
| Orquestación local | Docker Compose | Inicio integrado |
| Autenticación | JWT | Identificación y control de acceso |
| Pruebas backend | PHPUnit | Pruebas automatizadas |
| Control de versiones | Git y GitHub | Historial de cambios |
| Documentación | Markdown | Especificaciones y decisiones técnicas |

Estas tecnologías deberán mantenerse coherentes con los documentos `architecture.md`, `data-model.md` y `api-contract.md`.

## 4. Arquitectura de la solución

El sistema utilizará una arquitectura cliente-servidor con separación de responsabilidades.

### 4.1. Frontend

El frontend se desarrollará utilizando React.

Sus responsabilidades serán:

- Presentar formularios y vistas.
- Gestionar la navegación.
- Mostrar información de juguetes y familias.
- Permitir registrar préstamos y devoluciones.
- Mostrar mensajes de validación en español.
- Consumir la API REST.
- Mostrar u ocultar funcionalidades según los permisos del usuario.

El frontend no será responsable de validar las reglas de negocio definitivas.

### 4.2. Backend

El backend se desarrollará utilizando PHP y Laravel.

Se organizará en cuatro capas:

**Dominio**

Contendrá las entidades, objetos de valor y reglas de negocio.

**Aplicación**

Contendrá los casos de uso que coordinan las operaciones del sistema.

**Infraestructura**

Implementará el acceso a PostgreSQL y los mecanismos técnicos necesarios.

**Presentación HTTP**

Contendrá los controladores, rutas, validaciones de entrada y transformación de respuestas.

### 4.3. Base de datos

PostgreSQL almacenará la información mediante las siguientes tablas:

- `categories`
- `toys`
- `families`
- `promoters`
- `loans`
- `loan_items`

Las tablas serán creadas mediante migraciones Laravel.

Las relaciones, restricciones e índices deberán coincidir con `data-model.md`.

### 4.4. Infraestructura

La aplicación deberá ejecutarse en contenedores Docker.

La configuración permitirá iniciar los servicios mediante un solo comando.

También se implementará un sembrador de datos utilizando la API.

## 5. Organización del proyecto

El proyecto utilizará la siguiente estructura:

```text
plan-mejoramiento/
├── docs/
│   ├── constitution.md
│   ├── spec.md
│   ├── plan.md
│   ├── architecture.md
│   ├── data-model.md
│   ├── api-contract.md
│   ├── tasks.md
│   └── adr/
├── api/
├── app/
├── page/
├── infra/
├── tool/
├── README.md
└── Acta_13_A13_version_publica.pdf
```

Cada carpeta tendrá una responsabilidad específica:

- `docs/`: documentación y decisiones arquitectónicas.
- `api/`: backend Laravel.
- `app/`: aplicación React.
- `page/`: sitio público estático.
- `infra/`: contenedores y configuración de servicios.
- `tool/`: sembrador de datos por API y herramientas de verificación.

## 6. Estrategia de implementación

El desarrollo se organizará por componentes y dependencias técnicas.

### Fase 1. Documentación y especificación

**Objetivo:** definir las reglas, arquitectura y contratos antes de implementar las funcionalidades.

Actividades:

1. Elaborar `constitution.md`.
2. Documentar las historias de usuario en `spec.md`.
3. Definir el plan técnico en `plan.md`.
4. Diseñar la arquitectura.
5. Definir el modelo de datos.
6. Documentar los endpoints.
7. Organizar las 23 tareas.
8. Registrar al menos tres ADR.

**Evidencia:** documentos Markdown completos y coherentes entre sí.

### Fase 2. Inicialización del backend

**Objetivo:** preparar el backend Laravel.

Actividades:

1. Crear la estructura del proyecto Laravel.
2. Configurar las dependencias.
3. Establecer la conexión con PostgreSQL.
4. Implementar el endpoint `GET /health`.
5. Configurar el entorno de pruebas.
6. Preparar las migraciones.

**Evidencia:** el servicio inicia correctamente y `GET /health` devuelve HTTP 200 con:

```json
{
  "status": "ok"
}
```

### Fase 3. Modelo de datos

**Objetivo:** implementar la persistencia y las restricciones de integridad.

Actividades:

1. Crear las migraciones de las seis tablas.
2. Definir claves primarias.
3. Definir claves foráneas.
4. Configurar índices únicos.
5. Aplicar restricciones de inventario.
6. Registrar las cinco categorías iniciales.
7. Comprobar la integridad de las relaciones.

**Evidencia:** migraciones ejecutadas correctamente y consultas de verificación satisfactorias.

### Fase 4. Dominio y reglas de negocio

**Objetivo:** desarrollar las reglas independientes de la infraestructura.

Actividades:

1. Implementar las entidades del dominio.
2. Crear objetos de valor cuando corresponda.
3. Validar las cantidades del inventario.
4. Aplicar el límite de tres juguetes por familia.
5. Calcular las fechas de vencimiento de nueve días.
6. Bloquear préstamos cuando existan juguetes vencidos.
7. Conservar los datos históricos de cada operación.
8. Implementar las validaciones de préstamos y devoluciones.

**Evidencia:** pruebas unitarias que comprueben las reglas del negocio.

### Fase 5. Autenticación y permisos

**Objetivo:** restringir el acceso a las operaciones.

Actividades:

1. Implementar la autenticación mediante JWT.
2. Crear el administrador inicial por configuración.
3. Permitir el registro de promotores.
4. Proteger los endpoints privados.
5. Aplicar permisos según el rol.
6. Validar errores HTTP 401 y 403.
7. Proteger el almacenamiento de contraseñas.

**Evidencia:** pruebas de autenticación y autorización para usuarios permitidos y no autorizados.

### Fase 6. Gestión de juguetes y familias

**Objetivo:** implementar las operaciones del catálogo y de las familias.

Actividades:

1. Registrar juguetes.
2. Consultar y buscar juguetes.
3. Actualizar información del catálogo.
4. Aplicar la baja lógica.
5. Controlar las referencias duplicadas.
6. Registrar familias.
7. Consultar familias.
8. Validar documentos duplicados.
9. Implementar paginación.

**Evidencia:** endpoints funcionales y pruebas automatizadas de validación.

### Fase 7. Préstamos y devoluciones

**Objetivo:** implementar las operaciones principales de la juegoteca.

Actividades:

1. Registrar préstamos asociados a familias.
2. Comprobar la disponibilidad.
3. Validar el límite de tres juguetes.
4. Validar los préstamos vencidos.
5. Descontar unidades disponibles.
6. Registrar devoluciones por línea.
7. Restituir unidades al inventario.
8. Evitar devoluciones duplicadas.
9. Conservar el historial de operaciones.
10. Ejecutar las operaciones mediante transacciones.

**Evidencia:** pruebas de préstamos, devoluciones, inventario e historial.

### Fase 8. Control de concurrencia

**Objetivo:** evitar inconsistencias cuando dos promotores operen simultáneamente sobre el mismo juguete.

Actividades:

1. Implementar transacciones de base de datos.
2. Aplicar mecanismos de bloqueo adecuados.
3. Controlar el acceso concurrente al inventario.
4. Evitar cantidades negativas.
5. Devolver HTTP 409 cuando la última unidad ya no esté disponible.
6. Comprobar que una sola operación se registre correctamente.

**Evidencia:** prueba de concurrencia utilizando PostgreSQL real.

### Fase 9. Consultas y reportes

**Objetivo:** proporcionar información sobre préstamos y demanda de juguetes.

Actividades:

1. Consultar el historial de préstamos.
2. Calcular los estados `open`, `overdue` y `closed`.
3. Consultar las líneas vencidas.
4. Calcular los días de retraso.
5. Generar el reporte de juguetes más solicitados.
6. Filtrar reportes por fechas.
7. Conservar la consistencia de los datos históricos.

**Evidencia:** pruebas automatizadas de consultas, estados y reportes.

### Fase 10. Aplicación React

**Objetivo:** construir las interfaces necesarias para utilizar el sistema.

Actividades:

1. Crear la estructura del frontend.
2. Implementar el inicio de sesión.
3. Gestionar la autenticación.
4. Crear las vistas de juguetes.
5. Crear las vistas de familias.
6. Implementar formularios de préstamos.
7. Implementar formularios de devoluciones.
8. Mostrar historial y préstamos vencidos.
9. Implementar la vista del reporte administrativo.
10. Integrar las respuestas y errores de la API.

**Evidencia:** interfaces funcionales conectadas al backend.

### Fase 11. Sitio público

**Objetivo:** presentar Toy Library como un producto web.

Actividades:

1. Crear una página pública estática.
2. Presentar el propósito de la juegoteca.
3. Explicar las funcionalidades principales.
4. Implementar un diseño adaptable.
5. Mantener los textos visibles en español.

**Evidencia:** sitio estático funcional que presenta el producto, sin enfocarse en el plan de mejoramiento.

### Fase 12. Contenedores y sembrador

**Objetivo:** facilitar la ejecución reproducible del sistema.

Actividades:

1. Crear la configuración Docker.
2. Configurar el servicio PostgreSQL.
3. Configurar el backend Laravel.
4. Configurar el frontend React.
5. Definir las variables de entorno de ejemplo.
6. Implementar el inicio mediante un solo comando.
7. Crear un sembrador que registre datos utilizando la API.
8. Verificar la comunicación entre servicios.

**Evidencia:** servicios ejecutándose correctamente y datos de prueba registrados mediante la API.

### Fase 13. Pruebas y verificación final

**Objetivo:** comprobar el cumplimiento completo de la especificación.

Actividades:

1. Ejecutar las pruebas unitarias.
2. Ejecutar las pruebas de integración.
3. Ejecutar las pruebas de autenticación.
4. Comprobar los endpoints.
5. Verificar el inventario.
6. Probar la concurrencia.
7. Comprobar reportes y consultas.
8. Verificar la ejecución con Docker.
9. Revisar que existan al menos 15 archivos de pruebas automatizadas.
10. Contrastar los resultados con las 23 tareas.

**Evidencia:** pruebas ejecutadas y resultados verificables.

## 7. Estrategia de pruebas

La validación del sistema se realizará mediante distintos tipos de pruebas.

### 7.1. Pruebas unitarias

Verificarán las reglas de negocio y los objetos de dominio.

Ejemplos:

- Validación de cantidades.
- Cálculo de fechas.
- Límite de préstamos.
- Estados de préstamos.
- Restricciones por vencimiento.

### 7.2. Pruebas de integración

Comprobarán la interacción entre la API, las operaciones de aplicación y PostgreSQL.

Ejemplos:

- Registro de juguetes.
- Registro de familias.
- Préstamos con actualización del inventario.
- Devoluciones.
- Persistencia del historial.

### 7.3. Pruebas de seguridad

Validarán los mecanismos de autenticación y autorización.

Ejemplos:

- Credenciales incorrectas.
- Tokens inválidos.
- Usuarios sin permisos.
- Acceso a endpoints protegidos.

### 7.4. Pruebas de concurrencia

Se ejecutarán solicitudes simultáneas para comprobar la integridad del inventario.

El caso principal consistirá en intentar prestar la última unidad disponible a través de dos solicitudes concurrentes.

El resultado esperado será una operación exitosa y otra rechazada con HTTP 409.

### 7.5. Pruebas de aceptación

Comprobarán que las diez historias de usuario cumplan sus criterios de aceptación.

Cada historia deberá contar con evidencia verificable.

## 8. Estrategia de control de versiones

El proyecto utilizará Git y GitHub siguiendo el documento de convenciones del repositorio.

### 8.1. Ramas

Los cambios se desarrollarán mediante ramas creadas a partir de `main`.

Los nombres seguirán el formato:

`<type>/<description-in-kebab-case>`

Ejemplos:

- `docs/toy-library-architecture`
- `docs/toy-library-data-model`
- `feat/toy-catalog`
- `feat/loan-management`
- `test/loan-concurrency`

### 8.2. Commits

Los mensajes utilizarán el formato:

`<type>(<scope>): <description>`

Ejemplos:

- `docs(architecture): define toy library system architecture`
- `docs(data): define toy library database model`
- `feat(api): implement toy registration endpoint`
- `test(quality): verify loan inventory constraints`
- `chore(devops): configure docker services`

### 8.3. Evidencia del historial

Se deberán realizar commits:

- Pequeños y descriptivos.
- Distribuidos en un mínimo de ocho días diferentes.
- Con al menos cuatro commits por cada subcarpeta exigida en el acta.
- Sin reescritura artificial del historial.

No se incluirán archivos `.env`, dependencias instaladas ni información sensible.

Los cambios deberán integrarse mediante Pull Requests conforme a las convenciones aplicables.

## 9. Criterios de verificación

La implementación deberá demostrar los siguientes resultados:

| Componente | Resultado esperado |
|---|---|
| Documentación | Especificaciones completas y coherentes |
| Backend | API Laravel funcional |
| Base de datos | Seis tablas con relaciones y restricciones |
| Autenticación | Acceso seguro según roles |
| Catálogo | Gestión de juguetes y disponibilidad |
| Familias | Registro y consultas |
| Préstamos | Cumplimiento de límites y vencimientos |
| Devoluciones | Actualización consistente del inventario |
| Concurrencia | Una operación exitosa y otra HTTP 409 |
| Reportes | Juguetes más solicitados |
| Frontend | Aplicación React funcional |
| Página pública | Presentación del producto |
| Contenedores | Inicio de servicios con un comando |
| Sembrador | Registro de datos mediante la API |
| Pruebas | Mínimo 15 archivos y ejecución verificable |

## 10. Entregables

La entrega deberá incluir:

1. Documentación técnica en `docs/`.
2. Backend Laravel en `api/`.
3. Frontend React en `app/`.
4. Sitio público estático en `page/`.
5. Configuración de infraestructura en `infra/`.
6. Sembrador y herramientas en `tool/`.
7. Archivo `README.md`.
8. Versión pública del Acta No. 13 identificada mediante el código A13, sin nombre ni documento de identidad.
9. Evidencias verificables de las tareas y pruebas.
10. Historial de commits conforme a los criterios de evaluación.

## 11. Fechas de entrega

**Primera entrega:** 19 de noviembre de 2026, a las 6:00 p. m.

**Sustentación individual:** 26 de noviembre de 2026, desde las 6:00 p. m.

**Segunda oportunidad, si corresponde:** 3 de diciembre de 2026, a las 6:00 p. m.

Durante la sustentación se deberá ejecutar el proyecto, explicar las decisiones técnicas y realizar un cambio solicitado por el evaluador.

## 12. Criterios de finalización

El proyecto se considerará técnicamente terminado cuando:

- Las diez historias de usuario estén implementadas.
- Se cumplan las seis reglas de negocio.
- Se verifiquen los cinco requisitos no funcionales.
- Los endpoints funcionen de acuerdo con `api-contract.md`.
- Las 23 tareas cuenten con evidencias.
- Las pruebas automatizadas se ejecuten correctamente.
- El proyecto funcione mediante contenedores.
- El frontend esté conectado al backend.
- La página pública presente el producto.
- El sembrador de datos utilice la API.
- La documentación y el código sean coherentes.

El cumplimiento académico también requiere aprobar la revisión del proyecto Toy Library y la revisión documental del proyecto formativo Friend Point, según el Acta No. 13.

## 13. Relación con otros documentos

Este plan deberá mantenerse alineado con:

- `constitution.md`: principios técnicos.
- `spec.md`: requisitos del producto.
- `architecture.md`: organización de componentes.
- `data-model.md`: modelo de persistencia.
- `api-contract.md`: contrato de comunicación.
- `tasks.md`: tareas y evidencias de implementación.
- `adr/`: decisiones arquitectónicas.

---

**Toy Library — Plan de Implementación — Plan de Mejoramiento A13 — Ficha 3239137.**
