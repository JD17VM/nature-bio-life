# 📚 Documentación Completa de Endpoints API - Nature Bio Life

**URL Base:** `http://127.0.0.1:8000/api`  
**Versión:** 1.0.0  
**Última Actualización:** 12 de abril de 2026

---

## 📋 Tabla de Contenido

1. [Headers Globales](#headers-globales)
2. [Autenticación](#autenticación)
3. [Dashboard](#dashboard)
4. [Categorías](#categorías)
5. [Categoría Premios](#categoría-premios)
6. [Categoría Videos](#categoría-videos)
7. [Productos](#productos)
8. [Premios](#premios)
9. [Videos](#videos)
10. [Materiales](#materiales)
11. [Tipo Materiales](#tipo-materiales)
12. [Pedidos](#pedidos)
13. [Comisiones](#comisiones)
14. [Historial Puntos](#historial-puntos)
15. [Canje Premios](#canje-premios)
16. [Referidos](#referidos)
17. [Notificaciones](#notificaciones)
18. [Configuraciones](#configuraciones)

---

# Headers Globales

## Peticiones Públicas

```json
{
    "Accept": "application/json",
    "Content-Type": "application/json"
}
```

## Peticiones Autenticadas

```json
{
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer [TU_TOKEN_AQUI]"
}
```

---

# 🔐 AUTENTICACIÓN

## 1. Registro de Usuario

| Propiedad         | Valor           |
| ----------------- | --------------- |
| **Endpoint**      | `/api/registro` |
| **Verbo HTTP**    | `POST`          |
| **Autenticación** | ❌ No requerida |
| **Rol Requerido** | ❌ No (Público) |

### Input (Body)

```json
{
    "nombre_completo": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "telefono": "987654321",
    "dni": "12345678",
    "direccion": "Av. Siempre Viva 123",
    "codigo_patrocinador": "ABC12345"
}
```

**Notas:**

- `codigo_patrocinador` es opcional
- Si se envía, debe ser un código válido de otro usuario
- Las contraseñas deben coincidir

### Output (201 Created)

```json
{
    "message": "¡Vendedor registrado exitosamente!",
    "user": {
        "id": 1,
        "nombre_completo": "Juan Pérez",
        "email": "juan@example.com",
        "telefono": "987654321",
        "dni": "12345678",
        "rol": "vendedor"
    },
    "access_token": "1|AbCdeFgHiJkLmNoPqRsTuVwXyZ...",
    "token_type": "Bearer"
}
```

### Output (422 Error de Validación)

```json
{
    "message": "Las contraseñas no coinciden. (and 2 more errors)",
    "errors": {
        "password": ["Las contraseñas no coinciden."],
        "email": ["Este email ya está en uso."],
        "dni": ["Este DNI ya está en uso."]
    }
}
```

---

## 2. Login

| Propiedad         | Valor           |
| ----------------- | --------------- |
| **Endpoint**      | `/api/login`    |
| **Verbo HTTP**    | `POST`          |
| **Autenticación** | ❌ No requerida |
| **Rol Requerido** | ❌ No (Público) |

### Input (Body)

```json
{
    "email": "juan@example.com",
    "password": "password123"
}
```

### Output (200 OK)

```json
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
```

### Output (401 No Autorizado)

```json
{
    "message": "Credenciales incorrectas."
}
```

---

## 3. Ver Perfil

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/perfil`               |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

### Input

Sin body, solo headers con token

### Output (200 OK)

```json
{
    "message": "Perfil obtenido exitosamente",
    "user": {
        "id": 1,
        "nombre_completo": "Juan Pérez",
        "email": "juan@example.com",
        "telefono": "987654321",
        "dni": "12345678",
        "rol": "vendedor",
        "activo": true,
        "codigo_referido": "XYZ98765"
    }
}
```

---

## 4. Actualizar Perfil

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/perfil`               |
| **Verbo HTTP**    | `PUT`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

### Input (Body)

```json
{
    "nombre_completo": "Juan Pérez García",
    "email": "juan.perez@example.com",
    "telefono": "987654321",
    "dni": "12345678",
    "direccion": "Av. Siempre Viva 123, Apt 4B"
}
```

**Notas:**

- Todos los campos son opcionales, puedes actualizar solo los que necesites
- El email debe ser único (no puede usarse si otro usuario lo tiene)
- El DNI debe ser único (no puede usarse si otro usuario lo tiene)

### Output (200 OK)

```json
{
    "message": "Perfil actualizado exitosamente",
    "user": {
        "id": 1,
        "nombre_completo": "Juan Pérez García",
        "email": "juan.perez@example.com",
        "telefono": "987654321",
        "dni": "12345678",
        "direccion": "Av. Siempre Viva 123, Apt 4B",
        "rol": "vendedor",
        "rol_label": "Vendedor",
        "codigo_referido": "XYZ98765",
        "patrocinador_id": null,
        "puntos_saldo": 1500,
        "activo": true,
        "created_at": "2026-03-15T10:30:00Z"
    }
}
```

### Output (422 Error de Validación)

```json
{
    "message": "El email debe ser válido. (and 1 more error)",
    "errors": {
        "email": ["Este email ya está en uso."],
        "dni": ["Este DNI ya está en uso."]
    }
}
```

---

## 5. Cambiar Contraseña

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/cambiar-contraseña`   |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

### Input (Body)

```json
{
    "current_password": "contraseña_actual",
    "new_password": "nueva_contraseña",
    "new_password_confirmation": "nueva_contraseña"
}
```

**Notas:**

- `current_password` debe ser correcta
- `new_password` debe tener al menos 8 caracteres
- `new_password` debe ser diferente de `current_password`
- Las contraseñas nuevas deben coincidir

### Output (200 OK)

```json
{
    "message": "Contraseña actualizada exitosamente"
}
```

### Output (422 Error de Validación)

```json
{
    "message": "La contraseña actual es incorrecta. (and 1 more error)",
    "errors": {
        "current_password": ["La contraseña actual es incorrecta."],
        "new_password": ["Las contraseñas nuevas no coinciden."]
    }
}
```

---

## 7. Logout

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/logout`               |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Sesión cerrada exitosamente."
}
```

---

# 📊 DASHBOARD

## 1. Dashboard Principal

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/dashboard`            |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

### Input

Sin body

### Output (200 OK)

```json
{
    "user": {
        "name": "Juan Pérez",
        "rank": "Vendedor",
        "status": "ACTIVO"
    },
    "stats": {
        "earnings": 250.5,
        "points": 1500
    },
    "referral_link": "http://127.0.0.1:8000/registro?ref=XYZ98765",
    "referral_code": "XYZ98765"
}
```

---

# 📁 CATEGORÍAS

## 1. Listar Categorías

| Propiedad         | Valor             |
| ----------------- | ----------------- |
| **Endpoint**      | `/api/categorias` |
| **Verbo HTTP**    | `GET`             |
| **Autenticación** | ❌ No requerida   |
| **Rol Requerido** | ❌ No (Público)   |
| **Paginación**    | ❌ No             |

### Input

Sin body

### Output (200 OK)

```json
[
    {
        "id": 1,
        "nombre": "Nutrición Deportiva",
        "descripcion": "Suplementos y vitaminas",
        "activa": true,
        "created_at": "2026-03-10T08:00:00Z"
    }
]
```

---

## 2. Ver Categoría por ID

| Propiedad         | Valor                  |
| ----------------- | ---------------------- |
| **Endpoint**      | `/api/categorias/{id}` |
| **Verbo HTTP**    | `GET`                  |
| **Autenticación** | ❌ No requerida        |
| **Rol Requerido** | ❌ No (Público)        |

### Input

Sin body

### Output (200 OK)

```json
{
    "id": 1,
    "nombre": "Nutrición Deportiva",
    "descripcion": "Suplementos y vitaminas",
    "activa": true,
    "created_at": "2026-03-10T08:00:00Z"
}
```

---

## 3. Crear Categoría

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/categorias`           |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "nombre": "Nueva Categoría",
    "descripcion": "Descripción"
}
```

### Output (201 Created)

```json
{
    "message": "Categoría creada exitosamente",
    "data": {
        "id": 3,
        "nombre": "Nueva Categoría",
        "descripcion": "Descripción",
        "activa": true
    }
}
```

### Output (403 Forbidden)

```json
{
    "message": "No autorizado. Solo Admin puede crear categorías."
}
```

---

## 4. Actualizar Categoría

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/categorias/{id}`      |
| **Verbo HTTP**    | `PUT`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "nombre": "Categoría Actualizada",
    "activa": false
}
```

### Output (200 OK)

```json
{
    "message": "Categoría actualizada exitosamente",
    "data": {
        "id": 1,
        "nombre": "Categoría Actualizada",
        "activa": false
    }
}
```

---

## 5. Eliminar Categoría

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/categorias/{id}`      |
| **Verbo HTTP**    | `DELETE`                    |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Categoría eliminada exitosamente"
}
```

---

# 🛍️ PRODUCTOS

## 1. Listar Productos

| Propiedad         | Valor                 |
| ----------------- | --------------------- |
| **Endpoint**      | `/api/productos`      |
| **Verbo HTTP**    | `GET`                 |
| **Autenticación** | ❌ No requerida       |
| **Rol Requerido** | ❌ No (Público)       |
| **Paginación**    | ✅ Sí (`per_page=15`) |

### Input

```
Parámetros: ?per_page=15
```

### Output (200 OK)

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "nombre": "Proteína Whey",
            "descripcion": "Proteína de alta calidad",
            "precio": 45.99,
            "stock": 100,
            "puntos": 50,
            "imagen_url": "productos/proteina.jpg",
            "activo": true,
            "categoria": {
                "id": 1,
                "nombre": "Nutrición Deportiva"
            }
        }
    ],
    "first_page_url": "https://www.naturebiolife.com/api/productos?page=1",
    "from": 1,
    "last_page": 4,
    "last_page_url": "https://www.naturebiolife.com/api/productos?page=4",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/productos?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": "https://www.naturebiolife.com/api/productos?page=2",
            "label": "2",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/productos?page=4",
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": "https://www.naturebiolife.com/api/productos?page=2",
    "path": "https://www.naturebiolife.com/api/productos",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 50
}
```

---

## 2. Ver Producto por ID

| Propiedad         | Valor                 |
| ----------------- | --------------------- |
| **Endpoint**      | `/api/productos/{id}` |
| **Verbo HTTP**    | `GET`                 |
| **Autenticación** | ❌ No requerida       |
| **Rol Requerido** | ❌ No (Público)       |

### Input

Sin body

### Output (200 OK)

```json
{
    "id": 1,
    "nombre": "Proteína Whey",
    "descripcion": "Proteína de alta calidad",
    "precio": 45.99,
    "stock": 100,
    "puntos": 50,
    "imagen_url": "productos/proteina.jpg",
    "categoria": {
        "id": 1,
        "nombre": "Nutrición Deportiva"
    }
}
```

---

## 3. Crear Producto

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/productos`            |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (FormData)

```
nombre: "Nuevo Producto"
descripcion: "Descripción"
precio: 50.00
stock: 200
puntos: 75
categoria_id: 1
activo: true
imagen: [file]
```

### Output (201 Created)

```json
{
    "message": "Producto creado exitosamente",
    "data": {
        "id": 5,
        "nombre": "Nuevo Producto",
        "precio": 50.0,
        "stock": 200,
        "puntos": 75,
        "imagen_url": "productos/nuevo_xxxxx.jpg"
    }
}
```

---

## 4. Actualizar Producto

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/productos/{id}`       |
| **Verbo HTTP**    | `PUT`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (FormData)

```
nombre: "Producto Actualizado"
precio: 55.00
stock: 180
imagen: [file] (opcional)
```

### Output (200 OK)

```json
{
    "message": "Producto actualizado exitosamente",
    "data": {
        "id": 1,
        "nombre": "Producto Actualizado",
        "precio": 55.0,
        "stock": 180
    }
}
```

---

## 5. Eliminar Producto

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/productos/{id}`       |
| **Verbo HTTP**    | `DELETE`                    |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Producto eliminado exitosamente"
}
```

---

# 🏆 PREMIOS

## 1. Listar Premios

| Propiedad         | Valor                 |
| ----------------- | --------------------- |
| **Endpoint**      | `/api/premios`        |
| **Verbo HTTP**    | `GET`                 |
| **Autenticación** | ❌ No requerida       |
| **Rol Requerido** | ❌ No (Público)       |
| **Paginación**    | ✅ Sí (`per_page=15`) |

### Input

```
Parámetros: ?per_page=15
```

### Output (200 OK)

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "nombre": "Camiseta Oficial",
            "descripcion": "Camiseta oficial",
            "puntos_requeridos": 500,
            "stock_disponible": 50,
            "disponible": true,
            "categoria_premio_id": 1,
            "categoriaPremio": {
                "id": 1,
                "nombre": "Merchandising"
            }
        }
    ],
    "first_page_url": "https://www.naturebiolife.com/api/premios?page=1",
    "from": 1,
    "last_page": 2,
    "last_page_url": "https://www.naturebiolife.com/api/premios?page=2",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/premios?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": "https://www.naturebiolife.com/api/premios?page=2",
            "label": "2",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/premios?page=2",
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": "https://www.naturebiolife.com/api/premios?page=2",
    "path": "https://www.naturebiolife.com/api/premios",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 20
}
```

---

## 2. Ver Premio por ID

| Propiedad         | Valor               |
| ----------------- | ------------------- |
| **Endpoint**      | `/api/premios/{id}` |
| **Verbo HTTP**    | `GET`               |
| **Autenticación** | ❌ No requerida     |
| **Rol Requerido** | ❌ No (Público)     |

### Input

Sin body

### Output (200 OK)

```json
{
    "id": 1,
    "nombre": "Camiseta Oficial",
    "puntos_requeridos": 500,
    "stock_disponible": 50,
    "disponible": true
}
```

---

## 3. Crear Premio

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/premios`              |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "nombre": "Nuevo Premio",
    "descripcion": "Descripción",
    "puntos_requeridos": 300,
    "stock_disponible": 100,
    "categoria_premio_id": 1,
    "disponible": true
}
```

### Output (201 Created)

```json
{
    "message": "Premio creado exitosamente",
    "data": {
        "id": 5,
        "nombre": "Nuevo Premio",
        "puntos_requeridos": 300
    }
}
```

---

## 4. Actualizar Premio

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/premios/{id}`         |
| **Verbo HTTP**    | `PUT`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "puntos_requeridos": 250,
    "stock_disponible": 80,
    "disponible": true
}
```

### Output (200 OK)

```json
{
    "message": "Premio actualizado exitosamente",
    "data": {
        "id": 1,
        "nombre": "Camiseta Oficial",
        "puntos_requeridos": 250
    }
}
```

---

## 5. Eliminar Premio

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/premios/{id}`         |
| **Verbo HTTP**    | `DELETE`                    |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Premio eliminado exitosamente"
}
```

---

# 🎬 VIDEOS

## 1. Listar Videos

| Propiedad         | Valor                 |
| ----------------- | --------------------- |
| **Endpoint**      | `/api/videos`         |
| **Verbo HTTP**    | `GET`                 |
| **Autenticación** | ❌ No requerida       |
| **Rol Requerido** | ❌ No (Público)       |
| **Paginación**    | ✅ Sí (`per_page=15`) |

### Input

```
Parámetros: ?per_page=15
```

### Output (200 OK)

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "titulo": "Tutorial de Uso",
            "descripcion": "Aprende cómo usar nuestros productos",
            "url_video": "https://youtube.com/embed/xxxxx",
            "categoria_video_id": 1,
            "activo": true,
            "categoriaVideo": {
                "id": 1,
                "nombre": "Tutoriales"
            },
            "created_at": "2026-04-10T15:30:00Z"
        }
    ],
    "first_page_url": "https://www.naturebiolife.com/api/videos?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "https://www.naturebiolife.com/api/videos?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/videos?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "https://www.naturebiolife.com/api/videos",
    "per_page": 15,
    "prev_page_url": null,
    "to": 10,
    "total": 10
}
```

---

## 2. Ver Video por ID

| Propiedad         | Valor              |
| ----------------- | ------------------ |
| **Endpoint**      | `/api/videos/{id}` |
| **Verbo HTTP**    | `GET`              |
| **Autenticación** | ❌ No requerida    |
| **Rol Requerido** | ❌ No (Público)    |

### Input

Sin body

### Output (200 OK)

```json
{
    "id": 1,
    "titulo": "Tutorial de Uso",
    "descripcion": "Aprende cómo usar nuestros productos",
    "url_video": "https://youtube.com/embed/xxxxx",
    "activo": true
}
```

---

## 3. Crear Video

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/videos`               |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "titulo": "Nuevo Video",
    "descripcion": "Descripción",
    "url_video": "https://youtube.com/embed/xxxxx",
    "categoria_video_id": 1,
    "activo": true
}
```

### Output (201 Created)

```json
{
    "message": "Video creado exitosamente",
    "data": {
        "id": 10,
        "titulo": "Nuevo Video",
        "url_video": "https://youtube.com/embed/xxxxx"
    }
}
```

---

## 4. Actualizar Video

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/videos/{id}`          |
| **Verbo HTTP**    | `PUT`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "titulo": "Video Actualizado",
    "activo": false
}
```

### Output (200 OK)

```json
{
    "message": "Video actualizado exitosamente",
    "data": {
        "id": 1,
        "titulo": "Video Actualizado",
        "activo": false
    }
}
```

---

## 5. Eliminar Video

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/videos/{id}`          |
| **Verbo HTTP**    | `DELETE`                    |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Video eliminado exitosamente"
}
```

---

# 📦 PEDIDOS

## 1. Listar Mis Pedidos

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/pedidos`              |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |
| **Paginación**    | ✅ Sí (`per_page=15`)       |

**Nota:** Usuarios normales ven solo sus pedidos. Admins ven todos.

### Input

```
Parámetros: ?per_page=15
```

### Output (200 OK)

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "numero_pedido": "ORD-ABCD1234",
            "user_id": 1,
            "subtotal": 150.5,
            "total": 160.5,
            "costo_envio": 10.0,
            "puntos_ganados": 150,
            "estado": "pendiente",
            "codigo_transaccion": "TXN123456",
            "notas": "Entrega urgente",
            "created_at": "2026-04-11T10:00:00Z",
            "detalles": [
                {
                    "id": 1,
                    "producto_id": 1,
                    "cantidad": 2,
                    "precio_unitario": 45.99
                }
            ]
        }
    ],
    "first_page_url": "https://www.naturebiolife.com/api/pedidos?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "https://www.naturebiolife.com/api/pedidos?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/pedidos?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "https://www.naturebiolife.com/api/pedidos",
    "per_page": 15,
    "prev_page_url": null,
    "to": 5,
    "total": 5
}
```

---

## 2. Ver Pedido por ID

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/pedidos/{id}`         |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

**Nota:** Solo puedes ver tu propio pedido, a menos que seas Admin.

### Input

Sin body

### Output (200 OK)

```json
{
    "id": 1,
    "numero_pedido": "ORD-ABCD1234",
    "user_id": 1,
    "subtotal": 150.5,
    "total": 160.5,
    "estado": "pendiente",
    "detalles": [
        {
            "id": 1,
            "producto_id": 1,
            "cantidad": 2,
            "precio_unitario": 45.99,
            "producto": {
                "id": 1,
                "nombre": "Proteína Whey"
            }
        }
    ]
}
```

### Output (403 Forbidden)

```json
{
    "message": "No tienes permiso para ver este pedido."
}
```

---

## 3. Crear Pedido

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/pedidos`              |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

### Input (FormData)

```json
{
  "user_id": 1,
  "detalles": [
    {
      "producto_id": 1,
      "cantidad": 2
    },
    {
      "producto_id": 3,
      "cantidad": 1
    }
  ],
  "codigo_transaccion": "TXN123456",
  "notas": "Entregar antes del viernes",
  "comprobante": [file] (opcional)
}
```

### Output (201 Created)

```json
{
    "message": "Pedido creado exitosamente",
    "data": {
        "id": 5,
        "numero_pedido": "ORD-XYZ9876",
        "user_id": 1,
        "subtotal": 150.5,
        "total": 160.5,
        "puntos_ganados": 150,
        "estado": "pendiente"
    }
}
```

### Output (422 Error)

```json
{
    "message": "No hay suficiente stock para el producto: Proteína Whey"
}
```

---

## 4. Actualizar Estado de Pedido

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/pedidos/{id}/estado`  |
| **Verbo HTTP**    | `PATCH`                     |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "estado": "completado"
}
```

### Output (200 OK)

```json
{
    "message": "Estado del pedido actualizado",
    "data": {
        "id": 1,
        "numero_pedido": "ORD-ABCD1234",
        "estado": "completado"
    }
}
```

---

## 5. Confirmar Pago de Pedido

| Propiedad         | Valor                              |
| ----------------- | ---------------------------------- |
| **Endpoint**      | `/api/pedidos/{id}/confirmar-pago` |
| **Verbo HTTP**    | `POST`                             |
| **Autenticación** | ✅ Requerida (Bearer Token)        |
| **Rol Requerido** | ✅ Admin                           |

### Input (Body)

```json
{
    "codigo_transaccion": "TXN999888"
}
```

### Output (200 OK)

```json
{
    "message": "Pago confirmado",
    "data": {
        "id": 1,
        "estado": "pagado",
        "codigo_transaccion": "TXN999888"
    }
}
```

---

# 💰 COMISIONES

## 1. Listar Mis Comisiones

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/comisiones`           |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |
| **Paginación**    | ✅ Sí (`per_page=15`)       |

**Nota:** Usuarios normales ven solo sus comisiones ganadas. Admins ven todas.

### Input

```
Parámetros: ?per_page=15
```

### Output (200 OK)

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "vendedor_id": 1,
            "comprador_id": 2,
            "pedido_id": 5,
            "monto_comision": 25.5,
            "estado": "pendiente",
            "fecha_pago": null,
            "vendedor": {
                "id": 1,
                "nombre_completo": "Juan Pérez"
            },
            "comprador": {
                "id": 2,
                "nombre_completo": "María García"
            },
            "created_at": "2026-04-11T10:15:00Z"
        }
    ],
    "first_page_url": "https://www.naturebiolife.com/api/comisiones?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "https://www.naturebiolife.com/api/comisiones?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/comisiones?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "https://www.naturebiolife.com/api/comisiones",
    "per_page": 15,
    "prev_page_url": null,
    "to": 8,
    "total": 8
}
```

---

## 2. Ver Comisión por ID

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/comisiones/{id}`      |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

