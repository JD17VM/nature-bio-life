# 📬 Implementación de Notificaciones - Fase 1

**Fecha de Implementación:** 14 de Abril de 2026  
**Estado:** ✅ COMPLETADO

---

## 1. Resumen Ejecutivo

Se implementó un sistema de notificaciones basado en **Laravel Notifications** con almacenamiento en base de datos (tabla `notifications`). El sistema utiliza el enfoque de **API simple con polling** (no real-time) como fue decidido anteriormente.

**Componentes creados:**

- ✅ 5 clases de notificación (Notification)
- ✅ Disparadores (notify() calls) en 3 controladores
- ✅ Integración con infraestructura existente (tabla + controller)

---

## 2. Clases de Notificación Creadas

### 📦 Directorio: `app/Notifications/Pedidos/`

#### a) **PedidoCreado.php**

**Cuándo se dispara:** Cuando `PedidoController->store()` crea un nuevo pedido  
**Destinatario:** Usuario que realizó la compra  
**Mensaje:**

```
"Tu pedido {numero} por ${total} ha sido recibido. Ganarás {puntos} puntos cuando se complete."
```

**Datos guardados:**

- `titulo`: "Pedido Creado"
- `mensaje`: Descripción del pedido con número, total y puntos

#### b) **PedidoCompletado.php**

**Cuándo se dispara:** Cuando `PedidoController->actualizarEstado()` cambia estado a ENTREGADO  
**Destinatario:** Usuario propietario del pedido  
**Mensaje:**

```
"Tu pedido {numero} fue completado por nuestro equipo. Se acreditarán {puntos} puntos en tu cuenta."
```

**Datos guardados:**

- `titulo`: "¡Pedido Completado!"
- `mensaje`: Confirmación de entrega y puntos acreditados

---

### 📦 Directorio: `app/Notifications/Canjes/`

#### c) **CanjeCreado.php**

**Cuándo se dispara:** Cuando `CanjePremioController->store()` crea un nuevo canje  
**Destinatario:** Usuario que solicita el canje  
**Mensaje:**

```
"Tu canje por '{premio}' ({puntos} puntos) ha sido registrado. Espera la aprobación de nuestro equipo."
```

**Datos guardados:**

- `titulo`: "Canje Solicitado"
- `mensaje`: Detalles del canje (premio y puntos)

#### d) **CanjeAprobado.php**

**Cuándo se dispara:** Cuando `CanjePremioController->update()` cambia estado a APROBADO  
**Destinatario:** Usuario propietario del canje  
**Mensaje:**

```
"Tu canje por '{premio}' fue aprobado. Se enviará en 3-5 días hábiles."
```

**Datos guardados:**

- `titulo`: "¡Canje Aprobado!"
- `mensaje`: Confirmación de aprobación con plazo de entrega

---

### 📦 Directorio: `app/Notifications/Comisiones/`

#### e) **ComisionGenerada.php**

**Cuándo se dispara:** Cuando `ComisionController->store()` crea una nueva comisión  
**Destinatario:** Vendedor/Patrocinador que gana la comisión  
**Mensaje:**

```
"¡Comisión de ${monto} ({porcentaje}%) por la venta de {producto} a {cliente}!"
```

**Datos guardados:**

- `titulo`: "¡Comisión Ganada!"
- `mensaje`: Detalles de la comisión (monto, porcentaje, cliente, producto)

---

## 3. Disparadores Implementados en Controladores

### 🎯 **PedidoController.php**

#### Cambio 1: Import

```php
use App\Notifications\Pedidos\PedidoCreado;
use App\Notifications\Pedidos\PedidoCompletado;
```

#### Cambio 2: Método `store()`

**Ubicación:** Al final de la transacción, después de crear el pedido  
**Código agregado:**

```php
// 4. Enviar notificación al usuario
$resultado->usuario->notify(new PedidoCreado($resultado));
```

**Efecto:** Cada vez que se cree un pedido, el usuario recibe una notificación en la tabla `notifications`.

#### Cambio 3: Método `actualizarEstado()`

