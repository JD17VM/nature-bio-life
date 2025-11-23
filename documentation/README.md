-   [Registro, login y autenticacion de tokens](./registro-login-autenticacion_token.md)

---

# Documentación API

Esta documentación cubre los 9 endpoints de gestión del catálogo. Todos los endpoints están validados. El frontend debe estar preparado para manejar respuestas de error 422.

## ⚙️ 1. Cómo Ejecutar el Servidor

Para iniciar el servidor de desarrollo de Laravel, ejecuta el siguiente comando en la raíz de tu proyecto:

```bash
php artisan serve
```

La API estará disponible en la siguiente URL base:

**URL Base:** `http://127.0.0.1:8000/api`

## ❗️ 2. Headers Globales (¡Obligatorio!)

Todas las peticiones (especialmente POST, PUT, DELETE) deben incluir los siguientes dos encabezados (Headers) para asegurar que Laravel responda en formato JSON:

```json
{
    "Accept": "application/json",
    "Content-Type": "application/json"
}
```

## Endpoints del Catálogo

### 1. Categorías (Productos)

**Ruta Base:** `/api/categorias`

| Método | Ruta  | Descripción                         |
| ------ | ----- | ----------------------------------- |
| GET    | /     | Lista todas las categorías activas. |
| GET    | /{id} | Muestra una categoría específica.   |
| POST   | /     | Crea una nueva categoría.           |
| PUT    | /{id} | Actualiza una categoría existente.  |
| DELETE | /{id} | Elimina una categoría.              |

#### Ejemplos (Categorías)

**POST /api/categorias (Crear)**

Body (Request):

```json
{
    "nombre": "Nutrición Deportiva",
    "descripcion": "Suplementos y vitaminas para atletas."
}
```

Respuesta (201 Created):

```json
{
    "message": "Categoría creada exitosamente",
    "data": {
        "id": 1,
        "nombre": "Nutrición Deportiva",
        "descripcion": "Suplementos y vitaminas para atletas.",
        "activa": true,
        "created_at": "...",
        "updated_at": "..."
    }
}
```

Respuesta (422 Error de Validación):

```json
{
    "message": "El nombre de la categoría es obligatorio. (and 1 more error)",
    "errors": {
        "nombre": [
            "El nombre de la categoría es obligatorio.",
            "Ya existe una categoría con este nombre."
        ]
    }
}
```

**PUT /api/categorias/1 (Actualizar)**

Body (Request): (Solo envías los campos a cambiar)

```json
{
    "activa": false
}
```

Respuesta (200 OK):

```json
{
    "message": "Categoría actualizada exitosamente",
    "data": {
        "id": 1,
        "nombre": "Nutrición Deportiva",
        "activa": false
    }
}
```

**DELETE /api/categorias/1 (Eliminar)**

Respuesta (200 OK):

```json
{
    "message": "Categoría eliminada exitosamente"
}
```

### 2. Productos

**Ruta Base:** `/api/productos`

| Método | Ruta  | Descripción                        |
| ------ | ----- | ---------------------------------- |
| GET    | /     | Lista todos los productos activos. |
| GET    | /{id} | Muestra un producto específico.    |
| POST   | /     | Crea un nuevo producto.            |
| PUT    | /{id} | Actualiza un producto existente.   |
| DELETE | /{id} | Elimina un producto.               |

#### Ejemplos (Productos)

**POST /api/productos (Crear)**

Body (Request): (Recuerda: precio como string)

```json
{
    "nombre": "Proteína Whey 1kg",
    "descripcion": "Sabor chocolate.",
    "precio": "75.50",
    "stock": 100,
    "puntos": 50,
    "categoria_id": 1
}
```

Respuesta (201 Created):

```json
{
    "message": "Producto creado exitosamente",
    "data": {
        "id": 1,
        "nombre": "Proteína Whey 1kg",
        "precio": "75.50"
    }
}
```

Respuesta (422 Error de Validación):

```json
{
    "message": "El precio debe tener 2 decimales. (and 1 more error)",
    "errors": {
        "precio": ["El precio debe tener 2 decimales."],
        "categoria_id": ["La categoría seleccionada no existe o no es válida."]
    }
}
```

### 3. Categorías de Premios

**Ruta Base:** `/api/categoria-premios`

| Método | Ruta  | Descripción                            |
| ------ | ----- | -------------------------------------- |
| GET    | /     | Lista todas las categorías de premios. |
| GET    | /{id} | Muestra una categoría de premio.       |
| POST   | /     | Crea una nueva categoría de premio.    |
| PUT    | /{id} | Actualiza una categoría de premio.     |
| DELETE | /{id} | Elimina una categoría de premio.       |

#### Ejemplos (Categorías de Premios)

**POST /api/categoria-premios (Crear)**

Body (Request):

```json
{
    "nombre": "Viajes",
    "descripcion": "Canje de puntos por viajes."
}
```

Respuesta (201 Created):

```json
{
    "message": "Categoría de premio creada exitosamente",
    "data": {}
}
```

