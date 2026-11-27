# Revisión de las 23 tareas de Toy Library

Fecha de la revisión: 26 de noviembre de 2026. La columna de evidencia describe lo comprobado; no implica que los demás criterios estén aprobados.

| Tarea | Evidencia y estado |
|---|---|
| T-01 | Salud HTTP y arranque comprobados en Docker. |
| T-02 | Las migraciones y cinco categorías se ejecutaron en PostgreSQL. |
| T-03 | ToyReference, Quantity y DateRange implementados y probados con valores válidos e inválidos. |
| T-04 | La entidad Toy y las reglas de inventario tienen pruebas unitarias y restricciones PostgreSQL. |
| T-05 | Las entidades Loan y LoanItem calculan el estado; plazo, historial y mora están probados. |
| T-06 | Las entidades Family y Promoter normalizan y validan identidad; registro y permisos están probados. |
| T-07 | Inicio de sesión, creación real de promotor, permisos, validaciones y rechazo de duplicados comprobados. |
| T-08 | Alta y actualización comprobadas; paginación vista en navegador. Probadas la búsqueda, la baja lógica y su bloqueo cuando existen préstamos pendientes. |
| T-09 | Préstamos y descuento comprobados; mora y simultaneidad probadas. El límite acumulado de tres y la liberación de cupo tras la devolución están probados por HTTP. |
| T-10 | Devolución, doble devolución y mora comprobadas. |
| T-11 | Reporte histórico y extremos inclusivos del intervalo comprobados con PostgreSQL. |
| T-12 | Los repositorios de juguetes, familias y promotores están integrados y probados con PostgreSQL. |
| T-13 | Dos solicitudes simultáneas bloquearon la misma fila y devolvieron 201 y 409; inventario correcto. |
| T-14 | Probados contraseñas con hash, duración de ocho horas, tokens alterados y expirados, y permisos basados en el rol guardado. |
| T-15 | Carga, consulta, reemplazo y eliminación de PNG comprobados; se rechazaron 3 MB y formato inválido. También pasaron la prueba del límite exacto de 2 MB y la carga de JPG. |
| T-16 | Se corrigieron paginación, fechas, errores de validación y vencidos; se protegieron las consultas del catálogo. Falta contrastar cada campo y código HTTP del contrato. |
| T-17 | Administrador creado e inicio de sesión real comprobado. |
| T-18 | PostgreSQL rechazó inventario negativo, total inválido, disponibilidad excesiva, rol inválido, clave foránea inexistente y referencia activa duplicada. |
| T-19 | Pruebas automáticas comprueban que el dominio no dependa de Laravel ni adaptadores y que los controladores no consulten DB directamente; falta revisar todas las dependencias entre capas. |
| T-20 | Los tres servicios iniciaron desde una copia limpia con credenciales de prueba; salud, inicio de sesión, categorías y frontend respondieron. |
| T-21 | 40 juguetes, 10 familias y 15 préstamos creados por API; repetición sin duplicados. |
| T-22 | Se observaron inicio de sesión, catálogo con paginación y reporte. Se registraron familia, préstamo, devolución, juguete y promotor desde React; se comprobó el acceso del promotor, la denegación de reportes, la imagen protegida y el estado vencido. La pantalla de préstamos se observó en móvil. |
| T-23 | Página y secciones de presentación observadas en escritorio y 390 × 844; el menú móvil se abre y muestra los enlaces. El sitio se sirvió independientemente de la API. |

Resultados reproducibles y limitaciones adicionales en evidencia-pruebas.md. Las tareas no identificadas como completas conservan sus estados pendientes en tasks.md.