**Nota:** Solo puedes ver tu propia comisión, a menos que seas Admin.

### Input

Sin body

### Output (200 OK)

```json
{
    "id": 1,
    "vendedor_id": 1,
    "monto": 25.5,
    "porcentaje": 10,
    "estado": "pendiente",
    "vendedor": {
        "id": 1,
        "nombre_completo": "Juan Pérez"
    }
}
```

---

## 3. Crear Comisión

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/comisiones`           |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "vendedor_id": 1,
    "comprador_id": 2,
    "pedido_id": 5,
    "monto": 25.5,
    "porcentaje": 10,
    "estado": "pendiente"
}
```

### Output (201 Created)

```json
{
    "message": "Comisión registrada",
    "data": {
        "id": 5,
        "vendedor_id": 1,
        "monto": 25.5,
        "estado": "pendiente"
    }
}
```

---

## 4. Actualizar Comisión

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/comisiones/{id}`      |
| **Verbo HTTP**    | `PUT`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "estado": "pagado",
    "fecha_pago": "2026-04-15"
}
```

### Output (200 OK)

```json
{
    "message": "Comisión actualizada",
    "data": {
        "id": 1,
        "estado": "pagado",
        "fecha_pago": "2026-04-15"
    }
}
```

---

## 5. Eliminar Comisión

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/comisiones/{id}`      |
| **Verbo HTTP**    | `DELETE`                    |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Comisión eliminada"
}
```

