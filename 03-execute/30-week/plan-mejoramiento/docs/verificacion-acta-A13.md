# Verificación de Cumplimiento del Acta No. 13 — Toy Library

**Proyecto:** Toy Library — Juegoteca Comunitaria de Juguetes  
**Identificador:** `toy-library`  
**Programa:** Análisis y Desarrollo de Software (ADSO)  
**Ficha:** 3239137  
**Código del aprendiz:** A13  
**Acta:** No. 13 de 2026  
**Fecha del acta:** 5 de noviembre de 2026  
**Estado:** En proceso de verificación

## 1. Propósito

Este documento permite verificar el cumplimiento de los requisitos establecidos en el Acta No. 13 de 2026, correspondiente al plan de mejoramiento individual A13.

Su objetivo es controlar las entregas obligatorias, verificar las reglas del proyecto Toy Library, registrar evidencias y preparar la sustentación individual.

Este documento complementa los archivos de especificación y no reemplaza los criterios oficiales de evaluación.

## 2. Información general del proyecto

| Elemento | Información |
|---|---|
| Código del aprendiz | A13 |
| Ficha | 3239137 |
| Proyecto asignado | Juegoteca comunitaria de juguetes |
| Identificador | `toy-library` |
| Frontend | React |
| Backend | PHP con Laravel |
| Base de datos propuesta | PostgreSQL |
| Contenedores | Docker |
| Primera entrega | 19 de noviembre de 2026, 6:00 p. m. |
| Sustentación | 26 de noviembre de 2026, desde las 6:00 p. m. |
| Segunda oportunidad | 3 de diciembre de 2026, 6:00 p. m. |

## 3. Verificación de documentación

El acta exige una especificación propia adaptada al dominio Toy Library.

| Documento | Estado |
|---|---|
| `constitution.md` | Pendiente de verificar |
| `spec.md` | Pendiente de verificar |
| `plan.md` | Pendiente de verificar |
| `architecture.md` | Pendiente de verificar |
| `data-model.md` | Pendiente de verificar |
| `api-contract.md` | Pendiente de verificar |
| `tasks.md` | Pendiente de verificar |
| Mínimo 3 ADR | Pendiente de verificar |
| Coherencia entre documentos | Pendiente de verificar |

### Criterios de verificación

- [ ] Los documentos están ubicados en `docs/`.
- [ ] La especificación está adaptada a juguetes y familias.
- [ ] Las diez historias de usuario están documentadas.
- [ ] Las seis reglas de negocio están documentadas.
- [ ] Los cinco requisitos no funcionales están documentados.
- [ ] Los endpoints equivalentes están definidos.
- [ ] Las 23 tareas están documentadas.
- [ ] Existen al menos tres ADR.
- [ ] Los documentos no presentan contradicciones.
- [ ] La documentación corresponde al sistema implementado.

## 4. Verificación de las reglas del negocio

### RN-01. Límite de juguetes

- [ ] Una familia puede tener máximo tres juguetes pendientes.
- [ ] El límite se calcula considerando todos los préstamos pendientes.
- [ ] No se permite registrar un préstamo que supere ese límite.
- [ ] Existe una prueba automatizada que demuestra esta restricción.

### RN-02. Plazo de devolución

- [ ] Cada préstamo tiene un plazo de nueve días.
- [ ] Las fechas se calculan correctamente.
- [ ] Las fechas se almacenan en UTC.
- [ ] Existe una prueba automatizada del vencimiento.

### RN-03. Bloqueo por préstamos vencidos

- [ ] El sistema identifica juguetes vencidos no devueltos.
- [ ] Una familia con vencidos no puede recibir nuevos préstamos.
- [ ] La restricción se elimina después de devolver los vencidos.
- [ ] Existe una prueba automatizada de esta condición.

### RN-04. Integridad del inventario

- [ ] Las unidades disponibles nunca son negativas.
- [ ] Las unidades disponibles no superan las unidades totales.
- [ ] Los préstamos descuentan correctamente el inventario.
- [ ] Las devoluciones restituyen las unidades.
- [ ] Existen restricciones de integridad en la base de datos.

