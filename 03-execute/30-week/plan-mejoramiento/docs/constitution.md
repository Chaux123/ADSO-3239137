# Constitución del Proyecto — Toy Library

**Proyecto:** Toy Library — Juegoteca Comunitaria de Juguetes  
**Identificador:** `toy-library`  
**Programa:** Análisis y Desarrollo de Software (ADSO)  
**Ficha:** 3239137  
**Aprendiz:** A13  
**Versión:** 1.0  
**Estado:** Vigente

## 1. Propósito

La presente constitución establece los principios, normas técnicas y criterios de calidad que deben cumplirse durante el desarrollo del proyecto Toy Library.

Toy Library es una aplicación web destinada a gestionar juguetes, familias, promotores, préstamos, devoluciones e inventario dentro de una juegoteca comunitaria.

Su desarrollo debe garantizar la integridad de los datos, la seguridad de las operaciones, la organización del código y la correspondencia entre la documentación, la implementación y las pruebas.

## 2. Principios fundamentales

### I. Separación del dominio

Las reglas de negocio deben implementarse de forma independiente de las herramientas utilizadas para desarrollar la aplicación.

El dominio no debe depender directamente de React, Laravel, controladores HTTP ni de la base de datos.

Las reglas deben poder comprobarse mediante pruebas unitarias.

### II. Arquitectura organizada

El backend debe organizarse mediante capas con responsabilidades claramente definidas:

- **Dominio:** entidades, objetos de valor y reglas de negocio.
- **Aplicación:** casos de uso y coordinación de operaciones.
- **Infraestructura:** persistencia, repositorios e integraciones.
- **Presentación:** controladores HTTP y respuestas de la API.

El frontend debe mantener separados los componentes visuales, la comunicación con la API y la gestión del estado.

### III. Dependencias controladas

Las dependencias deben dirigirse hacia las reglas internas del sistema.

El dominio no debe conocer detalles de infraestructura ni depender de implementaciones específicas de frameworks.

### IV. Principios SOLID

El código debe aplicar los principios SOLID para facilitar su comprensión, mantenimiento y verificación.

Cada clase y función debe tener una responsabilidad clara.

Se evitará duplicar lógica de negocio y crear funcionalidades que no estén contempladas en la especificación.

### V. Base de datos mediante migraciones

La creación y modificación de tablas se realizará mediante migraciones de Laravel.

Las migraciones deben definir:

- Claves primarias y foráneas.
- Restricciones de integridad.
- Índices necesarios.
- Relaciones entre entidades.
- Validaciones de inventario que correspondan a la base de datos.

El modelo implementado deberá coincidir con `data-model.md`.

### VI. Integridad del inventario

El sistema debe garantizar que la cantidad de unidades disponibles de un juguete permanezca siempre entre cero y su cantidad total.

No se permitirá registrar préstamos cuando no existan unidades suficientes.

Las operaciones que modifiquen el inventario deben ejecutarse de manera consistente.

### VII. Información calculada

Los estados de los préstamos, los días de vencimiento y los reportes se calcularán a partir de la información registrada cuando corresponda.

Se evitará almacenar información derivada innecesaria que pueda provocar inconsistencias.

### VIII. Conservación del historial

Los préstamos y devoluciones deben conservarse como registros históricos.

No se permitirá eliminar estas operaciones.

Cada línea de préstamo deberá almacenar la información necesaria del juguete al momento de la operación.

Los cambios posteriores en el catálogo no podrán modificar los datos históricos congelados.

Los juguetes del catálogo únicamente podrán retirarse mediante baja lógica.

### IX. Seguridad de la información

El sistema debe proteger la información y las operaciones mediante mecanismos de autenticación y autorización.

Las credenciales deberán almacenarse de forma segura.

No se podrán publicar contraseñas, tokens, archivos `.env` ni otros secretos en GitHub.

Los permisos deben aplicarse a los endpoints correspondientes.

### X. Coherencia entre documentación y código

La documentación debe representar el funcionamiento real del sistema.

Cualquier modificación que afecte reglas de negocio, contratos de API, estructura de datos o arquitectura deberá reflejarse en los documentos correspondientes.

Los archivos `spec.md`, `architecture.md`, `data-model.md`, `api-contract.md` y `tasks.md` deberán mantenerse alineados.

### XI. Convenciones de idioma

Todos los identificadores utilizados en el código deberán estar escritos en inglés.

Esta regla incluye:

- Nombres de clases.
- Funciones y métodos.
- Variables.
- Entidades y atributos.
- Tablas y columnas.
- Endpoints de la API.

Los mensajes y textos presentados al usuario deberán estar en español.

### XII. Evidencias verificables

Cada tarea deberá contar con una evidencia que permita comprobar su implementación.

No se considerará terminada una tarea únicamente porque exista el código.

Las evidencias podrán incluir:

- Pruebas automatizadas.
- Respuestas verificables de los endpoints.
- Ejecución funcional de la aplicación.
- Validación de las restricciones de base de datos.
- Comprobación de reglas de negocio.