---

# 📊 HISTORIAL PUNTOS

## 1. Listar Mi Historial de Puntos

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/historial-puntos`     |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |
| **Paginación**    | ✅ Sí (`per_page=15`)       |

**Nota:** Usuarios normales ven solo su historial. Admins ven todos.

### Input

```
Parámetros: ?per_page=15
```

### Output (200 OK)

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "puntos": 150,
            "tipo": "compra",
            "descripcion": "Puntos por compra",
            "balance_nuevo": 1500,
            "referencia_id": 5,
            "referencia_tipo": "pedido",
            "usuario": {
                "id": 1,
                "nombre_completo": "Juan Pérez"
            },
            "created_at": "2026-04-11T10:00:00Z"
        }
    ],
    "first_page_url": "https://www.naturebiolife.com/api/historial-puntos?page=1",
    "from": 1,
    "last_page": 2,
    "last_page_url": "https://www.naturebiolife.com/api/historial-puntos?page=2",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/historial-puntos?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": "https://www.naturebiolife.com/api/historial-puntos?page=2",
            "label": "2",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/historial-puntos?page=2",
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": "https://www.naturebiolife.com/api/historial-puntos?page=2",
    "path": "https://www.naturebiolife.com/api/historial-puntos",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 25
}
```

---

## 2. Ver Movimiento por ID

