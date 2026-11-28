# Evidencia de verificación de Toy Library

Fecha: 26 de noviembre de 2026.

- PostgreSQL aislado, PHP 8.3 y Laravel: 132 pruebas aprobadas, 355 aserciones.
- Dos procesos HTTP independientes compitieron por la última unidad: PostgreSQL mostró ambos esperando el bloqueo; las respuestas fueron 201 y 409, y el inventario terminó en cero.
- El ciclo completo de préstamo, cambio de nombre, devolución, repetición de devolución e informe conservó el nombre histórico y restituyó una unidad.
- El sembrador por API creó 40 juguetes, 10 familias y 15 préstamos en una base vacía. Una segunda ejecución creó cero duplicados.
- React compiló con Vite 7 y npm audit no informó vulnerabilidades en la instalación comprobada.

Estas comprobaciones no constituyen una calificación oficial ni prueban todos los criterios de aceptación. Los préstamos generados vencen después de nueve días reales; no se alteraron fechas para simular vencimiento.

La prueba de concurrencia requiere PostgreSQL y ejecuta migraciones en la base de pruebas. Usar exclusivamente una base desechable independiente; nunca apuntarla a datos reales. Configurar DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, JWT_SECRET y APP_KEY para el entorno de pruebas antes de ejecutar `php artisan test`.

- La carga, consulta y eliminación de fotografías se verificó con una solicitud multipart de navegador.

- El reporte por fechas se verificó con préstamos dentro y fuera del intervalo y con fechas invertidas.

- La consulta de vencidos y la prohibición de nuevos préstamos por mora se probaron con tiempo simulado.

- Se observaron en navegador el ingreso, catálogo con paginación, listado de préstamos y reporte con fechas. No se probaron todos los recorridos visuales.

- PostgreSQL rechazó inventario negativo y permitió reutilizar una referencia tras la baja lógica, con la nueva migración.

- Se verificó el límite acumulado de tres juguetes entre préstamos y la recuperación del cupo al devolver uno.

- Se probó el reemplazo de imágenes, el rechazo de 3 MB y de formato inválido. Se observó la página pública en escritorio y a 390 × 844, incluido el menú móvil.

- Se aceptaron PNG de exactamente 2 MB y JPEG; PostgreSQL rechazó totales inválidos, roles inválidos, claves foráneas inexistentes y referencias activas duplicadas.

- Una copia sin vendor ni node_modules arrancó con docker compose up --build -d; salud, acceso administrativo, cinco categorías y frontend respondieron correctamente tras instalar dependencias.

- El catálogo impidió retirar un juguete prestado y ocultó el juguete después de su devolución y baja lógica.

- En la prueba del ciclo integral se verificaron los extremos inclusivos de fechas del reporte y el filtro por estado cerrado (46 aserciones en esa prueba).

- Los objetos de valor ToyReference y Quantity se integraron en la validación de referencias e inventario y se probaron con valores válidos e inválidos.

- Se comprobó que la batería rechaza una base con nombre distinto antes de ejecutar las pruebas; se verificaron credenciales, token alterado y permisos efectivos de un promotor.
- Compose esperó a que la API respondiera antes de iniciar React.

- Desde React se registró una familia, se realizó un préstamo y se devolvió el juguete; la pantalla mostró la restitución del inventario. Se observó la pantalla de préstamos en 390 × 844.

- Las entidades Toy, Loan, LoanItem, Family y Promoter participan en la validación o en el cálculo de estados; la batería completa pasó tras integrarlas.

- Los controladores de catálogo, familias, promotores, autenticación, préstamos y reportes delegan sus consultas a adaptadores; una prueba automatizada comprueba que no usan DB directamente.

- Se probaron desde la interfaz los registros de juguetes y promotores. Las consultas del catálogo y las imágenes requieren autenticación, conforme al contrato; los rechazos sin sesión se comprobaron con pruebas HTTP.
- Los errores de validación incluyen mensajes en español y códigos específicos para juguetes repetidos y rangos de fechas inválidos.
- El promotor creado inició sesión en React, vio una imagen protegida y recibió acceso denegado al intentar entrar a reportes. La API rechazó tokens expirados y nombres de usuario duplicados; se comprobó el hash de contraseña almacenado.
- Tras el primer arranque se comprobó que un reinicio de la API usa las dependencias existentes y vuelve a quedar saludable.
- Se observó un préstamo vencido en React con datos de prueba; después se restauró su fecha. Se revisó también el contenido de las secciones de la página pública servida sin la API.
- Tras proteger el catálogo se actualizó y ejecutó nuevamente el sembrador con autenticación: encontró las 10 familias y los 40 juguetes existentes, y no creó préstamos duplicados.
- El registro de familias utiliza un caso de uso y un puerto de persistencia; una prueba arquitectónica comprueba su independencia de adaptadores. Se verificó que los identificadores malformados devuelven 404 sin provocar errores de PostgreSQL.
- El inicio de sesión usa un caso de uso y un puerto de persistencia de promotores; la batería completa volvió a pasar tras el cambio.
- La creación de promotores también usa un caso de uso y el puerto de persistencia; pasaron la batería completa y las comprobaciones arquitectónicas posteriores.
- La extensión del rol de cliente se probó en PostgreSQL aislado: 133 pruebas y 373 aserciones aprobadas. Se verificó el ingreso, la consulta de catálogo y el rechazo de operaciones reservadas al personal.