**Ubicación:** Dentro del bloque `if ($nuevoEstado === EstadoPedidoEnum::ENTREGADO)`  
**Código agregado:**

```php
// Notificar al usuario que su pedido fue completado
$pedido->usuario->notify(new PedidoCompletado($pedido));
```

**Efecto:** Solo cuando el estado cambia a ENTREGADO, se dispara la notificación.

---

### 🎯 **CanjePremioController.php**

#### Cambio 1: Imports

```php
use App\Notifications\Canjes\CanjeCreado;
use App\Notifications\Canjes\CanjeAprobado;
```

#### Cambio 2: Método `store()`

**Ubicación:** Después de descontar stock  
**Código agregado:**

```php
// 4. Enviar notificación al usuario
$canje->usuario->notify(new CanjeCreado($canje));
```

**Efecto:** Cada canje registrado genera una notificación al usuario.

#### Cambio 3: Método `update()`

**Ubicación:** Después de actualizar el estado  
**Código agregado:**

```php
$datosValidados = $request->validated();
$estadoAnterior = $canjePremio->estado;
$canjePremio->update($datosValidados);

// Si el nuevo estado es aprobado, notificar al usuario
if ($canjePremio->estado === EstadoCanjeEnum::APROBADO && $estadoAnterior !== EstadoCanjeEnum::APROBADO) {
    $canjePremio->usuario->notify(new CanjeAprobado($canjePremio));
}
```

**Efecto:** Solo dispara cuando la transición es hacia APROBADO (previene duplicados).

---

### 🎯 **ComisionController.php**

#### Cambio 1: Import

```php
use App\Notifications\Comisiones\ComisionGenerada;
```

#### Cambio 2: Método `store()`

**Ubicación:** Después de crear la comisión  
**Código agregado:**

```php
$comision = Comision::create($request->validated());

// Cargar las relaciones necesarias y disparar notificación al vendedor
$comision->load(['vendedor', 'pedido.detalles.producto', 'comprador']);
$comision->vendedor->notify(new ComisionGenerada($comision));
```

**Efecto:** El vendedor recibe una notificación cuando se genera una comisión.

---

## 4. Flujo de Datos - Cómo Funciona

### 4.1 Creación de Notificación

```
┌─────────────────────────────────────────────────┐
│ 1. Usuario realiza acción (crear pedido, canje) │
└────────────┬────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────┐
│ 2. Controlador ejecuta ->notify(Notification)   │
└────────────┬────────────────────────────────────┘
             │
             ▼
┌──────────────────────────────────────────────────┐
│ 3. Laravel crea registro en tabla notifications │
│    (via() retorna ['database'])                  │
└────────────┬──────────────────────────────────────┘
             │
             ▼
┌──────────────────────────────────────────────────┐
│ 4. toArray() prepara JSON con titulo + mensaje  │
│    Se guarda en campo 'data' (estructura fija)   │
└──────────────────────────────────────────────────┘
```

### 4.2 Lectura de Notificaciones (Polling)

```
┌──────────────────────────────────────────────────┐
│ Frontend polling cada 30-60 segundos             │
└────────────┬─────────────────────────────────────┘
             │
             ▼
┌──────────────────────────────────────────────────┐
│ GET /api/notificaciones (NotificacionController)│
└────────────┬─────────────────────────────────────┘
             │
             ▼
┌──────────────────────────────────────────────────┐
│ Retorna JSON con todas las notificaciones del    │
│ usuario autenticado (usa auth()->user()->id())   │
└──────────────────────────────────────────────────┘
```

---

## 5. Infraestructura Utilizada (Ya Existente)

### 5.1 Tabla `notifications`