| Propiedad         | Valor                        |
| ----------------- | ---------------------------- |
| **Endpoint**      | `/api/historial-puntos/{id}` |
| **Verbo HTTP**    | `GET`                        |
| **Autenticación** | ✅ Requerida (Bearer Token)  |
| **Rol Requerido** | ❌ No                        |

### Input

Sin body

### Output (200 OK)

```json
{
    "id": 1,
    "user_id": 1,
    "puntos": 150,
    "tipo": "compra",
    "descripcion": "Puntos por compra",
    "balance_nuevo": 1500,
    "referencia_id": 5,
    "created_at": "2026-04-11T10:00:00Z"
}
```

---

## 3. Crear Movimiento Manual de Puntos

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/historial-puntos`     |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "user_id": 1,
    "puntos": 100,
    "tipo": "bonificacion",
    "descripcion": "Bonificación por referido confirmado"
}
```

### Output (201 Created)

```json
{
    "message": "Movimiento registrado",
    "data": {
        "id": 10,
        "user_id": 1,
        "puntos": 100,
        "tipo": "bonificacion",
        "balance_nuevo": 1600,
        "created_at": "2026-04-11T11:00:00Z"
    }
}
```

---

# 🎁 CANJE PREMIOS

## 1. Listar Mis Canjes

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/canje-premios`        |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |
| **Paginación**    | ❌ No (retorna array)       |

**Nota:** Usuarios normales ven solo sus canjes. Admins ven todos.

### Input

Sin parámetros

### Output (200 OK)

```json
[
    {
        "id": 1,
        "user_id": 1,
        "premio_id": 1,
        "puntos_utilizados": 500,
        "estado": "pendiente",
        "observaciones": "Talle M",
        "usuario": {
            "id": 1,
            "nombre_completo": "Juan Pérez"
        },
        "premio": {
            "id": 1,
            "nombre": "Camiseta Oficial",
            "puntos_requeridos": 500
        },
        "created_at": "2026-04-11T09:30:00Z",
        "updated_at": "2026-04-11T09:30:00Z"
    }
]
```

---

## 2. Ver Canje por ID

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/canje-premios/{id}`   |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