### 4. Premios

**Ruta Base:** `/api/premios`

| Método | Ruta  | Descripción                          |
| ------ | ----- | ------------------------------------ |
| GET    | /     | Lista todos los premios disponibles. |
| GET    | /{id} | Muestra un premio específico.        |
| POST   | /     | Crea un nuevo premio.                |
| PUT    | /{id} | Actualiza un premio existente.       |
| DELETE | /{id} | Elimina un premio.                   |

#### Ejemplos (Premios)

**POST /api/premios (Crear)**

Body (Request):

```json
{
    "nombre": "Estadía en Hotel",
    "puntos_requeridos": 10000,
    "stock": 5,
    "categoria_premio_id": 1
}
```

Respuesta (201 Created):

```json
{
    "message": "Premio creado exitosamente",
    "data": {}
}
```

### 5. Categorías de Videos

**Ruta Base:** `/api/categoria-videos`

| Método | Ruta  | Descripción                                       |
| ------ | ----- | ------------------------------------------------- |
| GET    | /     | Lista todas las categorías de videos (ordenadas). |
| GET    | /{id} | Muestra una categoría de video.                   |
| POST   | /     | Crea una nueva categoría de video.                |
| PUT    | /{id} | Actualiza una categoría de video.                 |
| DELETE | /{id} | Elimina una categoría de video.                   |

#### Ejemplos (Categorías de Videos)

**POST /api/categoria-videos (Crear)**

Body (Request):

```json
{
    "nombre": "Capacitación de Producto",
    "orden": 1
}
```

Respuesta (201 Created):

```json
{
    "message": "Categoría de video creada exitosamente",
    "data": {}
}
```

### 6. Videos

**Ruta Base:** `/api/videos`

| Método | Ruta  | Descripción                     |
| ------ | ----- | ------------------------------- |
| GET    | /     | Lista todos los videos activos. |
| GET    | /{id} | Muestra un video específico.    |
| POST   | /     | Crea un nuevo video.            |
| PUT    | /{id} | Actualiza un video existente.   |
| DELETE | /{id} | Elimina un video.               |

#### Ejemplos (Videos)

**POST /api/videos (Crear)**

Body (Request):

```json
{
    "titulo": "Cómo usar la Proteína Whey",
    "url": "https://youtube.com/watch?v=12345",
    "categoria_video_id": 1,
    "nivel": "Básico"
}
```

Respuesta (201 Created):

```json
{
    "message": "Video creado exitosamente",
    "data": {}
}
```

### 7. Tipos de Material

**Ruta Base:** `/api/tipo-materiales`

| Método | Ruta  | Descripción                        |
| ------ | ----- | ---------------------------------- |
| GET    | /     | Lista todos los tipos de material. |
| GET    | /{id} | Muestra un tipo de material.       |
| POST   | /     | Crea un nuevo tipo de material.    |
| PUT    | /{id} | Actualiza un tipo de material.     |
| DELETE | /{id} | Elimina un tipo de material.       |

#### Ejemplos (Tipos de Material)

**POST /api/tipo-materiales (Crear)**

Body (Request):

```json
{
    "nombre": "PDF",
    "extension_permitida": "pdf"
}
```

Respuesta (201 Created):

```json
{
    "message": "Tipo de material creado exitosamente",
    "data": {}
}
```

### 8. Materiales

**Ruta Base:** `/api/materiales`

| Método | Ruta  | Descripción                         |
| ------ | ----- | ----------------------------------- |
| GET    | /     | Lista todos los materiales activos. |
| GET    | /{id} | Muestra un material específico.     |
| POST   | /     | Crea un nuevo material.             |
| PUT    | /{id} | Actualiza un material existente.    |
| DELETE | /{id} | Elimina un material.                |

#### Ejemplos (Materiales)

**POST /api/materiales (Crear)**

Body (Request):

```json
{
    "titulo": "Catálogo PDF 2025",
    "archivo_url": "https://cdn.example.com/catalogo.pdf",
    "tipo_material_id": 1
}
```

Respuesta (201 Created):

```json
{
    "message": "Material creado exitosamente",
    "data": {}
}
```

### 9. Configuraciones

**Ruta Base:** `/api/configuraciones`

| Método | Ruta  | Descripción                                  |
| ------ | ----- | -------------------------------------------- |
| GET    | /     | Lista todas las configuraciones del sistema. |
| GET    | /{id} | Muestra una configuración específica.        |
| POST   | /     | Crea una nueva configuración.                |
| PUT    | /{id} | Actualiza una configuración existente.       |
| DELETE | /{id} | Elimina una configuración.                   |

#### Ejemplos (Configuraciones)

**POST /api/configuraciones (Crear)**

Body (Request):

```json
{
    "clave": "porcentaje_comision",
    "valor": "10.5",
    "descripcion": "Porcentaje de comisión para vendedores de nivel 1."
}
```

Respuesta (201 Created):

```json
{
    "message": "Configuración creada exitosamente",
    "data": {}
}
```
