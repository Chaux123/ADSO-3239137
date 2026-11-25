
# Toy Library — Página pública

## 1. Descripción

Toy Library es una biblioteca comunitaria de juguetes que promueve el juego, el aprendizaje y la colaboración entre las familias.

Esta página pública presenta el proyecto, sus beneficios, las categorías de juguetes y las reglas principales del sistema de préstamos.

Forma parte del Plan de Mejoramiento Individual — Acta A13 del programa Análisis y Desarrollo de Software (ADSO) del SENA.

## 2. Objetivo

Presentar Toy Library mediante una página web informativa, accesible y adaptable a diferentes dispositivos.

La página permite conocer el funcionamiento del proyecto sin necesidad de iniciar sesión.

## 3. Tecnologías utilizadas

- HTML5
- CSS3
- JavaScript
- Diseño adaptable (responsive design)
- HTML semántico
- Atributos de accesibilidad ARIA

No requiere React, Laravel ni una base de datos para mostrar su contenido estático.

## 4. Estructura del proyecto

    page/
    ├── index.html
    ├── README.md
    ├── css/
    │   └── styles.css
    └── js/
        └── main.js

## 5. Funcionalidades

### Página de inicio

Presenta Toy Library con el mensaje:

"Compartir también es jugar".

### Información del proyecto

Explica la misión y los objetivos de la biblioteca comunitaria.

### Beneficios

Presenta los beneficios del préstamo de juguetes:

- Acceso a juguetes.
- Aprendizaje mediante el juego.
- Reutilización de recursos.
- Participación familiar.

### Funcionamiento

Explica las etapas del préstamo:

1. Consultar juguetes.
2. Registrar la familia.
3. Solicitar el préstamo.
4. Devolver los juguetes.

### Categorías

Presenta las cinco categorías del sistema:

- Bebés.
- Construcción.
- Didácticos.
- Exterior.
- Simbólicos.

### Navegación adaptable

El menú se adapta a computadores, tabletas y celulares.

JavaScript permite abrirlo y cerrarlo mediante el botón de navegación.

## 6. Reglas principales del préstamo

El sistema Toy Library contempla las siguientes reglas:

- Una familia puede tener máximo tres juguetes pendientes de devolución.
- Cada préstamo tiene una duración de nueve días.
- Una familia con préstamos vencidos no puede solicitar nuevos juguetes.
- Solo pueden prestarse juguetes con unidades disponibles.
- Los promotores registran préstamos y devoluciones.
- Los administradores gestionan el inventario y los reportes.

Estas reglas se implementan y validan en la API de Laravel, no en la página pública.

## 7. Cómo visualizar la página

No es necesario instalar PHP, Composer ni Node.js.

Para visualizarla localmente:

1. Descargar o clonar el repositorio.
2. Entrar a la carpeta `page`.
3. Abrir `index.html` en un navegador web.

La página debe cargar sus estilos desde `css/styles.css` y su JavaScript desde `js/main.js`.

## 8. Diferencia entre page y app

### page/

Página pública informativa desarrollada con HTML, CSS y JavaScript.

Su objetivo es dar a conocer Toy Library.

### app/

Aplicación React conectada a la API REST de Laravel.

Permite gestionar:

- Autenticación.
- Juguetes.
- Familias.
- Préstamos.
- Devoluciones.
- Promotores.
- Reportes.

## 9. Accesibilidad

La página incluye:

- Idioma español.
- Etiquetas HTML semánticas.
- Navegación mediante teclado.
- Atributos ARIA en el menú.
- Diseño adaptable.
- Compatibilidad con preferencias de movimiento reducido.

La accesibilidad deberá comprobarse durante las pruebas finales.

## 10. Estado del desarrollo

Archivos preparados:

- `index.html`
- `css/styles.css`
- `js/main.js`
- `README.md`

Verificaciones pendientes:

- Comprobar la visualización en navegadores.
- Revisar el diseño en celulares.
- Probar el menú móvil.
- Revisar enlaces y navegación.
- Realizar correcciones finales.

## 11. Información académica

Proyecto: Toy Library

Evidencia: Plan de Mejoramiento Individual — Acta A13

Programa: Análisis y Desarrollo de Software (ADSO)

Institución: SENA

Ficha: 3239137