### Input

Sin body

### Output (200 OK)

```json
{
    "id": 1,
    "user_id": 1,
    "premio_id": 1,
    "puntos_utilizados": 500,
    "estado": "pendiente",
    "observaciones": "Talle M",
    "created_at": "2026-04-11T09:30:00Z"
}
```

---

## 3. Crear Canje de Premio

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/canje-premios`        |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

### Input (Body)

```json
{
    "user_id": 1,
    "premio_id": 1,
    "observaciones": "Talle M"
}
```

### Output (201 Created)

```json
{
    "message": "Canje realizado exitosamente",
    "data": {
        "id": 5,
        "user_id": 1,
        "premio_id": 1,
        "puntos_utilizados": 500,
        "estado": "pendiente"
    }
}
```

### Output (422 Error)

```json
{
    "message": "Puntos insuficientes. Tienes 300 y necesitas 500."
}
```

---

## 4. Actualizar Estado del Canje

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/canje-premios/{id}`   |
| **Verbo HTTP**    | `PUT`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "estado": "completado",
    "observaciones": "Enviado el 15 de abril"
}
```

### Output (200 OK)

```json
{
    "message": "Canje actualizado",
    "data": {
        "id": 1,
        "estado": "completado",
        "observaciones": "Enviado el 15 de abril"
    }
}
```

---

## 5. Eliminar Canje

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/canje-premios/{id}`   |
| **Verbo HTTP**    | `DELETE`                    |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Canje eliminado"
}
```

---

# 👥 REFERIDOS

## 1. Listar Mis Referidos

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/referidos`            |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |
| **Paginación**    | ✅ Sí (`per_page=15`)       |

