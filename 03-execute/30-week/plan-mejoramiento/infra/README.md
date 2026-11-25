
# Toy Library — Infraestructura Docker

## 1. Descripción

Esta carpeta contiene la configuración de infraestructura del proyecto Toy Library, correspondiente al Plan de Mejoramiento Individual — Acta A13.

Docker Compose permite ejecutar los servicios principales del sistema dentro de contenedores.

Servicios incluidos:

- PostgreSQL: base de datos.
- Laravel: API REST.
- React y Vite: frontend.

Esta configuración está destinada al desarrollo local.

## 2. Tecnologías

- Docker
- Docker Compose
- PHP 8.3
- Laravel 12
- Composer 2
- PostgreSQL 16
- Node.js 22
- React
- Vite

## 3. Estructura

    infra/
    ├── .env.example
    ├── .gitignore
    ├── README.md
    ├── docker-compose.yml
    └── docker/
        ├── php/
        │   └── Dockerfile
        └── node/
            └── Dockerfile

## 4. Requisitos

Para ejecutar el sistema se necesita:

- Docker Desktop instalado.
- Docker Compose disponible.
- Repositorio Toy Library descargado.
- Docker Desktop en funcionamiento.

No es obligatorio instalar PHP, Composer o Node.js directamente en Windows si se utilizan los contenedores.

## 5. Configurar variables de entorno

Abrir una terminal dentro de la carpeta infra.

En PowerShell:

    Copy-Item .env.example .env

Abrir el archivo .env y cambiar el valor de DB_PASSWORD por una contraseña segura.

Ejemplo:

    DB_PASSWORD=replace_with_a_strong_local_password

No subir el archivo .env a GitHub.

## 6. Configurar la API Laravel

El backend se encuentra en:

    ../api/

Antes de iniciar Laravel, preparar su archivo .env.

Desde la carpeta infra, ejecutar en PowerShell:

    Copy-Item ../api/.env.example ../api/.env

Verificar que la configuración de PostgreSQL sea:

    DB_CONNECTION=pgsql
    DB_HOST=db
    DB_PORT=5432
    DB_DATABASE=toy_library
    DB_USERNAME=toy_library

La variable DB_PASSWORD de Laravel debe coincidir con la contraseña configurada en infra/.env.

También debe existir una clave APP_KEY válida.

La clave JWT debe configurarse con un valor seguro.

No publicar claves ni contraseñas reales en el repositorio.

## 7. Construir los contenedores

Desde la carpeta infra, ejecutar:

    docker compose build

Este comando construye las imágenes para Laravel y React.

PostgreSQL utiliza la imagen oficial definida en Docker Compose.

## 8. Iniciar los servicios

Ejecutar:

    docker compose up -d

Consultar el estado:

    docker compose ps

Consultar los registros:

    docker compose logs -f

Para revisar solamente Laravel:

    docker compose logs -f api

Para revisar React:

    docker compose logs -f frontend

Para revisar PostgreSQL:

    docker compose logs -f db

## 9. Preparar Laravel

Cuando el contenedor API esté funcionando, generar la clave de Laravel si todavía no existe:

    docker compose exec api php artisan key:generate

Las migraciones deben ejecutarse únicamente después de comprobar la configuración de la base de datos.

Ejecutar:

    docker compose exec api php artisan migrate

Después cargar los datos iniciales:

    docker compose exec api php artisan db:seed

Estos comandos requieren que las migraciones y los seeders del proyecto estén correctamente implementados.

No ejecutar migrate:fresh sobre una base de datos que contenga información que deba conservarse.

## 10. Acceder a los servicios

Frontend React:

    http://localhost:5173

Backend Laravel:

    http://localhost:8000

Estado de la API:

    http://localhost:8000/api/v1/health

PostgreSQL:

    Host: localhost
    Port: 5432
    Database: toy_library
    User: toy_library

La contraseña corresponde al valor configurado en infra/.env.

El puerto de PostgreSQL está publicado solamente en la interfaz local de la computadora.

## 11. Comunicación entre contenedores

Dentro de Docker, los servicios utilizan sus nombres para comunicarse.

Laravel se conecta a PostgreSQL mediante:

    DB_HOST=db

React utiliza el proxy de Vite hacia Laravel mediante:

    API_PROXY_TARGET=http://api:8000

La configuración del frontend utiliza:

    VITE_API_URL=/api/v1

El navegador envía solicitudes a Vite, y Vite las redirige hacia la API Laravel.

## 12. Ejecutar pruebas

Las pruebas automatizadas del backend se encuentran en:

    ../api/tests/

Para ejecutarlas desde la carpeta infra:

    docker compose exec api php artisan test

Antes de ejecutarlas, se debe configurar una base de datos de pruebas independiente.

Nunca ejecutar pruebas que modifiquen o eliminen datos sobre una base de producción.

El proyecto debe contar con al menos 15 archivos de pruebas automatizadas según los requisitos del Acta A13.

La existencia de esos archivos no significa que las pruebas hayan sido ejecutadas exitosamente.

## 13. Detener contenedores

Para detener los servicios:

    docker compose down

Para iniciarlos nuevamente:

    docker compose up -d

No utilizar la opción -v si se desean conservar los datos de PostgreSQL.

El comando docker compose down -v elimina los volúmenes administrados por Docker Compose y puede destruir los datos almacenados.

## 14. Seguridad

- No subir archivos .env con contraseñas reales.
- No almacenar secretos JWT en el frontend.
- No utilizar APP_DEBUG=true en producción.
- Cambiar las contraseñas de ejemplo antes de ejecutar los servicios.
- Utilizar una base de datos separada para las pruebas.
- Mantener las claves y credenciales fuera del control de versiones.
- Revisar los permisos administrativos en Laravel.
- Utilizar HTTPS en un despliegue de producción.

## 15. Estado del proyecto

Archivos de infraestructura preparados:

- docker-compose.yml
- docker/php/Dockerfile
- docker/node/Dockerfile
- .env.example
- .gitignore
- README.md

Pendiente de verificación:

- Construcción de imágenes Docker.
- Instalación de dependencias.
- Generación de claves.
- Conexión con PostgreSQL.
- Ejecución de migraciones.
- Carga de datos iniciales.
- Comunicación entre React y Laravel.
- Ejecución de pruebas automatizadas.
- Correcciones de integración.

Los comandos de este documento describen el procedimiento previsto, pero todavía no se ha verificado su ejecución.

## 16. Información académica

Proyecto: Toy Library

Evidencia: Plan de Mejoramiento Individual — Acta A13

Programa: Análisis y Desarrollo de Software (ADSO)

Institución: SENA

Ficha: 3239137
