# ADR-004 — Autenticación, Autorización y Seguridad del Sistema

**Proyecto:** Toy Library — Juegoteca Comunitaria de Juguetes  
**Identificador:** `toy-library`  
**Código del aprendiz:** A13  
**Ficha:** ADSO 3239137  
**Fecha:** 11 de noviembre de 2026  
**Estado:** Aceptado  
**Categoría:** Seguridad y control de acceso

## 1. Contexto

Toy Library es una aplicación web destinada a administrar juguetes, familias, promotores, préstamos y devoluciones de una juegoteca comunitaria.

El sistema permite realizar operaciones que modifican el inventario y registrar información de las familias beneficiarias.

Por esta razón, es necesario garantizar que solamente los usuarios autorizados puedan acceder a las funcionalidades correspondientes.

Los usuarios autenticados serán administradores y promotores. Las familias serán beneficiarias del servicio y no tendrán cuentas de acceso.

El proyecto utilizará React para el frontend y PHP con Laravel para el backend.

Se requiere una estrategia que permita autenticar usuarios, controlar sus permisos y proteger las credenciales utilizadas por la aplicación.

## 2. Problema identificado

Si las operaciones del sistema no cuentan con mecanismos de autenticación y autorización, podrían presentarse los siguientes problemas:

- Acceso de usuarios no autorizados.
- Modificación indebida del inventario.
- Registro de préstamos por personas sin permisos.
- Exposición de información de las familias.
- Creación de usuarios con privilegios administrativos.
- Robo o divulgación de contraseñas.
- Acceso a funcionalidades restringidas.
- Publicación accidental de credenciales en GitHub.

Se necesita una solución de seguridad que proteja los datos y permita diferenciar las funciones de cada usuario.

## 3. Decisión arquitectónica

Se decide implementar autenticación mediante **JSON Web Tokens (JWT)** y autorización basada en roles.

Las contraseñas serán almacenadas mediante hashes seguros, utilizando los mecanismos de protección proporcionados por Laravel.

La API validará la identidad y los permisos antes de ejecutar operaciones protegidas.

### 3.1. Principios de seguridad

La implementación seguirá estas condiciones:

1. Las contraseñas no se almacenarán en texto plano.
2. La autenticación se realizará mediante credenciales.
3. Los usuarios autenticados recibirán un token JWT.
4. Los tokens tendrán una duración de ocho horas.
5. Las rutas protegidas requerirán un token válido.
6. Los permisos dependerán del rol del usuario.
7. Los administradores tendrán acceso a las funciones administrativas.
8. Los promotores solamente podrán ejecutar las operaciones autorizadas.
9. Las familias no tendrán cuentas de inicio de sesión.
10. No se publicarán credenciales ni secretos en el repositorio.

## 4. Actores y roles

El sistema tendrá dos roles autenticados.

### 4.1. Administrador (`admin`)

El administrador será responsable de las funciones de gestión del sistema.

Podrá:

- Registrar promotores.
- Crear juguetes.
- Actualizar juguetes.
- Dar de baja juguetes.
- Gestionar imágenes.
- Consultar categorías.
- Consultar familias.
- Registrar familias.
- Registrar préstamos.
- Registrar devoluciones.
- Consultar el historial.
- Consultar préstamos vencidos.
- Generar reportes de juguetes más solicitados.

El administrador inicial será configurado mediante variables de entorno.

No se permitirá crear nuevos administradores desde los endpoints de la API.

### 4.2. Promotor (`promoter`)

El promotor será responsable de atender a las familias y gestionar las operaciones de préstamo.

Podrá:

- Iniciar sesión.
- Consultar categorías.
- Consultar juguetes disponibles.
- Buscar juguetes.
- Registrar familias.
- Consultar familias.
- Registrar préstamos.
- Registrar devoluciones.
- Consultar préstamos activos y vencidos.
- Consultar el historial de préstamos.

No podrá:

- Crear promotores.
- Crear o modificar juguetes.
- Dar de baja juguetes.
- Modificar imágenes del catálogo.
- Consultar reportes administrativos.

### 4.3. Familia (`family`)

Las familias serán registros de beneficiarios.

No podrán iniciar sesión ni consumir directamente los endpoints protegidos.

Las operaciones de préstamo y devolución serán registradas por administradores o promotores autenticados.

## 5. Matriz de permisos