### Input

```
Parámetros: ?per_page=15 (opcional)
```

### Output (200 OK)

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 2,
            "nombre_completo": "María García",
            "email": "maria@example.com",
            "codigo_referido": "ABC98765",
            "rol": "vendedor",
            "total_sub_referidos": 3,
            "total_pedidos": 5,
            "total_compras": 450.0,
            "total_comisiones_generadas": 45.0,
            "created_at": "2026-04-10T11:30:00Z"
        }
    ],
    "first_page_url": "https://www.naturebiolife.com/api/referidos?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "https://www.naturebiolife.com/api/referidos?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/referidos?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "https://www.naturebiolife.com/api/referidos",
    "per_page": 15,
    "prev_page_url": null,
    "to": 12,
    "total": 12
}
```

---

## 2. Ver Detalle de Referido

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/referidos/{id}`       |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |

### Input

```
Parámetros: ?per_page=10 (para pedidos y comisiones)
```

### Output (200 OK)

```json
{
    "referido": {
        "id": 2,
        "nombre_completo": "María García",
        "email": "maria@example.com",
        "telefono": "987654321",
        "rol": "vendedor",
        "total_pedidos": 5,
        "total_compras": 450.0
    },
    "pedidos": {
        "data": [
            {
                "id": 10,
                "numero_pedido": "ORD-REF0001",
                "total": 150.5,
                "estado": "completado",
                "created_at": "2026-04-09T14:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 10,
            "total": 5
        }
    },
    "comisiones": [
        {
            "id": 1,
            "monto_comision": 15.05,
            "estado": "pagado",
            "pedido": {
                "id": 10,
                "numero_pedido": "ORD-REF0001"
            },
            "created_at": "2026-04-09T14:30:00Z"
        }
    ]
}
```

### Output (403 Forbidden)

```json
{
    "message": "Este usuario no es tu referido."
}
```

---

# 🔔 NOTIFICACIONES

## 1. Listar Mis Notificaciones

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/notificaciones`       |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ❌ No                       |
| **Paginación**    | ✅ Sí (15 por página)       |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Notificaciones obtenidas correctamente",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": "uuid-1234-5678",
                "type": "App\\Notifications\\PedidoConfirmado",
                "notifiable_type": "App\\Models\\User",
                "notifiable_id": 1,
                "data": {
                    "titulo": "Pedido Confirmado",
                    "mensaje": "Tu pedido ORD-ABCD1234 ha sido confirmado"
                },
                "read_at": null,
                "created_at": "2026-04-11T10:30:00Z"
            }
        ],
        "first_page_url": "https://www.naturebiolife.com/api/notificaciones?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "https://www.naturebiolife.com/api/notificaciones?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "https://www.naturebiolife.com/api/notificaciones?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "https://www.naturebiolife.com/api/notificaciones",
        "per_page": 15,
        "prev_page_url": null,
        "to": 3,
        "total": 3
    },
    "unread_count": 1
}
```

---

## 2. Ver Notificaciones No Leídas

