# Comisiones Automáticas por Referidos

El sistema genera comisiones automáticamente sin intervención manual cuando se cambia el estado de un pedido.

---

## Cómo funciona

### Al marcar un pedido como `entregado`

Cuando el admin cambia el estado a `entregado` mediante `PATCH /api/pedidos/{id}/estado`, el sistema:

1. Busca si el comprador del pedido tiene un patrocinador (`patrocinador_id`).
2. Si tiene patrocinador, consulta el porcentaje de comisión en la tabla `configuraciones` (clave: `porcentaje_comision`). Si no existe esa configuración, usa **10%** como valor por defecto.
3. Crea automáticamente un registro en `comisiones` con estado `pendiente`.
4. **Previene duplicados:** si ya existe una comisión para ese pedido, no genera otra.

### Al marcar un pedido como `cancelado`

El sistema anula automáticamente todas las comisiones en estado `pendiente` asociadas a ese pedido, cambiándolas a `anulada`.

---

## Flujo completo

```
Admin: PATCH /api/pedidos/12/estado  { "estado": "entregado" }
         │
         ├─ pedido.estado → "entregado"
         ├─ estado_pedidos → nuevo registro en historial
         │
         └─ ¿comprador tiene patrocinador?
               │
               ├─ NO → nada más
               │
               └─ SÍ → comisiones (nuevo registro)
                          vendedor_id  = patrocinador_id del comprador
                          comprador_id = user_id del pedido
                          monto_compra = pedido.total
                          porcentaje   = configuraciones['porcentaje_comision'] ?? 10%
                          monto_comision = monto_compra * porcentaje / 100
                          estado       = "pendiente"
```

---

## Configurar el porcentaje de comisión

El porcentaje se lee desde la tabla `configuraciones`. Para cambiarlo:

```
POST /api/configuraciones
{
    "clave": "porcentaje_comision",
    "valor": "15",
    "descripcion": "Porcentaje de comisión para patrocinadores nivel 1"
}
```

O si ya existe, actualizar con:
```
PUT /api/configuraciones/{id}
{
    "valor": "15"
}
```

> Si no existe la configuración `porcentaje_comision`, el sistema usa **10%** por defecto.

---

## Estados de una comisión

Ver [Enums de Estado](./enums-estados.md) — sección "Estado de Comisión".

El flujo típico de una comisión es:
```
pendiente → aprobada → pagada
```

El admin aprueba y paga las comisiones manualmente a través de `PUT /api/comisiones/{id}`.

---

## Ejemplo de respuesta al entregar un pedido con comisión generada

`PATCH /api/pedidos/12/estado`
```json
{ "estado": "entregado", "observaciones": "Entregado en domicilio" }
```

Respuesta `200 OK`:
```json
{
    "message": "Estado del pedido actualizado a \"Entregado\" exitosamente.",
    "data": {
        "id": 12,
        "numero_pedido": "ORD-ABCDE12345",
        "estado": "entregado",
        "total": "180.00",
        "detalles": [ ... ],
        "estados": [
            { "estado": "pendiente", "fecha_cambio": "..." },
            { "estado": "verificando_pago", "fecha_cambio": "..." },
            { "estado": "entregado", "fecha_cambio": "...", "observaciones": "Entregado en domicilio" }
        ]
    }
}
```

> La comisión generada se puede ver en `GET /api/comisiones` o en `GET /api/referidos/{id}`.
