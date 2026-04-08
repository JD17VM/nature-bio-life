# Enums de Estado — Nature Bio Life API

Referencia rápida de todos los estados posibles del sistema. El frontend debe usar estos valores exactos al enviar o comparar estados.

---

## 1. Estado de Pedido (`EstadoPedidoEnum`)

**Campo:** `pedido.estado`

| Valor (string) | Etiqueta | Descripción |
|----------------|----------|-------------|
| `pendiente` | Pendiente | Pedido creado. El cliente aún no subió comprobante de pago. |
| `verificando_pago` | Verificando Pago | Comprobante subido. El admin lo está revisando. |
| `procesando` | Procesando | Pago confirmado. Se está preparando el despacho. |
| `enviado` | Enviado | Paquete despachado, en camino al cliente. |
| `entregado` | Entregado | Cliente recibió el pedido. **Estado final positivo.** |
| `cancelado` | Cancelado | Pedido cancelado. **Estado final negativo.** |

### Flujo de transiciones permitidas

```
pendiente → verificando_pago → procesando → enviado → entregado
    ↓               ↓               ↓           ↓
 cancelado       cancelado       cancelado   cancelado
```

> **Importante:** El API rechazará cambios de estado que no sigan este flujo.
> Por ejemplo, no se puede pasar de `entregado` de vuelta a `enviado`.

### Valores para enviar al endpoint `PATCH /api/pedidos/{id}/estado`

```json
{ "estado": "verificando_pago" }
{ "estado": "procesando" }
{ "estado": "enviado" }
{ "estado": "entregado" }
{ "estado": "cancelado" }
```

---

## 2. Estado de Comisión (`EstadoComisionEnum`)

**Campo:** `comision.estado`

| Valor (string) | Etiqueta | Descripción |
|----------------|----------|-------------|
| `pendiente` | Pendiente | Comisión generada automáticamente al confirmar un pedido. Esperando revisión del admin. |
| `aprobada` | Aprobada | Admin aprobó la comisión. Lista para pagar. |
| `pagada` | Pagada | Monto abonado al vendedor. **Estado final positivo.** |
| `anulada` | Anulada | Comisión anulada (ej: pedido cancelado). **Estado final negativo.** |

### Flujo

```
pendiente → aprobada → pagada
    ↓
  anulada
```

---

## 3. Estado de Canje de Premio (`EstadoCanjeEnum`)

**Campo:** `canje_premio.estado`

| Valor (string) | Etiqueta | Descripción |
|----------------|----------|-------------|
| `pendiente` | Pendiente | Solicitud de canje recibida. Esperando revisión del admin. |
| `aprobado` | Aprobado | Admin aprobó el canje. Se está preparando la entrega. |
| `entregado` | Entregado | Premio entregado al usuario. **Estado final positivo.** |
| `rechazado` | Rechazado | Solicitud rechazada por admin. **Estado final negativo.** |

### Flujo

```
pendiente → aprobado → entregado
    ↓
 rechazado
```

---

## 4. Rol de Usuario (`RolUsuarioEnum`)

**Campo:** `user.rol`

| Valor (string) | Etiqueta | Descripción |
|----------------|----------|-------------|
| `admin` | Administrador | Acceso total. Puede gestionar pedidos, comisiones, productos y usuarios. |
| `socio` | Socio | Acceso intermedio. |
| `vendedor` | Vendedor | Acceso básico. Solo ve sus propios pedidos, comisiones y referidos. |

### Cómo usarlo para proteger rutas en el frontend

El campo `rol` viene en la respuesta del login y del perfil:

```json
{
  "user": {
    "id": 1,
    "nombre_completo": "Juan Pérez",
    "email": "juan@example.com",
    "rol": "vendedor",
    ...
  },
  "access_token": "..."
}
```

Compara `user.rol === 'admin'` para mostrar/ocultar secciones de administración.
