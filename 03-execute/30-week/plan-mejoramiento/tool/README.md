
# Toy Library — Herramientas de desarrollo

## 1. Descripción

Esta carpeta contiene herramientas auxiliares para el proyecto Toy Library, correspondiente al Plan de Mejoramiento Individual — Acta A13.

Su objetivo es facilitar la carga de datos de ejemplo y la verificación de la API REST desarrollada con Laravel.

## 2. Objetivo

Proporcionar una herramienta que permita crear información de prueba para comprobar el funcionamiento del sistema.

Los datos de ejemplo permitirán trabajar con:

- Categorías de juguetes.
- Juguetes.
- Familias.
- Préstamos.
- Devoluciones.

## 3. Estructura prevista

    tool/
    ├── README.md
    ├── .env.example
    ├── package.json
    └── seed-api.js

## 4. Tecnologías

- Node.js
- JavaScript
- API REST de Laravel
- JSON
- Autenticación JWT

## 5. Datos de ejemplo

La herramienta permitirá preparar información para comprobar:

- Registro de familias.
- Registro de juguetes.
- Disponibilidad del inventario.
- Préstamos de hasta tres juguetes.
- Devoluciones individuales.
- Consultas de información.

Las categorías iniciales serán creadas por los seeders de Laravel:

1. Bebés.
2. Construcción.
3. Didácticos.
4. Exterior.
5. Simbólicos.

## 6. Requisitos

Para utilizar la herramienta se necesita:

- Node.js.
- API Laravel ejecutándose.
- PostgreSQL configurado.
- Categorías iniciales cargadas.
- Una cuenta de administrador válida.

La herramienta también podrá ejecutarse en un contenedor Node.js preparado para el proyecto.

## 7. Configuración

Crear un archivo .env local a partir de .env.example.

Las variables previstas son:

    API_BASE_URL=http://localhost:8000/api/v1
    ADMIN_USERNAME=admin
    ADMIN_PASSWORD=replace_with_local_password

Las credenciales deben corresponder a un administrador registrado en Laravel.

No subir contraseñas reales al repositorio.

## 8. Instalación

Abrir una terminal dentro de la carpeta tool.

Instalar las dependencias:

    npm install

## 9. Ejecución

Con Laravel y PostgreSQL funcionando, ejecutar:

    npm run seed

Este comando registra por la API 40 juguetes, 10 familias y 15 préstamos de demostración. En una segunda ejecución reconoce los registros creados. Los préstamos pendientes se podrán consultar como vencidos una vez transcurridos nueve días.

## 10. Precauciones

- Utilizar únicamente una base de datos de desarrollo o pruebas.
- No ejecutar cargas de prueba en producción.
- No publicar credenciales administrativas.
- Evitar registrar datos personales reales.
- Identificar los registros generados para evitar duplicados.
- Comprobar los resultados de las solicitudes HTTP.
- No eliminar registros existentes sin autorización.

## 11. Relación con la API

La herramienta utilizará los endpoints de Laravel:

    POST /api/v1/auth/login
    GET  /api/v1/categories
    POST /api/v1/toys
    POST /api/v1/families
    POST /api/v1/loans

Las solicitudes protegidas enviarán el token JWT obtenido mediante el inicio de sesión.

Los administradores podrán crear juguetes y los usuarios autenticados con permisos podrán registrar familias y préstamos.

## 12. Estado del desarrollo

El sembrador se ejecutó contra la API de desarrollo y se repitió para comprobar que reconoce los datos existentes. Los resultados y las condiciones de las pruebas están en `../docs/evidencia-pruebas.md`.

## 13. Información académica

Proyecto: Toy Library

Evidencia: Plan de Mejoramiento Individual — Acta A13

Programa: Análisis y Desarrollo de Software (ADSO)

Institución: SENA

Ficha: 3239137