### RN-05. Conservación del historial

- [ ] Los préstamos no pueden eliminarse.
- [ ] Las devoluciones no pueden eliminarse.
- [ ] Los datos congelados de las líneas de préstamo permanecen inmutables.
- [ ] Los cambios del catálogo no modifican el historial.
- [ ] Los juguetes únicamente tienen baja lógica.

### RN-06. Concurrencia

- [ ] Se controlan las solicitudes simultáneas.
- [ ] Dos operadores no pueden prestar la misma última unidad.
- [ ] Una operación se completa correctamente.
- [ ] La segunda operación recibe HTTP 409.
- [ ] La prueba se ejecuta con PostgreSQL real.
- [ ] El inventario permanece consistente.

## 5. Verificación de las diez historias de usuario

| Historia | Funcionalidad | Estado |
|---|---|---|
| HU-01 | Autenticación | Pendiente |
| HU-02 | Gestión de promotores | Pendiente |
| HU-03 | Gestión de juguetes | Pendiente |
| HU-04 | Búsqueda de juguetes | Pendiente |
| HU-05 | Gestión de familias | Pendiente |
| HU-06 | Registro de préstamos | Pendiente |
| HU-07 | Registro de devoluciones | Pendiente |
| HU-08 | Historial de préstamos | Pendiente |
| HU-09 | Consulta de vencidos | Pendiente |
| HU-10 | Juguetes más solicitados | Pendiente |

### Criterios

- [ ] Las diez historias están implementadas.
- [ ] Cada historia cumple sus criterios de aceptación.
- [ ] Las funcionalidades pueden utilizarse desde React.
- [ ] Los endpoints responden según `api-contract.md`.
- [ ] Existen pruebas automatizadas de las funcionalidades.
- [ ] Se registraron evidencias de funcionamiento.

## 6. Verificación de los cinco requisitos no funcionales

| Código | Requisito | Estado |
|---|---|---|
| RNF-01 | Rendimiento | Pendiente |
| RNF-02 | Concurrencia | Pendiente |
| RNF-03 | Seguridad | Pendiente |
| RNF-04 | Consistencia de respuestas HTTP | Pendiente |
| RNF-05 | Despliegue reproducible | Pendiente |

### Rendimiento

- [ ] Se ejecutó una prueba reproducible de consultas.
- [ ] Se registraron tiempos de respuesta.
- [ ] Los resultados cumplen el criterio establecido en `spec.md`.

### Concurrencia

- [ ] Se ejecutaron solicitudes simultáneas.
- [ ] Una solicitud recibió HTTP 201.
- [ ] La otra recibió HTTP 409.
- [ ] No existieron unidades negativas.

### Seguridad

- [ ] Las contraseñas se almacenan mediante hashes seguros.
- [ ] Las rutas protegidas requieren autenticación.
- [ ] Los roles tienen permisos diferenciados.
- [ ] No se publican credenciales.
- [ ] Los datos sensibles no aparecen en respuestas ni registros.

### Respuestas HTTP

- [ ] Los errores tienen una estructura uniforme.
- [ ] Los códigos HTTP coinciden con el contrato.
- [ ] Los mensajes dirigidos al usuario están en español.
- [ ] No se exponen detalles internos del servidor.

### Despliegue

- [ ] El sistema inicia mediante un solo comando.
- [ ] Laravel funciona correctamente.
- [ ] React se comunica con la API.
- [ ] PostgreSQL conserva los datos.
- [ ] Las migraciones funcionan.
- [ ] El sembrador registra datos mediante la API.

## 7. Verificación del backend Laravel

- [ ] El backend está ubicado en `api/`.
- [ ] Laravel está configurado.
- [ ] La conexión a PostgreSQL funciona.
- [ ] Existen las seis tablas.
- [ ] Están registradas las cinco categorías iniciales.
- [ ] Funcionan las migraciones.
- [ ] Se implementaron las reglas del dominio.
- [ ] Los controladores respetan los permisos.
- [ ] Los préstamos utilizan transacciones.
- [ ] Las devoluciones actualizan el inventario.
- [ ] Los errores HTTP coinciden con el contrato.
- [ ] La arquitectura respeta la separación de responsabilidades.

