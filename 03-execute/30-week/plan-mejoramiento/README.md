Este proyecto es la solución del plan de mejoramiento del aprendiz A13, ficha 3239137, dominio toy-library

# Toy Library — Juegoteca Comunitaria de Juguetes

## 1. Descripción del proyecto

Toy Library es una aplicación web diseñada para administrar el préstamo y la devolución de juguetes en una juegoteca comunitaria.

El sistema permite gestionar juguetes, familias, promotores, préstamos, devoluciones e inventario. Su propósito es facilitar el control de los juguetes disponibles y garantizar que los préstamos se realicen de manera organizada y segura.

Este proyecto corresponde al plan de mejoramiento individual A13, establecido en el Acta No. 13 de 2026 del programa Análisis y Desarrollo de Software (ADSO), ficha 3239137 del SENA.

## 2. Objetivo general

Desarrollar una aplicación web que permita gestionar los préstamos de juguetes de una juegoteca comunitaria, aplicando reglas de negocio, control de inventario, autenticación, persistencia de datos y pruebas automatizadas.

## 3. Tecnologías utilizadas

| Tecnología | Uso |
|---|---|
| React | Desarrollo de la interfaz de usuario |
| PHP | Lenguaje del backend |
| Laravel | Framework para desarrollar la API |
| PostgreSQL | Almacenamiento de datos |
| Docker | Ejecución de servicios en contenedores |
| REST API | Comunicación entre frontend y backend |
| Git y GitHub | Control de versiones |
| Markdown | Documentación del proyecto |

## 4. Funcionalidades del sistema

El proyecto contempla las siguientes funcionalidades:

1. Autenticación de administradores y promotores.
2. Registro y gestión de promotores.
3. Registro, actualización y baja lógica de juguetes.
4. Consulta y búsqueda de juguetes por nombre y categoría.
5. Registro y administración de familias.
6. Registro de préstamos de juguetes.
7. Registro de devoluciones.
8. Consulta del historial de préstamos.
9. Identificación de préstamos vencidos.
10. Generación de reportes de juguetes más solicitados.

## 5. Reglas de negocio

El sistema debe cumplir las siguientes reglas:

- Una familia puede tener máximo 3 juguetes pendientes de devolución.
- Cada préstamo tiene un plazo de 9 días.
- Una familia con préstamos vencidos no podrá recibir nuevos juguetes hasta devolverlos.
- La cantidad de unidades disponibles nunca podrá ser negativa ni superar el inventario total.
- Los datos históricos registrados en cada línea de préstamo deben permanecer congelados.
- Los préstamos y devoluciones no se pueden eliminar.
- Los juguetes se retiran del catálogo mediante baja lógica.
- Las fechas deben almacenarse en UTC.
- Los mensajes visibles para el usuario deben estar en español.
- Si dos promotores intentan prestar simultáneamente la última unidad disponible, solamente uno podrá completar la operación y el otro recibirá HTTP 409.

## 6. Categorías de juguetes

El sistema debe tener cinco categorías iniciales:

1. Bebés.
2. Construcción.
3. Didácticos.
4. Exterior.
5. Simbólicos.

Estas categorías se registrarán mediante el proceso de inicialización de datos.

## 7. Estructura del proyecto

```text
plan-mejoramiento/
│
├── docs/
│   ├── constitution.md
│   ├── spec.md
│   ├── plan.md
│   ├── architecture.md
│   ├── data-model.md
│   ├── api-contract.md
│   ├── tasks.md
│   ├── verificacion-acta-A13.md
│   └── adr/
│       ├── adr-001-migrations.md
│       ├── adr-002-concurrency.md
│       ├── adr-003-snapshots.md
│       └── adr-004-security.md
│
├── api/
│   └── Backend desarrollado con Laravel
│
├── app/
│   └── Frontend desarrollado con React
│
├── page/
│   └── Sitio público estático de Toy Library
│
├── infra/
│   └── Configuración de contenedores y base de datos
│
├── tool/
│   └── Herramientas de inicialización y verificación
│
├── README.md
└── Acta_13_A13_version_publica.pdf
```

## 8. Documentación

La documentación técnica se encuentra en la carpeta `docs/`.

| Archivo | Descripción |
|---|---|
| constitution.md | Principios y reglas técnicas del proyecto |
| spec.md | Especificación funcional y reglas de negocio |
| plan.md | Plan de implementación |
| architecture.md | Arquitectura y componentes del sistema |
| data-model.md | Modelo de datos y restricciones |
| api-contract.md | Contrato de los endpoints de la API |
| tasks.md | Las 23 tareas y sus evidencias |
| adr/ | Decisiones de arquitectura |
| verificacion-acta-A13.md | Verificación de los requisitos del acta |

