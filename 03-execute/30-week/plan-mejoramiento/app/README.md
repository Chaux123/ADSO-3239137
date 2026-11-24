
# Toy Library — Frontend

Aplicación web desarrollada con React y Vite para gestionar una biblioteca comunitaria de juguetes.

Este frontend forma parte del proyecto Toy Library, correspondiente al Plan de Mejoramiento — Acta A13.

## 1. Tecnologías

- React 18
- React Router 6
- Vite 5
- JavaScript
- CSS
- API REST de Laravel
- Autenticación mediante JWT

## 2. Funcionalidades

### Catálogo público

- Consultar juguetes registrados.
- Buscar juguetes por nombre o marca.
- Filtrar por categoría.
- Visualizar fotografías.
- Consultar unidades disponibles.

### Autenticación

- Iniciar sesión con usuario y contraseña.
- Identificar los roles administrador y promotor.
- Cerrar sesión.
- Proteger rutas según el rol.

### Gestión de familias

- Registrar familias.
- Consultar familias registradas.
- Buscar por nombre o documento.
- Consultar información de una familia.

### Gestión de préstamos

- Registrar préstamos de 1 a 3 juguetes.
- Consultar préstamos registrados.
- Mostrar fechas de vencimiento.
- Registrar devoluciones individuales.

El backend valida las reglas de negocio:

- Máximo 3 juguetes pendientes por familia.
- Duración del préstamo de 9 días.
- Bloqueo de nuevos préstamos cuando existen vencimientos.
- Inventario disponible obligatorio.
- Protección ante operaciones concurrentes.

### Administración

Los administradores pueden:

- Registrar juguetes.
- Modificar datos e inventario.
- Retirar juguetes.
- Subir y eliminar fotografías.
- Registrar promotores.
- Consultar reportes de juguetes más prestados.

## 3. Estructura

```text
app/
├── .env.example
├── .gitignore
├── index.html
├── package.json
├── vite.config.js
├── README.md
└── src/
    ├── App.jsx
    ├── main.jsx
    ├── components/
    │   └── ProtectedRoute.jsx
    ├── context/
    │   └── AuthContext.jsx
    ├── pages/
    │   ├── CatalogPage.jsx
    │   ├── FamilyPage.jsx
    │   ├── LoanPage.jsx
    │   ├── LoginPage.jsx
    │   ├── PromoterPage.jsx
    │   ├── ReportPage.jsx
    │   └── ToyManagementPage.jsx
    ├── services/
    │   └── api.js
    └── styles/
        └── global.css
```

## 4. Requisitos para ejecutar

Se necesita:

- Node.js compatible con Vite 5.
- npm.
- Backend Laravel configurado.
- PostgreSQL para el backend.

Las dependencias PHP y Composer corresponden al backend, no al frontend React.

## 5. Instalación

Abrir una terminal dentro de la carpeta `app`.

Instalar las dependencias:

```bash
npm install
```

Copiar la configuración de ejemplo:

En Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

En Linux:

```bash
cp .env.example .env
```

## 6. Configuración

El archivo `.env` debe contener:

```env
VITE_API_URL=/api/v1
```

Durante el desarrollo, Vite redirige las solicitudes `/api` hacia:

```text
http://localhost:8000
```

El backend Laravel debe estar ejecutándose en ese puerto.

No almacenar contraseñas ni secretos JWT en variables `VITE_`.

## 7. Ejecutar el frontend

Iniciar el servidor de desarrollo:

```bash
npm run dev
```

Abrir en el navegador:

```text
http://localhost:5173
```

## 8. Compilar para producción

Ejecutar:

```bash
npm run build
```

El resultado se genera en la carpeta:

```text
dist/
```

La configuración de producción debe dirigir las solicitudes `/api/v1` al backend correspondiente.

## 9. Rutas de la aplicación

| Ruta | Acceso |
|---|---|
| `/` | Público |
| `/catalogo` | Público |
| `/login` | Público |
| `/familias` | Administrador y promotor |
| `/prestamos` | Administrador y promotor |
| `/administrar-juguetes` | Administrador |
| `/administrar-promotores` | Administrador |
| `/reportes` | Administrador |

## 10. Seguridad

- El frontend envía el token JWT en el encabezado Authorization.
- Los tokens se almacenan temporalmente en sessionStorage en esta implementación.
- Las rutas administrativas requieren el rol correspondiente.
- Laravel verifica la autenticación y autorización de cada operación protegida.
- Las contraseñas no deben almacenarse en el frontend.

Antes de producción se debe revisar la estrategia de almacenamiento de tokens y seguridad de sesiones.

## 11. Estado del proyecto

Los archivos principales del frontend están preparados.

Pendiente de verificación:

- Instalación de dependencias.
- Compilación de React.
- Integración con la API Laravel.
- Pruebas funcionales.
- Validación de permisos.
- Pruebas de errores y disponibilidad.
- Correcciones identificadas durante las pruebas.

La existencia del código no significa que la aplicación haya sido ejecutada o validada.

## 12. Proyecto académico

Proyecto: Toy Library

Programa: Análisis y Desarrollo de Software (ADSO)

Institución: SENA

Ficha: 3239137

Evidencia: Plan de Mejoramiento — Acta A13