| Propiedad         | Valor                        |
| ----------------- | ---------------------------- |
| **Endpoint**      | `/api/notificaciones/unread` |
| **Verbo HTTP**    | `GET`                        |
| **Autenticación** | ✅ Requerida (Bearer Token)  |
| **Rol Requerido** | ❌ No                        |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Notificaciones no leídas",
    "data": [
        {
            "id": "uuid-1234-5678",
            "type": "App\\Notifications\\PedidoConfirmado",
            "data": {
                "titulo": "Pedido Confirmado",
                "mensaje": "Tu pedido ORD-ABCD1234 ha sido confirmado"
            },
            "read_at": null,
            "created_at": "2026-04-11T10:30:00Z"
        }
    ]
}
```

---

## 3. Marcar Notificación Como Leída

| Propiedad         | Valor                           |
| ----------------- | ------------------------------- |
| **Endpoint**      | `/api/notificaciones/{id}/read` |
| **Verbo HTTP**    | `PUT`                           |
| **Autenticación** | ✅ Requerida (Bearer Token)     |
| **Rol Requerido** | ❌ No                           |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Notificación marcada como leída"
}
```

---

## 4. Marcar Todas las Notificaciones Como Leídas

| Propiedad         | Valor                          |
| ----------------- | ------------------------------ |
| **Endpoint**      | `/api/notificaciones/read-all` |
| **Verbo HTTP**    | `PUT`                          |
| **Autenticación** | ✅ Requerida (Bearer Token)    |
| **Rol Requerido** | ❌ No                          |

### Input

Sin body

### Output (200 OK)

```json
{
    "message": "Todas las notificaciones marcadas como leídas"
}
```

---

# 🏷️ CATEGORÍA PREMIOS

## 1. Listar Categorías de Premios

| Propiedad         | Valor                    |
| ----------------- | ------------------------ |
| **Endpoint**      | `/api/categoria-premios` |
| **Verbo HTTP**    | `GET`                    |
| **Autenticación** | ❌ No requerida          |
| **Rol Requerido** | ❌ No (Público)          |
| **Paginación**    | ❌ No (retorna array)    |

### Input

Sin body

### Output (200 OK)

```json
[
    {
        "id": 1,
        "nombre": "Merchandising",
        "descripcion": "Artículos de marca",
        "created_at": "2026-03-10T08:00:00Z",
        "updated_at": "2026-03-10T08:00:00Z"
    },
    {
        "id": 2,
        "nombre": "Viajes",
        "descripcion": "Paquetes y experiencias de viaje",
        "created_at": "2026-03-15T10:30:00Z",
        "updated_at": "2026-03-15T10:30:00Z"
    }
]
```

---

## 2. Crear Categoría de Premios

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/categoria-premios`    |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "nombre": "Nueva Categoría",
    "descripcion": "Descripción"
}
```

### Output (201 Created)

```json
{
    "message": "Categoría de premios creada exitosamente",
    "data": {
        "id": 5,
        "nombre": "Nueva Categoría"
    }
}
```

---

# 🎥 CATEGORÍA VIDEOS

## 1. Listar Categorías de Videos

| Propiedad         | Valor                   |
| ----------------- | ----------------------- |
| **Endpoint**      | `/api/categoria-videos` |
| **Verbo HTTP**    | `GET`                   |
| **Autenticación** | ❌ No requerida         |
| **Rol Requerido** | ❌ No (Público)         |
| **Paginación**    | ❌ No (retorna array)   |

### Input

Sin body

### Output (200 OK)

```json
[
    {
        "id": 1,
        "nombre": "Tutoriales",
        "descripcion": "Videos educativos",
        "orden": 1,
        "created_at": "2026-03-10T08:00:00Z",
        "updated_at": "2026-03-10T08:00:00Z"
    },
    {
        "id": 2,
        "nombre": "Capacitación",
        "descripcion": "Videos de capacitación de productos",
        "orden": 2,
        "created_at": "2026-03-12T14:00:00Z",
        "updated_at": "2026-03-12T14:00:00Z"
    }
]
```

---

## 2. Crear Categoría de Videos

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/categoria-videos`     |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "nombre": "Nueva Categoría",
    "descripcion": "Descripción",
    "orden": 2
}
```

### Output (201 Created)

```json
{
    "message": "Categoría de videos creada exitosamente",
    "data": {
        "id": 5,
        "nombre": "Nueva Categoría"
    }
}
```

---

# 🏗️ MATERIALES

## 1. Listar Materiales

| Propiedad         | Valor                 |
| ----------------- | --------------------- |
| **Endpoint**      | `/api/materiales`     |
| **Verbo HTTP**    | `GET`                 |
| **Autenticación** | ❌ No requerida       |
| **Rol Requerido** | ❌ No (Público)       |
| **Paginación**    | ✅ Sí (`per_page=15`) |

### Input

```
Parámetros: ?per_page=15
```

### Output (200 OK)

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "nombre": "Brochure",
            "descripcion": "Folleto informativo",
            "archivo_url": "materiales/brochure.pdf",
            "tipo_material_id": 1,
            "tipoMaterial": {
                "id": 1,
                "nombre": "Documentos"
            },
            "activo": true,
            "created_at": "2026-03-20T09:00:00Z"
        }
    ],
    "first_page_url": "https://www.naturebiolife.com/api/materiales?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "https://www.naturebiolife.com/api/materiales?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "https://www.naturebiolife.com/api/materiales?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "https://www.naturebiolife.com/api/materiales",
    "per_page": 15,
    "prev_page_url": null,
    "to": 5,
    "total": 5
}
```

---

## 2. Crear Material

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/materiales`           |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (FormData)

```
nombre: "Nuevo Material"
descripcion: "Descripción"
tipo_material_id: 1
archivo: [file]
```

### Output (201 Created)

