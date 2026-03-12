1. Actualización de la Documentación
He agregado la Sección 5 explicando los roles y he aclarado en la Sección 4 que las acciones de crear/editar requieren rol de Admin.

markdown
# Documentación API - Parte 2: Autenticación y Roles

Esta documentación cubre el registro, inicio de sesión, uso de tokens y la **gestión de roles** para acceder a rutas protegidas.

**URL Base:** `http://127.0.0.1:8000/api`

## ❗️ 1. Headers Globales (Revisado)

Para todas las peticiones, se deben seguir estas reglas de encabezados:

**Peticiones Públicas** (ej: `POST /api/login`):
```json
{
  "Accept": "application/json",
  "Content-Type": "application/json"
}
Peticiones Protegidas (ej: GET /api/perfil): Además de los anteriores, debes añadir el token de autorización.

json
{
  "Accept": "application/json",
  "Authorization": "Bearer [TU_TOKEN_AQUI]"
}
2. Endpoints de Autenticación (Públicos)
Estos endpoints son públicos y se usan para obtener o registrar un usuario.

2.1 Registro de Usuario (Rol por defecto: Vendedor)
Crea un nuevo usuario y devuelve sus datos junto con un primer token.

Endpoint: POST /api/registro

Descripción: Registra un nuevo usuario en el sistema. Por defecto se le asigna el rol de Vendedor.

Body (Request):

json
{
    "nombre_completo": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "telefono": "987654321",
    "dni": "12345678",
    "direccion": "Av. Siempre Viva 123",
    "codigo_patrocinador": null 
}
Nota: codigo_patrocinador es opcional. Si se envía, debe ser un codigo_referido válido de otro usuario.

Respuesta (201 Created):

json
{
    "message": "¡Vendedor registrado exitosamente!",
    "data": {
        "id": 1,
        "nombre_completo": "Juan Pérez",
        "email": "juan@example.com",
        "rol": "vendedor",
        "codigo_referido": "ABC12345"
    },
    "access_token": "1|AbCdeFgHiJkLmNoPqRsTuVwXyZ...",
    "token_type": "Bearer"
}
2.2 Login
Valida las credenciales y devuelve un nuevo token.

Endpoint: POST /api/login

Body (Request):

json
{
    "email": "juan@example.com",
    "password": "password123"
}
Respuesta (200 OK):

json
{
    "message": "Inicio de sesión exitoso",
    "user": {
        "id": 1,
        "nombre_completo": "Juan Pérez",
        "email": "juan@example.com",
        "rol": "vendedor" 
    },
    "access_token": "2|ZxYwVuTsRqPoNmLkJiHgFeDcBa...",
    "token_type": "Bearer"
}
3. Endpoints Protegidos (Privados)
Estos endpoints requieren el access_token.

3.1 Ver Perfil
Obtiene los datos del usuario autenticado.

Endpoint: GET /api/perfil

Headers: Authorization: Bearer [TOKEN]

Respuesta (200 OK):

json
{
    "message": "Perfil obtenido exitosamente",
    "data": {
        "id": 1,
        "nombre_completo": "Juan Pérez",
        "rol": "vendedor"
    }
}
3.2 Logout (Cerrar Sesión)
Invalida el token actual.

Endpoint: POST /api/logout

Headers: Authorization: Bearer [TOKEN]

4. Actualización: Rutas del Catálogo y Permisos
Las rutas del catálogo tienen diferentes niveles de acceso:

Rutas PÚBLICAS: (Cualquiera puede ver)

GET /api/productos (Listar)
GET /api/productos/{id} (Ver detalle)
GET /api/videos
GET /api/premios
Rutas ADMINISTRATIVAS: (Requieren Token + Rol Admin)

POST /api/productos (Crear)
PUT /api/productos/{id} (Editar)
DELETE /api/productos/{id} (Eliminar)
Igual para Videos y Premios.
4.1 Ejemplo: Crear un Producto (Solo Admin)
Endpoint: POST /api/productos

Headers: Authorization: Bearer [TOKEN_DE_ADMIN]

Body (Request):

json
{
    "nombre": "Producto Exclusivo",
    "precio": "199.99",
    "stock": 10,
    "puntos": 100,
    "categoria_id": 1
}
Posibles Respuestas:

201 Created: Si el usuario es Admin.
403 Forbidden: Si el usuario es Vendedor o Socio ("No autorizado. Solo Admin puede crear productos").
401 Unauthenticated: Si no se envía token.
5. Roles y Permisos del Sistema
El sistema maneja estrictamente 3 roles. El campo rol se devuelve en el objeto user al hacer login.

1. Admin (admin)
Acceso: Total.
Capacidades:
Crear, Editar y Eliminar productos, videos y premios.
Gestionar comisiones y puntos de usuarios.
Ver dashboard global.
2. Vendedor (vendedor)
Acceso: Área de Negocio.
Capacidades:
Ver catálogo y comprar (precio normal).
Registrar nuevos afiliados (red).
Ver dashboard de sus comisiones y referidos.
Es el rol por defecto al registrarse.
3. Socio (socio)
Acceso: Área de Compras.
Capacidades:
Ver catálogo y comprar.
Canjear premios.
NO tiene acceso a herramientas de negocio ni red de referidos.
plaintext

### 2. Archivo de Pruebas HTTP (`tests/pruebas_roles.http`)

Este archivo está diseñado para que pruebes los 3 roles. Intenta crear un producto con cada uno: solo el Admin debería poder hacerlo.

> **Nota:** Asegúrate de tener usuarios creados en tu base de datos con los correos `admin@test.com`, `vendedor@test.com` y `socio@test.com` (y sus roles respectivos en la columna `rol`) para que esto funcione.

```http:d:\Proyectos\PROYECTOS\GIT\project-affiliate-web\nature-bio-life\tests\pruebas_roles.http
### VARIABLES GLOBALES
@host = http://localhost:8000/api
@password = password123

