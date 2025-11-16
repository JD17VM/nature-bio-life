# Documentación API - Parte 2: Autenticación

Esta documentación cubre el registro de vendedores, inicio de sesión y el uso de tokens de autenticación para acceder a rutas protegidas.

**URL Base:** `http://127.0.0.1:8000/api`

## ❗️ 1. Headers Globales (Revisado)

Para todas las peticiones, se deben seguir estas reglas de encabezados:

**Peticiones Públicas** (ej: `POST /api/login`):
```json
{
  "Accept": "application/json",
  "Content-Type": "application/json"
}
```

**Peticiones Protegidas** (ej: `GET /api/perfil`):
Además de los anteriores, **debes** añadir el token de autorización.
```json
{
  "Accept": "application/json",
  "Authorization": "Bearer [TU_TOKEN_AQUI]"
}
```

---

## 2. Endpoints de Autenticación (Públicos)

Estos endpoints son públicos y se usan para obtener o registrar un vendedor.

### 2.1 Registro de Vendedor

Crea un nuevo vendedor y devuelve sus datos junto con un primer token de autenticación.

**Endpoint:** `POST /api/registro`

**Descripción:** Registra un nuevo usuario/vendedor en el sistema.

**Body (Request):**
```json
{
    "nombre_completo": "Vendedor de Prueba",
    "email": "vendedor@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "telefono": "987654321",
    "dni": "12345678",
    "direccion": "Av. Siempre Viva 123",
    "codigo_patrocinador": null 
}
```
*Nota: `codigo_patrocinador` es opcional. Si se envía, debe ser un `codigo_referido` válido de otro usuario.*

**Respuesta (201 Created):**
```json
{
    "message": "¡Vendedor registrado exitosamente!",
    "data": {
        "nombre_completo": "Vendedor de Prueba",
        "email": "vendedor@example.com",
        "telefono": "987654321",
        "dni": "12345678",
        "direccion": "Av. Siempre Viva 123",
        "patrocinador_id": null,
        "codigo_referido": "aBcDeF1234",
        "id": 1
    },
    "access_token": "1|AbCdeFgHiJkLmNoPqRsTuVwXyZ...",
    "token_type": "Bearer"
}
```

**Respuesta (422 Error de Validación):**
```json
{
    "message": "Las contraseñas no coinciden. (and 2 more errors)",
    "errors": {
        "password": [
            "Las contraseñas no coinciden."
        ],
        "email": [
            "Este email ya está en uso."
        ],
        "codigo_patrocinador": [
            "El código de patrocinador no es válido."
        ]
    }
}
```

### 2.2 Login de Vendedor

Valida las credenciales y devuelve un nuevo token. **Importante:** Al hacer login, todos los tokens anteriores de este usuario son invalidados.

**Endpoint:** `POST /api/login`

**Body (Request):**
```json
{
    "email": "vendedor@example.com",
    "password": "password123"
}
```

**Respuesta (200 OK):**
```json
{
    "message": "Inicio de sesión exitoso",
    "data": { },
    "access_token": "2|ZxYwVuTsRqPoNmLkJiHgFeDcBa...",
    "token_type": "Bearer"
}
```

**Respuesta (401 Error de Credenciales):**
```json
{
    "message": "Email o contraseña incorrectos."
}
```

---

## 3. Endpoints Protegidos (Privados)

Estos endpoints **requieren** el `access_token` en el encabezado `Authorization`.

### 3.1 Cómo Enviar el Token (Pasaporte Digital)

Para todas las peticiones a endpoints protegidos, debes añadir el token obtenido en el login.

- **Header:** `Authorization`
- **Valor:** `Bearer [TU_TOKEN_AQUI]` (La palabra "Bearer", un espacio, y el token)

**Ejemplo:**
```
Authorization: Bearer 2|ZxYwVuTsRqPoNmLkJiHgFeDcBa...
```

### 3.2 Ver Perfil

Obtiene los datos del vendedor actualmente autenticado.

**Endpoint:** `GET /api/perfil`

**Headers:**
```json
{
  "Accept": "application/json",
  "Authorization": "Bearer [TU_TOKEN_AQUI]"
}
```

**Respuesta (200 OK):**
```json
{
    "message": "Perfil obtenido exitosamente",
    "data": {
        "id": 1,
        "nombre_completo": "Vendedor de Prueba",
        "email": "vendedor@example.com"
    }
}
```

**Respuesta (401 Error de Autenticación):** (Si el token es inválido o no se envía)
```json
{
    "message": "Unauthenticated."
}
```

### 3.3 Logout (Cerrar Sesión)

Invalida y destruye el token que se está usando actualmente.

**Endpoint:** `POST /api/logout`

**Headers:**
```json
{
  "Accept": "application/json",
  "Authorization": "Bearer [TU_TOKEN_AQUI]"
}
```

**Respuesta (200 OK):**
```json
{
    "message": "Sesión cerrada exitosamente"
}
```

---

## 4. Actualización: Rutas del Catálogo Protegidas

Las rutas del catálogo (Parte 1 de la documentación) han sido actualizadas:

**Rutas PÚBLICAS:** `GET` (Listar `index` y Ver `show`) siguen siendo públicas. Cualquiera puede ver el catálogo.
- `GET /api/productos`
- `GET /api/productos/1`

**Rutas PRIVADAS:** `POST` (Crear), `PUT` (Actualizar) y `DELETE` (Borrar) ahora están protegidas y **requieren el token de autorización**.

### 4.1 Ejemplo: Crear un Producto (Protegido)

**Endpoint:** `POST /api/productos`

**Headers:** (¡Ahora requiere el token!)
```json
{
  "Accept": "application/json",
  "Content-Type": "application/json",
  "Authorization": "Bearer [TU_TOKEN_AQUI]"
}
```

**Body (Request):**
```json
{
    "nombre": "Nuevo Producto Protegido",
    "precio": "199.99",
    "stock": 10,
    "puntos": 100,
    "categoria_id": 1
}
```

**Respuesta (201 Created):**
```json
{
    "message": "Producto creado exitosamente",
    "data": { }
}
```

**Respuesta (401 Error de Autenticación):** (Si olvidas el token)
```json
{
    "message": "Unauthenticated."
}
```