```json
{
    "message": "Material creado exitosamente",
    "data": {
        "id": 5,
        "nombre": "Nuevo Material",
        "url_archivo": "materiales/nuevo_xxxxx.pdf"
    }
}
```

---

# ⚙️ TIPO MATERIALES

## 1. Listar Tipos de Materiales

| Propiedad         | Valor                  |
| ----------------- | ---------------------- |
| **Endpoint**      | `/api/tipo-materiales` |
| **Verbo HTTP**    | `GET`                  |
| **Autenticación** | ❌ No requerida        |
| **Rol Requerido** | ❌ No (Público)        |
| **Paginación**    | ❌ No (retorna array)  |

### Input

Sin body

### Output (200 OK)

```json
[
    {
        "id": 1,
        "nombre": "Documentos",
        "descripcion": "Archivos de documentación",
        "created_at": "2026-02-15T09:00:00Z",
        "updated_at": "2026-02-15T09:00:00Z"
    },
    {
        "id": 2,
        "nombre": "Presentaciones",
        "descripcion": "Archivos de presentación",
        "created_at": "2026-02-20T10:30:00Z",
        "updated_at": "2026-02-20T10:30:00Z"
    }
]
```

---

## 2. Crear Tipo de Material

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/tipo-materiales`      |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "nombre": "Nuevo Tipo",
    "descripcion": "Descripción"
}
```

### Output (201 Created)

```json
{
    "message": "Tipo de material creado exitosamente",
    "data": {
        "id": 5,
        "nombre": "Nuevo Tipo"
    }
}
```

---

# ⚙️ CONFIGURACIONES

## 1. Listar Configuraciones

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/configuraciones`      |
| **Verbo HTTP**    | `GET`                       |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |
| **Paginación**    | ❌ No (retorna array)       |

### Input

Sin body

### Output (200 OK)

```json
[
    {
        "id": 1,
        "clave": "comision_vendedor",
        "valor": "10",
        "descripcion": "Porcentaje de comisión para vendedores",
        "tipo": "porcentaje",
        "created_at": "2026-02-01T08:00:00Z",
        "updated_at": "2026-02-01T08:00:00Z"
    },
    {
        "id": 2,
        "clave": "comision_socio",
        "valor": "15",
        "descripcion": "Porcentaje de comisión para socios",
        "tipo": "porcentaje",
        "created_at": "2026-02-01T08:30:00Z",
        "updated_at": "2026-02-01T08:30:00Z"
    }
]
```

---

## 2. Crear Configuración

| Propiedad         | Valor                       |
| ----------------- | --------------------------- |
| **Endpoint**      | `/api/configuraciones`      |
| **Verbo HTTP**    | `POST`                      |
| **Autenticación** | ✅ Requerida (Bearer Token) |
| **Rol Requerido** | ✅ Admin                    |

### Input (Body)

```json
{
    "clave": "nueva_config",
    "valor": "valor_por_defecto",
    "descripcion": "Descripción",
    "tipo": "texto"
}
```

### Output (201 Created)

```json
{
    "message": "Configuración creada exitosamente",
    "data": {
        "id": 5,
        "clave": "nueva_config",
        "valor": "valor_por_defecto"
    }
}
```

---

# 📊 CÓDIGOS DE ESTADO HTTP

| Código | Significado           | Descripción                                            |
| ------ | --------------------- | ------------------------------------------------------ |
| `200`  | OK                    | Solicitud exitosa                                      |
| `201`  | Created               | Recurso creado exitosamente                            |
| `400`  | Bad Request           | Solicitud malformada                                   |
| `401`  | Unauthorized          | No autenticado                                         |
| `403`  | Forbidden             | Autenticado pero sin permisos (no tiene rol requerido) |
| `404`  | Not Found             | Recurso no encontrado                                  |
| `422`  | Unprocessable Entity  | Error de validación                                    |
| `500`  | Internal Server Error | Error del servidor                                     |

---

# 🔐 ROLES Y PERMISOS

## Roles Implementados

| Rol          | Descripción        | Permisos                                 |
| ------------ | ------------------ | ---------------------------------------- |
| **admin**    | Administrador      | Acceso total a todas las operaciones     |
| **socio**    | Socio/Patrocinador | Acceso a datos de referidos y comisiones |
| **vendedor** | Vendedor           | Acceso limitado a datos propios          |

## Operaciones Protegidas

| Operación          | Rol Requerido                        |
| ------------------ | ------------------------------------ |
| Create (POST)      | ✅ Admin                             |
| Update (PUT/PATCH) | ✅ Admin                             |
| Delete (DELETE)    | ✅ Admin                             |
| Index (GET lista)  | ❌ Pública (excepto configuraciones) |
| Show (GET detalle) | ❌ Pública (excepto configuraciones) |

---

# 📝 NOTAS IMPORTANTES

1. **Autenticación:** Todos los endpoints protegidos usan `Sanctum` con Bearer Tokens.

2. **Paginación:** Los endpoints que la soportan aceptan `?per_page=n` (máximo 100).

3. **Validación:** Todos los endpoints de creación/actualización retornan errores `422` con detalles.

4. **Filtrado:** Usuarios normales no pueden ver datos de otros usuarios (excepto datos públicos).

5. **Rate Limiting:** Incluye throttling configurado en `config/sanctum.php`.

6. **CORS:** Habilitado para facilitar integración con frontend.

---

**Versión:** 1.0.0  
**Última Actualización:** 12 de abril de 2026  
**Especificación:** JSON REST API con autenticación Bearer Token (Laravel Sanctum)