## 8. Verificación del frontend React

- [ ] El frontend se encuentra en `app/`.
- [ ] La aplicación inicia correctamente.
- [ ] Existe una interfaz de inicio de sesión.
- [ ] El catálogo permite consultar juguetes.
- [ ] Las familias pueden registrarse mediante usuarios autorizados.
- [ ] Se pueden registrar préstamos.
- [ ] Se pueden registrar devoluciones.
- [ ] Se muestra el historial.
- [ ] Se identifican los préstamos vencidos.
- [ ] Se muestra el reporte administrativo.
- [ ] Se respetan los permisos según el rol.
- [ ] Los mensajes están en español.

## 9. Verificación de la página pública

El acta exige una página estática que presente el producto y no la prueba.

- [ ] La página se encuentra en `page/`.
- [ ] Presenta Toy Library como producto.
- [ ] Explica su propósito.
- [ ] Describe las funcionalidades principales.
- [ ] Tiene un diseño adaptable.
- [ ] Se visualiza en computador.
- [ ] Se visualiza en dispositivos móviles.
- [ ] No presenta el sitio como una prueba técnica.

## 10. Verificación de infraestructura

- [ ] Existe la carpeta `infra/`.
- [ ] Los contenedores están configurados.
- [ ] El backend puede ejecutarse en Docker.
- [ ] El frontend puede ejecutarse en Docker.
- [ ] PostgreSQL puede ejecutarse en Docker.
- [ ] Los servicios se comunican correctamente.
- [ ] El sistema inicia con un solo comando.
- [ ] Los archivos `.env` no están publicados.
- [ ] La configuración permite repetir el despliegue.

## 11. Verificación del sembrador

- [ ] Existe un sembrador en `tool/`.
- [ ] Se conecta a la API.
- [ ] Utiliza autenticación cuando corresponde.
- [ ] Registra juguetes.
- [ ] Registra familias.
- [ ] Registra préstamos.
- [ ] No utiliza inserciones SQL directas para los datos de demostración.
- [ ] Se comprobó que los datos quedaron registrados correctamente.

## 12. Verificación de las 23 tareas

Las tareas deberán comprobarse mediante las evidencias establecidas en `tasks.md`.

| Tarea | Estado | Evidencia |
|---|---|---|
| T-01 | Pendiente | Sin verificar |
| T-02 | Pendiente | Sin verificar |
| T-03 | Pendiente | Sin verificar |
| T-04 | Pendiente | Sin verificar |
| T-05 | Pendiente | Sin verificar |
| T-06 | Pendiente | Sin verificar |
| T-07 | Pendiente | Sin verificar |
| T-08 | Pendiente | Sin verificar |
| T-09 | Pendiente | Sin verificar |
| T-10 | Pendiente | Sin verificar |
| T-11 | Pendiente | Sin verificar |
| T-12 | Pendiente | Sin verificar |
| T-13 | Pendiente | Sin verificar |
| T-14 | Pendiente | Sin verificar |
| T-15 | Pendiente | Sin verificar |
| T-16 | Pendiente | Sin verificar |
| T-17 | Pendiente | Sin verificar |
| T-18 | Pendiente | Sin verificar |
| T-19 | Pendiente | Sin verificar |
| T-20 | Pendiente | Sin verificar |
| T-21 | Pendiente | Sin verificar |
| T-22 | Pendiente | Sin verificar |
| T-23 | Pendiente | Sin verificar |

### Criterios

- [ ] Las 23 tareas están implementadas.
- [ ] Todas cuentan con evidencias.
- [ ] Las dependencias se han respetado.
- [ ] Las pruebas necesarias se ejecutaron.
- [ ] No quedan tareas pendientes al momento de la entrega.

## 13. Verificación de pruebas automatizadas

El acta exige un mínimo de quince archivos de pruebas automatizadas.