```sql
CREATE TABLE notifications (
    id UUID PRIMARY KEY,
    type VARCHAR(255),              -- Nombre de la clase (e.g., "App\Notifications\Pedidos\PedidoCreado")
    notifiable_type VARCHAR(255),   -- "App\Models\User"
    notifiable_id BIGINT,           -- ID del usuario destinatario
    data JSON,                      -- toArray() output
    read_at TIMESTAMP,              -- NULL si no leída
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 5.2 Modelo User (Ya tiene Notifiable trait)

```php
class User extends Authenticatable
{
    use Notifiable;  // ← Permite $user->notify()
}
```

### 5.3 Controlador NotificacionController (Ya funcional)

```php
class NotificacionController extends Controller
{
    public function index();          // GET todos
    public function unread();         // GET no leídas
    public function markAsRead();     // PATCH marcar como leída
    public function markAllAsRead();  // PATCH todas como leídas
}
```

---

## 6. Validación de Sintaxis

✅ **PedidoCreado.php** - Sin errores  
✅ **PedidoCompletado.php** - Sin errores  
✅ **CanjeCreado.php** - Sin errores  
✅ **CanjeAprobado.php** - Sin errores  
✅ **ComisionGenerada.php** - Sin errores

✅ **PedidoController.php** - Sin errores  
✅ **CanjePremioController.php** - Sin errores  
✅ **ComisionController.php** - Sin errores

---

## 7. Punto Clave - Estructura Predecible para Frontend

**El `data` siempre tiene estructura igual:**

```json
{
    "titulo": "Texto identificativo",
    "mensaje": "Descripción completa y amigable"
}
```

**Esto permite:**

- ✅ Tipado TypeScript simple y consistente
- ✅ UI genérica que funciona para todas las notificaciones
- ✅ No necesita condicionales para mostrar diferentes campos
- ✅ Si necesita detalles, usa el `type` para saber hacia dónde navegar

**Ejemplo de lógica Frontend:**

```typescript
interface Notification {
  id: string;
  type: string; // "App\Notifications\Pedidos\PedidoCreado", etc
  data: {
    titulo: string;
    mensaje: string;
  };
  read_at: string | null;
  created_at: string;
}

// Mostrar todas igual
notificaciones.map(notif => (
  <NotificacionCard
    title={notif.data.titulo}
    message={notif.data.mensaje}
    onPress={() => {
      // Usar el type para navegar a la pantalla correcta
      if (notif.type.includes('Pedido')) {
        navigation.navigate('Pedidos');
      } else if (notif.type.includes('Canje')) {
        navigation.navigate('Canjes');
      } else if (notif.type.includes('Comision')) {
        navigation.navigate('Comisiones');
      }
    }}
  />
))
```

---

## 8. Próximos Pasos (No Implementados Aún)

### Fase 2: Notificaciones Adicionales (Opcional)

- [ ] `PedidoCancelado.php` - Cuando pedido es cancelado
- [ ] `CanjeRechazado.php` - Cuando canje es rechazado
- [ ] `ReferidoRegistrado.php` - Cuando un usuario es referido
- [ ] `ReferidoCompro.php` - Cuando un referido realiza compra

### Fase 3: Features Avanzadas

- [ ] WebSocket real-time (para reemplazar polling)
- [ ] Notificaciones por email (`->via(['database', 'mail'])`)
- [ ] Notificaciones por SMS (usando Twilio)
- [ ] Plantillas dinámicas con variables

---

## 9. Cómo Testear

### 9.1 Via Postman/cURL

**1. Crear un pedido (dispara PedidoCreado):**

```bash
POST /api/pedidos
Authorization: Bearer {token}
Content-Type: application/json