## 9. Arquitectura del sistema

El sistema utiliza una arquitectura cliente-servidor.

**Frontend:** desarrollado en React para presentar las interfaces y permitir la interacción con los usuarios.

**Backend:** desarrollado en Laravel para procesar solicitudes, ejecutar reglas de negocio y administrar la información.

**Base de datos:** PostgreSQL para almacenar juguetes, categorías, familias, promotores, préstamos y sus detalles.

**Infraestructura:** contenedores Docker para ejecutar los servicios y la base de datos de forma integrada.

La comunicación entre el frontend y el backend se realiza mediante una API REST.

## 10. Instalación y ejecución

El proyecto debe permitir ejecutar los servicios y la base de datos utilizando un solo comando de Docker Compose.

Desde la carpeta que contiene la configuración de Compose, ejecutar:

```bash
docker compose up --build -d
```

**Nota:** antes de utilizar este comando se debe verificar la ubicación del archivo Compose y completar las variables de entorno necesarias, según la configuración de `infra/`.

La inicialización de la base de datos y la ejecución del sembrador mediante la API deben documentarse y verificarse durante la implementación.

## 11. Pruebas automatizadas

El proyecto debe contar con al menos 15 archivos de pruebas automatizadas.

Las pruebas deberán verificar:

- Validación de datos.
- Autenticación y autorización.
- Registro y consulta de juguetes.
- Gestión de familias.
- Registro de préstamos.
- Límite de tres juguetes por familia.
- Restricción de préstamos vencidos.
- Plazo de nueve días.
- Control del inventario.
- Registro de devoluciones.
- Conservación de datos históricos.
- Manejo de concurrencia.
- Respuestas HTTP de error.
- Reporte de juguetes más solicitados.

Las evidencias y resultados de las pruebas se registrarán durante el desarrollo.

## 12. Control de versiones

El proyecto se desarrolla utilizando Git y GitHub.

Se deberán cumplir las siguientes condiciones:

- Realizar commits pequeños y descriptivos.
- Distribuir los commits en mínimo 8 días diferentes.
- Registrar al menos 4 commits por subcarpeta requerida.
- No reescribir el historial.
- No subir archivos `.env`.
- No incluir dependencias como `node_modules`.
- Mantener coherencia entre documentación, código y pruebas.

## 13. Criterios de cumplimiento

El proyecto debe implementar:

- 10 historias de usuario.
- 6 reglas de negocio documentadas.
- 5 requisitos no funcionales.
- Todos los endpoints equivalentes del contrato.
- 23 tareas de implementación.
- Mínimo 15 archivos de pruebas automatizadas.
- Mínimo 3 registros de decisiones arquitectónicas (ADR).
- Frontend React.
- Backend Laravel.
- Base de datos en contenedores.
- Sitio público estático.
- Sembrador de datos mediante la API.

El proyecto debe cumplir las condiciones establecidas en el Acta No. 13 y demostrar su funcionamiento mediante evidencias verificables.

## 14. Entrega y sustentación

**Primera entrega:** 19 de noviembre de 2026 a las 6:00 p. m.

**Sustentación:** 26 de noviembre de 2026 desde las 6:00 p. m.

La sustentación será individual y consistirá en ejecutar el proyecto, explicar las decisiones técnicas y realizar un cambio solicitado durante la evaluación.

Para cumplir el plan de mejoramiento se requiere aprobar tanto la revisión de Toy Library como la revisión documental del proyecto formativo Friend Point, con mínimo 70 puntos en cada una y cumpliendo las condiciones específicas de evaluación.

## 15. Información académica

- **Institución:** Servicio Nacional de Aprendizaje (SENA).
- **Centro:** Centro de la Industria, la Empresa y los Servicios (CIES).
- **Regional:** Huila.
- **Programa:** Análisis y Desarrollo de Software (ADSO).
- **Ficha:** 3239137.
- **Código del aprendiz:** A13.
- **Proyecto:** Toy Library.
- **Acta:** No. 13 de 2026.
- **Tipo de entrega:** Plan de mejoramiento individual.

## 16. Estado del proyecto

**Estado:** En desarrollo.

La implementación, las pruebas y las evidencias de cumplimiento se irán registrando conforme se completen las tareas definidas en `docs/tasks.md`.

## 17. Referencias

- Acta No. 13 de 2026 — Plan de mejoramiento individual A13.
- Especificación de referencia Manager Library: https://github.com/code-sena/test-manager-library-3239137-docs
- Repositorio académico: https://github.com/code-sena/ADSO-3239137

---

**Toy Library — Plan de Mejoramiento A13 — Ficha 3239137.**