# Correos de prueba (Asegúrate de que existan en tu BD con el rol correcto)
@email_admin = admin@test.com
@email_vendedor = vendedor@test.com
@email_socio = socio@test.com

# ===========================================================================
# 1. OBTENER TOKENS PARA CADA ROL
# ===========================================================================

# 1.1 LOGIN ADMIN (Rol: 'admin')
# @name loginAdmin
POST {{host}}/login
Content-Type: application/json
Accept: application/json

{
    "email": "{{email_admin}}",
    "password": "{{password}}"
}

### Guardar Token Admin
@token_admin = {{loginAdmin.response.body.access_token}}


# 1.2 LOGIN VENDEDOR (Rol: 'vendedor')
# @name loginVendedor
POST {{host}}/login
Content-Type: application/json
Accept: application/json

{
    "email": "{{email_vendedor}}",
    "password": "{{password}}"
}

### Guardar Token Vendedor
@token_vendedor = {{loginVendedor.response.body.access_token}}


# 1.3 LOGIN SOCIO (Rol: 'socio')
# @name loginSocio
POST {{host}}/login
Content-Type: application/json
Accept: application/json

{
    "email": "{{email_socio}}",
    "password": "{{password}}"
}

### Guardar Token Socio
@token_socio = {{loginSocio.response.body.access_token}}


# ===========================================================================
# 2. PRUEBA DE FUEGO: CREAR UN PRODUCTO
# Solo el ADMIN debe poder hacerlo. Los demás deben recibir error 403.
# ===========================================================================

# 2.1 INTENTO ADMIN -> DEBE FUNCIONAR (Status 201)
POST {{host}}/productos
Authorization: Bearer {{token_admin}}
Content-Type: application/json
Accept: application/json

{
    "nombre": "Producto Test Admin",
    "precio": 150.00,
    "stock": 100,
    "puntos": 20,
    "categoria_id": 1,
    "activo": true
}

# 2.2 INTENTO VENDEDOR -> DEBE FALLAR (Status 403 Forbidden)
POST {{host}}/productos
Authorization: Bearer {{token_vendedor}}
Content-Type: application/json
Accept: application/json

{
    "nombre": "Producto Hack Vendedor",
    "precio": 10.00,
    "stock": 100,
    "puntos": 500,
    "categoria_id": 1
}

# 2.3 INTENTO SOCIO -> DEBE FALLAR (Status 403 Forbidden)
POST {{host}}/productos
Authorization: Bearer {{token_socio}}
Content-Type: application/json
Accept: application/json

{
    "nombre": "Producto Hack Socio",
    "precio": 10.00,
    "stock": 100,
    "puntos": 500,
    "categoria_id": 1
}

# ===========================================================================
# 3. PRUEBA DE ACCESO A PERFIL (Todos deben poder entrar)
# ===========================================================================

GET {{host}}/perfil
Authorization: Bearer {{token_socio}}
Accept: application/json