| Operación | Administrador | Promotor |
|---|---|---|
| Iniciar sesión | Sí | Sí |
| Registrar promotores | Sí | No |
| Consultar categorías | Sí | Sí |
| Consultar juguetes | Sí | Sí |
| Crear juguetes | Sí | No |
| Actualizar juguetes | Sí | No |
| Dar de baja juguetes | Sí | No |
| Subir imágenes | Sí | No |
| Eliminar imágenes | Sí | No |
| Consultar imágenes | Sí | Sí |
| Registrar familias | Sí | Sí |
| Consultar familias | Sí | Sí |
| Registrar préstamos | Sí | Sí |
| Registrar devoluciones | Sí | Sí |
| Consultar historial | Sí | Sí |
| Consultar vencidos | Sí | Sí |
| Consultar reportes | Sí | No |

Esta matriz deberá mantenerse alineada con `api-contract.md`.

## 6. Estrategia de autenticación JWT

### 6.1. Inicio de sesión

El usuario enviará sus credenciales al endpoint:

`POST /api/v1/auth/login`

Ejemplo de solicitud:

```json
{
  "username": "promoter01",
  "password": "example_password"
}
```

Laravel verificará que el usuario exista y que la contraseña corresponda al hash almacenado.

Si las credenciales son correctas, se generará un token JWT.

### 6.2. Respuesta de autenticación

**HTTP 200**

```json
{
  "access_token": "<jwt_token>",
  "token_type": "Bearer",
  "expires_in": 28800,
  "role": "promoter"
}
```

El valor `28800` corresponde a ocho horas expresadas en segundos.

### 6.3. Contenido del token

El JWT incluirá únicamente la información necesaria para identificar y autorizar al usuario.

Campos previstos:

- `sub`: identificador del usuario.
- `role`: rol del usuario.
- `iat`: fecha de emisión.
- `exp`: fecha de expiración.

No se incluirán contraseñas, hashes ni información sensible innecesaria dentro del token.

### 6.4. Validación del token

En cada solicitud protegida, Laravel deberá comprobar:

- La firma del token.
- Su fecha de expiración.
- La identidad del usuario.
- El rol autorizado para la operación.

Los tokens inválidos, alterados o vencidos serán rechazados con HTTP 401.

La implementación deberá utilizar un mecanismo de firma seguro y validar explícitamente el algoritmo permitido.

## 7. Protección de contraseñas

Las contraseñas se almacenarán mediante funciones de hash seguras proporcionadas por Laravel.

Se utilizará el componente `Hash` del framework, configurado con un algoritmo de almacenamiento de contraseñas apropiado.

### 7.1. Registro

Al registrar un promotor:

1. Se recibirá el nombre de usuario.
2. Se validará la contraseña.
3. Se normalizará el nombre de usuario.
4. Se generará el hash de la contraseña.
5. Se almacenará en `password_hash`.
6. Se devolverán únicamente los datos públicos del usuario.

### 7.2. Inicio de sesión

Durante la autenticación:

1. Se buscará el usuario.
2. Se obtendrá su hash almacenado.
3. Se comprobará la contraseña mediante el mecanismo de verificación de Laravel.
4. Si es válida, se generará el JWT.
5. Si es incorrecta, se devolverá HTTP 401.

### 7.3. Reglas

- Nunca almacenar contraseñas en texto plano.
- Nunca incluir contraseñas en respuestas HTTP.
- Nunca devolver `password_hash`.
- No registrar contraseñas en logs.
- No utilizar hashes rápidos como MD5 o SHA-256 directamente para almacenar contraseñas.
- No publicar credenciales reales en GitHub.

## 8. Autorización basada en roles

El backend será responsable de verificar los permisos.

React podrá ocultar funcionalidades según el rol, pero esta validación visual no reemplazará los controles del servidor.

### 8.1. Middleware de autenticación

Laravel utilizará middleware para comprobar la autenticación de las solicitudes.

Las rutas protegidas requerirán:

`Authorization: Bearer <token>`

Si el token no existe o no es válido, la API devolverá HTTP 401.

### 8.2. Middleware o políticas de autorización

Las operaciones restringidas comprobarán el rol del usuario autenticado.

Por ejemplo:

- Registrar promotores: `admin`.
- Crear juguetes: `admin`.
- Actualizar juguetes: `admin`.
- Registrar préstamos: `admin` o `promoter`.
- Registrar devoluciones: `admin` o `promoter`.
- Consultar reportes: `admin`.

