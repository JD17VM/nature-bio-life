
# Simulación de Diagrama de Base de Datos del Proyecto

A continuación se presenta una simulación textual del diagrama de la base de datos, extraída del análisis de los archivos de migración de Laravel.

---

## Tabla: `users`
Esta tabla es central y gestiona toda la información de los usuarios del sistema.

**Atributos Principales:**
- `id`: (Primaria) Identificador único del usuario.
- `nombre_completo`: Nombre del usuario.
- `email`: Correo electrónico (único).
- `password`: Contraseña hasheada.
- `rol`: Rol del usuario (`admin`, `patrocinador`, `cliente`).
- `codigo_referido`: Código único que el usuario comparte para referir a otros.
- `patrocinador_id`: (Foránea) Referencia a otro usuario en la misma tabla (`users.id`). Representa quién lo refirió.
- `activo`: Booleano para indicar si el usuario está activo.
- `created_at` / `updated_at`: Fechas de registro y actualización.

**Relaciones:**
- **Auto-referencia (Opcional):** Un usuario (`patrocinador_id`) puede patrocinar a muchos otros usuarios. Si un patrocinador es eliminado, el `patrocinador_id` de sus referidos se establece en `NULL`.

---

## Tablas de Productos y Catálogos

### Tabla: `categorias`
Agrupa los productos en diferentes clasificaciones.
- `id`: (Primaria) ID de la categoría.
- `nombre`: Nombre de la categoría (ej: "Suplementos").
- `activa`: Booleano para habilitar/deshabilitar la categoría.

### Tabla: `productos`
Contiene la información de los artículos que se pueden comprar.
- `id`: (Primaria) ID del producto.
- `nombre`: Nombre del producto.
- `precio`: Precio de venta.
- `stock`: Cantidad disponible.
- `puntos`: Puntos que otorga la compra de este producto.
- `categoria_id`: (Foránea) Enlaza con `categorias.id`.
- `imagen_url`: URL de la imagen del producto.
- `activo`: Booleano para indicar si el producto está visible.

**Relaciones:**
- **`categorias` (1) -> `productos` (N):** Una categoría puede tener muchos productos. La relación es restrictiva para evitar que se elimine una categoría si tiene productos asociados.

---

## Tablas de Pedidos y Comisiones

### Tabla: `pedidos`
Registra cada orden de compra realizada por un usuario.
- `id`: (Primaria) ID del pedido.
- `numero_pedido`: Código único de la orden (ej: "ORD-001").
- `user_id`: (Foránea) El usuario que realizó el pedido (`users.id`).
- `total`: Monto total de la compra.
- `puntos_ganados`: Total de puntos obtenidos en esta orden.
- `estado`: Estado actual del pedido (ej: "pendiente", "pagado").

**Relaciones:**
- **`users` (1) -> `pedidos` (N):** Un usuario puede tener muchos pedidos. Si se elimina un usuario, sus pedidos también se eliminan (`cascadeOnDelete`).

### Tabla: `detalle_pedidos`
Describe qué productos y en qué cantidad componen cada pedido.
- `id`: (Primaria) ID del detalle.
- `pedido_id`: (Foránea) El pedido al que pertenece este detalle (`pedidos.id`).
- `producto_id`: (Foránea) El producto comprado (`productos.id`).
- `cantidad`: Número de unidades compradas.
- `precio_unitario`: Precio del producto en el momento de la compra.

**Relaciones:**
- **`pedidos` (1) -> `detalle_pedidos` (N):** Un pedido está compuesto por múltiples líneas de detalle.
- **`productos` (1) -> `detalle_pedidos` (N):** Un producto puede estar en muchos pedidos diferentes.

### Tabla: `comisiones`
Registra las comisiones generadas por la venta de un referido.
- `id`: (Primaria) ID de la comisión.
- `vendedor_id`: (Foránea) El usuario que gana la comisión (`users.id`).
- `comprador_id`: (Foránea) El usuario referido que realizó la compra (`users.id`).
- `pedido_id`: (Foránea) El pedido que generó esta comisión (`pedidos.id`).
- `monto_comision`: Dinero ganado por el vendedor.
- `estado`: Estado de la comisión (ej: "pendiente", "pagada").

**Relaciones:**
- **`pedidos` (1) -> `comisiones` (1):** Un pedido genera una comisión para el patrocinador del comprador.
- **`users` (1) -> `comisiones` (N):** Un usuario (vendedor) puede recibir múltiples comisiones.

---

## Tablas de Puntos y Premios