- [ ] Existen al menos quince archivos de pruebas.
- [ ] Las pruebas unitarias funcionan.
- [ ] Las pruebas de integración funcionan.
- [ ] Las pruebas HTTP funcionan.
- [ ] Se comprueban los límites de préstamos.
- [ ] Se comprueban los vencimientos.
- [ ] Se comprueban las devoluciones.
- [ ] Se comprueba la integridad del inventario.
- [ ] Se comprueban los permisos.
- [ ] Se comprueba la concurrencia.
- [ ] Se registraron los resultados de ejecución.

**Cantidad de archivos de pruebas:** pendiente de verificar.

**Resultado de ejecución:** pendiente de verificar.

## 14. Verificación de commits

El acta establece condiciones específicas sobre el historial de Git.

- [ ] Los commits están distribuidos en ocho o más días diferentes.
- [ ] Existen al menos cuatro commits por cada subcarpeta exigida.
- [ ] Los commits son pequeños y descriptivos.
- [ ] No se ha reescrito el historial.
- [ ] No se alteraron artificialmente las fechas.
- [ ] No se publicaron archivos `.env`.
- [ ] No se publicaron carpetas `node_modules`.
- [ ] Se utilizaron ramas y Pull Requests según las convenciones.
- [ ] Los commits corresponden a trabajo real.

### Registro de cumplimiento

| Indicador | Requerido | Verificado |
|---|---|---|
| Días con commits | 8 o más | Pendiente |
| Commits por subcarpeta | 4 o más | Pendiente |
| Historial sin reescritura | Sí | Pendiente |
| Archivos sensibles excluidos | Sí | Pendiente |

**Nota:** el historial deberá conservar evidencias reales de los commits. Las integraciones mediante Pull Requests deberán revisarse para comprobar que no se pierda la trazabilidad requerida.

## 15. Verificación de estructura de entrega

La estructura obligatoria es:

```text
03-execute/
└── NN-week/
    └── plan-mejoramiento/
        ├── docs/
        ├── api/
        ├── app/
        ├── page/
        ├── infra/
        ├── tool/
        ├── README.md
        └── Acta_13_A13_version_publica.pdf
```

### Lista de verificación

- [ ] El proyecto está en el fork correspondiente de `ADSO-3239137`.
- [ ] Se utilizó la carpeta de la semana en curso.
- [ ] Existe la carpeta `plan-mejoramiento/`.
- [ ] Existe `docs/`.
- [ ] Existe `api/`.
- [ ] Existe `app/`.
- [ ] Existe `page/`.
- [ ] Existe `infra/`.
- [ ] Existe `tool/`.
- [ ] Se adjuntó el PDF público del acta.
- [ ] El PDF no contiene el nombre ni el documento de identidad del aprendiz.
- [ ] Existe el archivo `README.md`.
- [ ] El README comienza con la frase obligatoria del acta.

### Frase obligatoria del README

Este proyecto es la solución del plan de mejoramiento del aprendiz A13, ficha 3239137, dominio toy-library

## 16. Verificación de originalidad

El acta establece penalizaciones por similitud con otros trabajos.

- [ ] La especificación está adaptada al dominio Toy Library.
- [ ] El código corresponde a las reglas de la juegoteca.
- [ ] No existen funcionalidades copiadas de otro dominio que no correspondan al proyecto.
- [ ] La documentación coincide con el código.
- [ ] El aprendiz puede explicar las decisiones técnicas.
- [ ] Se revisaron las similitudes innecesarias con proyectos de otros aprendices.

El acta establece las siguientes consecuencias:

- Similitud igual o superior al 98 %: pérdida de los informes 3, 4 y 5.
- Similitud desde el 85 %: reducción a la mitad de esos informes.

Se deberán conservar las características propias del dominio y desarrollar una solución verificable.

## 17. Verificación del proyecto formativo Friend Point

El plan exige una segunda revisión del proyecto formativo del equipo.

**Proyecto:** Friend Point (Service Marketplace Platform).