Si el usuario está autenticado, pero no posee los permisos necesarios, se devolverá HTTP 403.

### 8.3. Principio de mínimo privilegio

Cada usuario tendrá únicamente los permisos necesarios para realizar sus funciones.

Las operaciones no autorizadas se rechazarán desde Laravel, aunque se intente acceder directamente mediante una solicitud HTTP.

## 9. Inicialización del administrador

Toy Library requiere un administrador inicial para gestionar el sistema.

Este usuario será creado utilizando configuración de entorno.

### 9.1. Variables de entorno

Se definirán variables como:

```dotenv
ADMIN_USERNAME=admin
ADMIN_PASSWORD=replace_with_secure_password
JWT_SECRET=replace_with_secure_random_secret
```

Los valores anteriores son ejemplos y no deben utilizarse como credenciales reales.

### 9.2. Proceso de inicialización

El mecanismo de inicialización deberá:

1. Obtener las credenciales desde el entorno.
2. Validar que estén configuradas.
3. Normalizar el nombre de usuario.
4. Generar el hash de la contraseña.
5. Crear el usuario con rol `admin`.
6. Evitar duplicar el registro en ejecuciones posteriores.

No se permitirá crear administradores mediante `POST /api/v1/users`.

## 10. Seguridad de la API

La API deberá utilizar mecanismos de validación y protección para evitar operaciones indebidas.

### 10.1. Validación de entradas

Todos los datos recibidos deberán validarse antes de ejecutar los casos de uso.

Se comprobarán:

- Campos obligatorios.
- Tipos de datos.
- Longitudes permitidas.
- Formatos de UUID.
- Cantidades válidas.
- Referencias únicas.
- Documentos duplicados.
- Permisos del usuario.
- Reglas de préstamo y devolución.

### 10.2. Protección de recursos

Los controladores deberán utilizar los servicios de aplicación correspondientes.

No se permitirá modificar directamente el inventario mediante campos enviados por el cliente.

Las cantidades disponibles serán actualizadas únicamente por operaciones autorizadas del backend.

### 10.3. Errores

Las respuestas de error no deberán exponer información técnica sensible.

Se utilizará la estructura documentada en `api-contract.md`.

Ejemplo:

```json
{
  "code": "FORBIDDEN_ROLE",
  "message": "No tiene permisos para realizar esta operación.",
  "details": []
}
```

## 11. Manejo de códigos HTTP

La API deberá diferenciar entre errores de autenticación, autorización y validación.

| HTTP | Condición |
|---|---|
| 200 | Autenticación exitosa |
| 201 | Recurso creado |
| 401 | Credenciales incorrectas o token inválido |
| 403 | Usuario autenticado sin permisos |
| 404 | Recurso inexistente |
| 409 | Conflicto de negocio |
| 422 | Datos inválidos |
| 500 | Error interno del servidor |

### 11.1. Credenciales incorrectas

**HTTP 401**

```json
{
  "code": "INVALID_CREDENTIALS",
  "message": "Las credenciales proporcionadas son incorrectas.",
  "details": []
}
```

El sistema no deberá revelar si el nombre de usuario o la contraseña fue el dato incorrecto.

### 11.2. Usuario sin permisos

**HTTP 403**

```json
{
  "code": "FORBIDDEN_ROLE",
  "message": "No tiene permisos para realizar esta operación.",
  "details": []
}
```

### 11.3. Token inválido

**HTTP 401**

```json
{
  "code": "UNAUTHORIZED",
  "message": "Debe iniciar sesión para continuar.",
  "details": []
}
```

El código `UNAUTHORIZED` deberá incorporarse a la tabla de errores de `api-contract.md` para mantener ambos documentos coherentes.

## 12. Seguridad del frontend React

React será responsable de gestionar la sesión visual del usuario y comunicarse con la API.

### 12.1. Responsabilidades

- Enviar las credenciales mediante HTTPS.
- Recibir el token de autenticación.
- Adjuntar el token a las solicitudes protegidas.
- Controlar el acceso visual a las páginas.
- Mostrar mensajes de error en español.
- Evitar mostrar información administrativa a promotores.

### 12.2. Almacenamiento del token

Se propone conservar el token de acceso en memoria durante la sesión activa de la aplicación.