### Tabla: `categoria_premios`
Clasifica los premios canjeables.
- `id`: (Primaria) ID de la categoría de premio.
- `nombre`: Nombre de la categoría (ej: "Viajes", "Electrónica").

### Tabla: `premios`
Contiene los premios que los usuarios pueden canjear con sus puntos.
- `id`: (Primaria) ID del premio.
- `nombre`: Nombre del premio.
- `puntos_requeridos`: Puntos necesarios para canjearlo.
- `stock`: Cantidad disponible del premio.
- `categoria_premio_id`: (Foránea) Enlaza con `categoria_premios.id`.

**Relaciones:**
- **`categoria_premios` (1) -> `premios` (N):** Una categoría puede agrupar varios premios.

### Tabla: `historial_puntos`
Lleva un registro de todos los movimientos de puntos (ganados y gastados) de un usuario.
- `id`: (Primaria) ID del movimiento.
- `user_id`: (Foránea) El usuario al que pertenece el movimiento (`users.id`).
- `pedido_id`: (Foránea, opcional) El pedido que generó los puntos.
- `puntos`: Cantidad de puntos (positivo si es ganancia, negativo si es gasto).
- `tipo`: `'ingreso'` o `'egreso'`.
- `balance_anterior` / `balance_nuevo`: Saldo de puntos antes y después del movimiento.

**Relaciones:**
- **`users` (1) -> `historial_puntos` (N):** Un usuario tiene un historial de múltiples movimientos de puntos.

### Tabla: `canje_premios`
Registra el canje de un premio por parte de un usuario.
- `id`: (Primaria) ID del canje.
- `user_id`: (Foránea) El usuario que realiza el canje (`users.id`).
- `premio_id`: (Foránea) El premio que se ha canjeado (`premios.id`).
- `puntos_utilizados`: Puntos que costó el canje.
- `estado`: Estado del canje (ej: "aprobado", "entregado").

**Relaciones:**
- **`users` (1) -> `canje_premios` (N):** Un usuario puede canjear múltiples premios.
- **`premios` (1) -> `canje_premios` (N):** Un premio puede ser canjeado por muchos usuarios.

---

## Tablas de Contenido Educativo (Videos y Material)

### Tabla: `categoria_videos`
Clasifica los videos educativos.
- `id`: (Primaria) ID de la categoría del video.
- `nombre`: Nombre de la categoría (ej: "Capacitación de Producto").

### Tabla: `videos`
Almacena la información de los videos.
- `id`: (Primaria) ID del video.
- `titulo`: Título del video.
- `url`: Enlace al video.
- `categoria_video_id`: (Foránea) Enlaza con `categoria_videos.id`.

**Relaciones:**
- **`categoria_videos` (1) -> `videos` (N):** Una categoría contiene múltiples videos.

### Tabla: `video_user` (Tabla Pivote)
Registra el progreso de un usuario en un video. Es una relación **Muchos a Muchos**.
- `user_id`: (Foránea) ID del usuario (`users.id`).
- `video_id`: (Foránea) ID del video (`videos.id`).
- `segundo_actual`: Último segundo de reproducción guardado.
- `completado`: Booleano que indica si el video fue visto por completo.

**Relaciones:**
- **`users` (N) <-> `videos` (N):** Un usuario puede ver muchos videos, y un video puede ser visto por muchos usuarios.

### Tabla: `tipo_materiales`
Clasifica el material de apoyo (ej: "PDF", "Imagen", "Documento").
- `id`: (Primaria) ID del tipo.
- `nombre`: Nombre del tipo.

### Tabla: `materiales`
Almacena enlaces a archivos o recursos de material de apoyo.
- `id`: (Primaria) ID del material.
- `titulo`: Título del material.
- `archivo_url`: Enlace al archivo.
- `tipo_material_id`: (Foránea) Enlaza con `tipo_materiales.id`.

**Relaciones:**
- **`tipo_materiales` (1) -> `materiales` (N):** Un tipo de material puede tener muchos archivos.

---

## Tablas Auxiliares y del Sistema

- **`configuraciones`**: Almacena pares `clave`/`valor` para configuraciones generales del sistema (ej: "costo_envio_general").
- **`estado_pedidos`**: Guarda un historial de los cambios de estado de un pedido (`pedido_id`, `estado`, `fecha_cambio`).
- **`notifications`**: Tabla polimórfica de Laravel para gestionar notificaciones.
- **`password_reset_tokens`**, **`failed_jobs`**, **`personal_access_tokens`**: Tablas estándar de Laravel para funcionalidades del framework.