- [ ] La documentación del proyecto formativo está disponible.
- [ ] Se revisaron las secciones 00 a 06.
- [ ] Las secciones están completas.
- [ ] Se comprobó la congruencia entre documentos.
- [ ] La documentación cumple la rúbrica de evaluación.
- [ ] Se obtuvo una calificación igual o superior a 70/100.

La aprobación de Toy Library no reemplaza esta segunda evaluación.

Ambas revisiones son obligatorias.

## 18. Criterios de aprobación

Para cumplir el plan de mejoramiento deben aprobarse dos revisiones.

### 18.1. Revisión Toy Library

Se utilizarán los siete informes de evaluación:

1. Commits.
2. Cambios en documentación.
3. Congruencia de especificación.
4. Calidad del sitio público.
5. Completitud de las 23 tareas.
6. Funcionalidades fuera de la especificación.
7. Código en español.

La revisión deberá obtener:

- Acumulado mínimo del 70 %.
- Ningún informe con calificación inferior a 50.
- Juicio A.

### 18.2. Revisión Friend Point

La documentación del proyecto formativo deberá obtener mínimo 70/100.

### 18.3. Condición final

**Las dos revisiones deben aprobarse.**

Aprobar únicamente una de ellas no cumple el plan de mejoramiento.

## 19. Preparación para la sustentación

La sustentación será individual y en vivo.

El aprendiz deberá demostrar que comprende el proyecto y puede modificarlo.

### Lista de preparación

- [ ] Puedo ejecutar el proyecto mediante Docker.
- [ ] Puedo explicar la estructura de carpetas.
- [ ] Puedo explicar la arquitectura.
- [ ] Puedo explicar las seis tablas.
- [ ] Puedo explicar las diez historias de usuario.
- [ ] Puedo explicar las seis reglas de negocio.
- [ ] Puedo explicar los cinco requisitos no funcionales.
- [ ] Puedo demostrar el inicio de sesión.
- [ ] Puedo registrar un juguete.
- [ ] Puedo registrar una familia.
- [ ] Puedo realizar un préstamo.
- [ ] Puedo registrar una devolución.
- [ ] Puedo demostrar el límite de tres juguetes.
- [ ] Puedo demostrar la restricción por vencimientos.
- [ ] Puedo demostrar el control de concurrencia.
- [ ] Puedo ejecutar las pruebas automatizadas.
- [ ] Puedo explicar las decisiones arquitectónicas.
- [ ] Puedo realizar un cambio solicitado durante la sustentación.

## 20. Fechas y compromisos

| Actividad | Fecha |
|---|---|
| Inicio del plan | 5 de noviembre de 2026 |
| Primera entrega | 19 de noviembre de 2026, 6:00 p. m. |
| Sustentación individual | 26 de noviembre de 2026, desde las 6:00 p. m. |
| Segunda oportunidad, si corresponde | 3 de diciembre de 2026, 6:00 p. m. |

El incumplimiento de la segunda oportunidad podrá dar lugar al reporte ante Coordinación Académica, conforme al procedimiento y las garantías establecidos en el reglamento aplicable.

## 21. Registro final de cumplimiento

| Elemento | Estado |
|---|---|
| Documentación | Pendiente de verificar |
| Diez historias de usuario | Pendiente |
| Seis reglas de negocio | Pendiente |
| Cinco requisitos no funcionales | Pendiente |
| Endpoints del contrato | Pendiente |
| Veintitrés tareas | Pendiente |
| Quince archivos de pruebas | Pendiente |
| Frontend React | Pendiente |
| Backend Laravel | Pendiente |
| Base de datos | Pendiente |
| Docker | Pendiente |
| Sembrador por API | Pendiente |
| Página pública | Pendiente |
| Historial de commits | Pendiente |
| PDF público del acta | Pendiente |
| README obligatorio | Pendiente |
| Sustentación individual | Pendiente |
| Revisión Friend Point | Pendiente |

**Resultado general:** pendiente de verificación.

Este documento deberá actualizarse conforme se obtengan evidencias reales de las tareas implementadas.

---

**Toy Library — Verificación del Acta No. 13 — Plan de Mejoramiento A13 — Ficha 3239137.**