No se almacenará en `localStorage` ni se expondrá mediante parámetros de URL.

Esta decisión reduce la persistencia del token en el navegador, aunque no elimina los riesgos de ejecución de código malicioso.

### 12.3. Expiración

Cuando el token expire:

1. La API rechazará las solicitudes protegidas.
2. React detectará el error HTTP 401.
3. La aplicación limpiará el estado de autenticación.
4. El usuario deberá iniciar sesión nuevamente.

No se implementará renovación automática de tokens, salvo que el contrato sea modificado para incluirla.

## 13. Protección de secretos

Los secretos utilizados por el sistema deberán almacenarse fuera del repositorio público.

### 13.1. Información protegida

Se consideran secretos:

- Contraseñas.
- Claves JWT.
- Credenciales de PostgreSQL.
- Tokens de acceso.
- Claves privadas.
- Variables de entorno sensibles.

### 13.2. Archivos excluidos

El archivo `.gitignore` deberá impedir que se agreguen:

- `.env`
- `.env.*` con secretos reales.
- Archivos privados de configuración.
- Dependencias generadas.
- Carpetas `node_modules`.

Se podrán publicar archivos `.env.example` que contengan nombres de variables y valores de ejemplo no sensibles.

### 13.3. Control de versiones

Antes de realizar un commit se deberá comprobar que no se estén publicando credenciales.

Las variables reales se configurarán en el entorno local o en los contenedores.

## 14. Comunicación segura

La API deberá utilizar HTTPS cuando esté expuesta en un entorno público o de producción.

### 14.1. Protección de credenciales

Las credenciales no deberán transmitirse mediante conexiones HTTP públicas sin cifrado.

### 14.2. Configuración CORS

Laravel permitirá solicitudes únicamente desde los orígenes autorizados del frontend.

No se utilizará una configuración abierta innecesaria en producción.

### 14.3. Registro de eventos

Los registros técnicos podrán contener información necesaria para diagnosticar errores.

No deberán almacenar contraseñas, tokens completos ni secretos.

## 15. Alternativas consideradas

### Alternativa 1. Acceso sin autenticación

Permitiría ejecutar operaciones directamente desde la interfaz.

**Ventajas:**

- Implementación sencilla.
- No requiere gestión de credenciales.

**Desventajas:**

- Permite accesos no autorizados.
- No diferencia permisos.
- Expone operaciones críticas.

**Resultado:** descartada.

### Alternativa 2. Sesiones tradicionales de Laravel

Consiste en utilizar sesiones del servidor y cookies para mantener la autenticación.

**Ventajas:**

- Integración con Laravel.
- Permite invalidar sesiones desde el servidor.
- Puede utilizar cookies seguras y HttpOnly.

**Desventajas:**

- Requiere una estrategia de cookies y protección CSRF adecuada.
- Necesita configurar correctamente la comunicación entre React y Laravel.

**Resultado:** alternativa válida, pero no seleccionada para este contrato.

### Alternativa 3. Autenticación JWT

Consiste en generar tokens firmados después de validar las credenciales.

**Ventajas:**

- Se adapta a la comunicación mediante API REST.
- Permite identificar usuarios y roles.
- Facilita la integración con React.
- Permite establecer una expiración explícita.

**Desventajas:**

- Requiere proteger los tokens.
- La invalidación anticipada requiere mecanismos adicionales.
- Es necesario validar correctamente las firmas y expiraciones.
- El cliente debe gestionar la expiración de sesión.

**Resultado:** alternativa seleccionada.

## 16. Consecuencias de la decisión

### 16.1. Consecuencias positivas

- Las operaciones estarán protegidas mediante autenticación.
- Los roles permitirán limitar funcionalidades.
- Las contraseñas se conservarán mediante hashes seguros.
- La API tendrá una estrategia uniforme de control de acceso.
- React podrá identificar el rol del usuario autenticado.
- Se reducirá el riesgo de operaciones no autorizadas.

### 16.2. Consecuencias negativas

- Será necesario implementar la emisión y validación de tokens.
- El frontend deberá gestionar la expiración de sesión.
- Los secretos deberán configurarse correctamente.
- Se requerirán pruebas de autenticación y autorización.

### 16.3. Compromisos adquiridos

