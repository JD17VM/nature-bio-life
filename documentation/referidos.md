# Endpoints de Referidos

Permiten ver la red de usuarios que se registraron usando tu `codigo_referido`.  
Todos los endpoints requieren autenticación con Bearer Token.

**URL Base:** `http://127.0.0.1:8000/api`

---

## 1. Listar mis referidos directos

**`GET /api/referidos`** — Requiere: `Authorization: Bearer {token}`

Devuelve todos los usuarios que se registraron con tu código de referido, paginados.

### Query params opcionales

| Param | Default | Descripción |
|-------|---------|-------------|
| `page` | 1 | Página actual |
| `per_page` | 15 | Items por página (máx. 100) |
| `patrocinador_id` | — | Solo para Admin: filtra por patrocinador |

### Respuesta (200 OK)

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 5,
            "nombre_completo": "María García",
            "email": "maria@example.com",
            "telefono": "987111222",
            "rol": "vendedor",
            "codigo_referido": "XKQP123456",
            "activo": true,
            "created_at": "2026-03-01T10:00:00.000000Z",
            "total_pedidos": 3,
            "total_compras": "540.00",
            "total_sub_referidos": 2,
            "total_comisiones_generadas": 54.00
        }
    ],
    "last_page": 1,
    "per_page": 15,
    "total": 1
}
```

### Campos del objeto referido

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `total_pedidos` | int | Cantidad de pedidos que ha hecho este referido |
| `total_compras` | decimal | Suma total en soles de todos sus pedidos |
| `total_sub_referidos` | int | Cuántos usuarios ha referido él a su vez |
| `total_comisiones_generadas` | decimal | Total de comisiones que me generó a mí |

---

## 2. Ver detalle de un referido

**`GET /api/referidos/{id}`** — Requiere: `Authorization: Bearer {token}`

Devuelve información completa de un referido específico: sus datos, sus pedidos y las comisiones que te generó.

> Solo puedes ver el detalle de usuarios que son tus referidos directos. El Admin puede ver cualquiera.

### Query params opcionales

| Param | Default | Descripción |
|-------|---------|-------------|
| `page` | 1 | Página del listado de pedidos |
| `per_page` | 10 | Pedidos por página (máx. 50) |

### Respuesta (200 OK)

```json
{
    "referido": {
        "id": 5,
        "nombre_completo": "María García",
        "email": "maria@example.com",
        "telefono": "987111222",
        "rol": "vendedor",
        "rol_label": "Vendedor",
        "codigo_referido": "XKQP123456",
        "activo": true,
        "miembro_desde": "2026-03-01T10:00:00.000000Z"
    },
    "resumen": {
        "total_pedidos": 3,
        "total_compras": 540.00,
        "total_comisiones_generadas": 54.00,
        "comisiones_pendientes": 18.00,
        "comisiones_pagadas": 36.00
    },
    "pedidos": {
        "current_page": 1,
        "data": [
            {
                "id": 12,
                "numero_pedido": "ORD-ABCDE12345",
                "total": "180.00",
                "estado": "entregado",
                "created_at": "2026-03-15T14:00:00.000000Z",
                "detalles": [ ... ]
            }
        ],
        "last_page": 1,
        "per_page": 10,
        "total": 3
    },
    "comisiones": [
        {
            "id": 8,
            "monto_compra": "180.00",
            "porcentaje": "10.00",
            "monto_comision": "18.00",
            "estado": "pagada",
            "fecha_generacion": "2026-03-15T14:05:00.000000Z",
            "pedido": {
                "id": 12,
                "numero_pedido": "ORD-ABCDE12345",
                "total": "180.00",
                "estado": "entregado"
            }
        }
    ]
}
```

### Respuesta si el usuario no es tu referido (403)

```json
{
    "message": "Este usuario no es tu referido."
}
```

---

## Notas para el frontend

- Usa `resumen.total_comisiones_generadas` para mostrar el valor total que te aportó ese referido.
- Usa `resumen.comisiones_pendientes` para destacar cuánto está por cobrar.
- El campo `total_sub_referidos` en el listado permite mostrar si un referido tuyo también está construyendo su propia red.