{
  "detalles": [
    {"producto_id": 1, "cantidad": 2}
  ]
}
```

**2. Leer notificaciones (frontend):**

```bash
GET /api/notificaciones
Authorization: Bearer {token}
```

**Response esperado (estructura estandarizada para TODAS las notificaciones):**

El `data` contiene SOLO `titulo` y `mensaje` para facilitar tipado consistente en el frontend. Si el frontend necesita detalles adicionales, hace otra llamada a ese recurso específico (ej: GET /api/pedidos/{id}).

### Ejemplo Universal

```json
[
    {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "type": "App\\Notifications\\Pedidos\\PedidoCreado",
        "notifiable_type": "App\\Models\\User",
        "notifiable_id": 5,
        "data": {
            "titulo": "Pedido Creado",
            "mensaje": "Tu pedido ORD-ABC123XYZ por $150.00 ha sido recibido. Ganarás 30 puntos cuando se complete."
        },
        "read_at": null,
        "created_at": "2026-04-14T15:30:00Z"
    },
    {
        "id": "660f9410-f40c-52e5-b827-557766551111",
        "type": "App\\Notifications\\Canjes\\CanjeAprobado",
        "notifiable_type": "App\\Models\\User",
        "notifiable_id": 7,
        "data": {
            "titulo": "¡Canje Aprobado!",
            "mensaje": "Tu canje por 'PlayStation 5' fue aprobado. Se enviará en 3-5 días hábiles."
        },
        "read_at": null,
        "created_at": "2026-04-14T16:15:00Z"
    },
    {
        "id": "770g0521-g51d-63f6-c938-668877662222",
        "type": "App\\Notifications\\Comisiones\\ComisionGenerada",
        "notifiable_type": "App\\Models\\User",
        "notifiable_id": 3,
        "data": {
            "titulo": "¡Comisión Ganada!",
            "mensaje": "Generaste una comisión de $75.00 (10%) por la venta de Vitaminas Premium a Juan García."
        },
        "read_at": null,
        "created_at": "2026-04-14T17:00:00Z"
    }
]
```

**Ventajas de esta estructura:**
✅ Tipado consistente en TypeScript (siempre tiene estructura igual)
✅ Frontend NO necesita detalles específicos en la notificación
✅ Si necesita detalles, hace otra llamada: GET /api/pedidos/{id}, GET /api/canjes/{id}, etc
✅ Respuesta más limpia y ligera
✅ Fácil de mostrar en UI genérica (título + mensaje)

**3. Marcar como leída:**

```bash
PATCH /api/notificaciones/{id}/leer
Authorization: Bearer {token}
```

---

## 10. Archivos Modificados

| Archivo                                              | Tipo       | Cambios                       |
| ---------------------------------------------------- | ---------- | ----------------------------- |
| `app/Http/Controllers/Api/PedidoController.php`      | Modificado | +2 imports, +2 notify() calls |
| `app/Http/Controllers/Api/CanjePremioController.php` | Modificado | +2 imports, +2 notify() calls |
| `app/Http/Controllers/Api/ComisionController.php`    | Modificado | +1 import, +1 notify() call   |
| `app/Notifications/Pedidos/PedidoCreado.php`         | **Creado** | 30 líneas                     |
| `app/Notifications/Pedidos/PedidoCompletado.php`     | **Creado** | 32 líneas                     |
| `app/Notifications/Canjes/CanjeCreado.php`           | **Creado** | 35 líneas                     |
| `app/Notifications/Canjes/CanjeAprobado.php`         | **Creado** | 35 líneas                     |
| `app/Notifications/Comisiones/ComisionGenerada.php`  | **Creado** | 40 líneas                     |

**Total:** 5 archivos creados + 3 archivos modificados

---

## 11. Conclusión

✅ **Implementación completada con estructura estandarizada**

El sistema de notificaciones **Fase 1** está listo y optimizado para el frontend:

- ✅ Todas las clases de notificación compiladas sin errores
- ✅ Estructura `data` consistente: SOLO `titulo` y `mensaje`
- ✅ Tipado predecible para React Native/TypeScript
- ✅ Sin necesidad de condicionales complejos en UI
- ✅ Compatible con infraestructura existente (cero cambios de BD)
- ✅ Soporta polling desde frontend (30-60 segundos)
- ✅ Escalable para agregar más tipos de notificaciones

**Cómo llegamos a esto:**

1. Inicialmente las clases guardaban todos los detalles
2. Simplificamos a SOLO `titulo` + `mensaje` para facilitar UI genérica
3. Frontend usa el campo `type` para saber hacia dónde navegar
4. Si necesita detalles completos, hace llamadas separadas a cada recurso

**Próximo paso sugerido:** Probar end-to-end con Postman y verificar que la estructura es correcta.