El proyecto deberá contar con al menos 15 archivos de pruebas automatizadas.

## 3. Reglas de negocio obligatorias

Toy Library debe cumplir las siguientes condiciones:

**RN-01. Límite de préstamos**

Cada familia podrá tener como máximo tres juguetes pendientes de devolución.

**RN-02. Plazo de devolución**

El plazo de préstamo será de nueve días.

**RN-03. Restricción por vencimiento**

Las familias con juguetes vencidos no podrán recibir nuevos préstamos hasta devolverlos.

**RN-04. Control del inventario**

Las unidades disponibles deberán mantenerse siempre entre cero y la cantidad total registrada.

**RN-05. Conservación de operaciones**

Los préstamos y devoluciones no podrán eliminarse. Los datos históricos congelados no se actualizarán cuando cambie el catálogo, y los juguetes tendrán únicamente baja lógica.

**RN-06. Concurrencia**

Cuando dos promotores intenten prestar simultáneamente la última unidad disponible de un juguete, solamente una operación podrá completarse.

La otra operación deberá recibir una respuesta HTTP 409.

## 4. Tecnologías del proyecto

Las tecnologías obligatorias son:

| Componente | Tecnología |
|---|---|
| Frontend | React |
| Backend | PHP con Laravel |
| Base de datos | PostgreSQL |
| Infraestructura | Docker |
| Comunicación | API REST |
| Control de versiones | Git y GitHub |
| Documentación | Markdown |

PostgreSQL se adopta como decisión técnica del proyecto. La tecnología de base de datos concreta deberá permanecer coherente con el modelo de datos, las migraciones y la configuración de infraestructura.

## 5. Reglas de desarrollo

Durante el desarrollo se deberán cumplir las siguientes condiciones:

1. Implementar las diez historias de usuario definidas en la especificación.
2. Respetar las seis reglas de negocio del proyecto.
3. Cumplir los cinco requisitos no funcionales documentados.
4. Implementar todos los endpoints equivalentes del contrato.
5. Completar las 23 tareas del proyecto.
6. Desarrollar pruebas automatizadas para los casos definidos.
7. Mantener el código y la documentación sincronizados.
8. No implementar funcionalidades fuera de la especificación.

## 6. Control de versiones

El proyecto utilizará Git y GitHub siguiendo las convenciones establecidas.

### 6.1 Ramas

Los cambios deberán desarrollarse en ramas específicas creadas a partir de `main`.

No se realizarán commits directamente sobre `main`.

Cada rama debe representar una tarea o un conjunto coherente de cambios.

### 6.2 Formato de commits

Los mensajes de commit seguirán el formato:

`<type>(<scope>): <description>`

Se utilizarán los tipos establecidos en `git-conventions.md`, como `feat`, `fix`, `docs`, `test`, `refactor`, `chore`, `style` y `perf`.

### 6.3 Historial

El desarrollo deberá registrar:

- Commits pequeños y descriptivos.
- Actividad en al menos ocho días diferentes.
- Mínimo cuatro commits por cada subcarpeta exigida en el acta.
- Un historial auténtico, sin modificar fechas ni reescribir commits.

### 6.4 Protección de archivos

El repositorio no deberá incluir:

- Archivos `.env` con información privada.
- Carpetas `node_modules`.
- Dependencias generadas.
- Credenciales o tokens.
- Archivos temporales innecesarios.

## 7. Infraestructura y ejecución

La aplicación deberá poder ejecutarse mediante contenedores.

La infraestructura debe permitir iniciar el frontend, backend y base de datos mediante un solo comando.

También deberá existir un mecanismo para sembrar datos utilizando la API.

Las fechas utilizadas por las operaciones deberán almacenarse en UTC.

## 8. Criterios de calidad

El sistema deberá:

- Mantener la consistencia del inventario.
- Evitar préstamos que incumplan las reglas.
- Proteger las operaciones mediante autenticación y autorización.
- Preservar el historial.
- Manejar correctamente los conflictos de concurrencia.
- Proporcionar mensajes claros en español.
- Contar con pruebas automatizadas verificables.
- Poder ejecutarse y explicarse durante una sustentación individual.

## 9. Documentación y decisiones arquitectónicas

El proyecto deberá contener los siguientes documentos:

- `constitution.md`
- `spec.md`
- `plan.md`
- `architecture.md`
- `data-model.md`
- `api-contract.md`
- `tasks.md`

Además, deberá registrar al menos tres decisiones arquitectónicas mediante documentos ADR.

Los ADR deben explicar el contexto, la decisión tomada y sus consecuencias.

## 10. Cumplimiento

Esta constitución orientará el desarrollo de Toy Library y será utilizada para verificar la coherencia de la implementación.

Ninguna funcionalidad se considerará terminada si incumple las reglas de negocio, contradice la especificación o carece de evidencia verificable.

El objetivo final es entregar una aplicación funcional, documentada, comprobable y que pueda ser sustentada individualmente.

---

**Toy Library — Plan de Mejoramiento A13 — ADSO 3239137.**