- Mantener la seguridad de contraseñas y tokens.
- Validar los roles en Laravel.
- No confiar únicamente en las restricciones visuales del frontend.
- Excluir secretos del repositorio.
- Implementar pruebas automatizadas de seguridad.
- Mantener los contratos HTTP actualizados.

## 17. Estrategia de pruebas

### 17.1. Prueba de autenticación correcta

**Preparación:** crear un promotor con credenciales válidas.

**Ejecución:** enviar las credenciales al endpoint de inicio de sesión.

**Resultado esperado:**

- Respuesta HTTP 200.
- Token JWT válido.
- Rol correcto.
- Duración configurada de ocho horas.

### 17.2. Prueba de credenciales incorrectas

**Preparación:** utilizar un usuario existente.

**Ejecución:** enviar una contraseña incorrecta.

**Resultado esperado:**

- HTTP 401.
- Código `INVALID_CREDENTIALS`.
- No se devuelve información sobre la contraseña correcta.

### 17.3. Prueba de token inválido

**Ejecución:** consultar un endpoint protegido utilizando un token alterado.

**Resultado esperado:**

- HTTP 401.
- No se ejecuta la operación.

### 17.4. Prueba de token expirado

**Ejecución:** utilizar un token cuya fecha de expiración haya pasado.

**Resultado esperado:**

- HTTP 401.
- El acceso es rechazado.

### 17.5. Prueba de permisos de promotor

**Preparación:** autenticar un usuario con rol `promoter`.

**Ejecución:** intentar registrar otro promotor.

**Resultado esperado:**

- HTTP 403.
- No se crea el usuario.

### 17.6. Prueba de permisos de administrador

**Preparación:** autenticar un usuario con rol `admin`.

**Ejecución:** registrar un promotor válido.

**Resultado esperado:**

- HTTP 201.
- El promotor queda registrado.
- No se devuelve `password_hash`.

### 17.7. Prueba de creación de administradores

**Ejecución:** intentar registrar un usuario con rol `admin` mediante la API.

**Resultado esperado:**

- La solicitud es rechazada.
- No se crea otro administrador.

### 17.8. Prueba de protección de contraseñas

**Ejecución:** registrar un promotor y consultar su registro en PostgreSQL.

**Resultado esperado:**

- La contraseña no aparece en texto plano.
- El hash almacenado es verificable mediante Laravel.
- Las respuestas de la API no contienen el hash.

## 18. Criterios de verificación

Esta decisión se considerará correctamente implementada cuando:

- [ ] Las contraseñas se almacenen mediante hashes seguros.
- [ ] El inicio de sesión genere tokens JWT válidos.
- [ ] Los tokens expiren después de ocho horas.
- [ ] Los endpoints protegidos exijan autenticación.
- [ ] Los tokens inválidos sean rechazados.
- [ ] Los tokens expirados sean rechazados.
- [ ] Los permisos se validen desde Laravel.
- [ ] Los promotores no puedan realizar acciones administrativas.
- [ ] No se puedan crear administradores mediante la API.
- [ ] El administrador inicial se configure mediante el entorno.
- [ ] Las credenciales reales no estén publicadas.
- [ ] Los errores HTTP sigan el contrato definido.
- [ ] Los mensajes dirigidos al usuario estén en español.
- [ ] Las pruebas automatizadas de seguridad se ejecuten correctamente.

## 19. Relación con otros documentos

Este ADR deberá mantenerse alineado con:

- `constitution.md`: principios de seguridad.
- `spec.md`: HU-01 y HU-02.
- `plan.md`: estrategia de implementación.
- `architecture.md`: autenticación y autorización.
- `data-model.md`: tabla `promoters`.
- `api-contract.md`: endpoints, permisos y errores.
- `tasks.md`: tareas de seguridad y autenticación.
- `adr-001-migrations.md`: persistencia.
- `adr-002-concurrency.md`: operaciones protegidas.
- `adr-003-snapshots.md`: conservación del historial.

## 20. Estado y seguimiento

**Estado de la decisión:** Aceptado.

Se adopta JWT para la autenticación, autorización basada en roles y hashes seguros de contraseñas como estrategia de seguridad del sistema Toy Library.

La decisión deberá implementarse y comprobarse mediante pruebas automatizadas.

La aceptación de este ADR no significa que los mecanismos de seguridad ya estén implementados.

---

**ADR-004 — Autenticación, Autorización y Seguridad — Toy Library A13 — Ficha 3239137.**
