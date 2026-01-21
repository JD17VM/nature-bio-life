# Guía Técnica: Manejo de Archivos y Storage

Esta sección cubre la configuración necesaria para que la API pueda servir imágenes y cómo probar la subida de archivos correctamente.

## 1. Habilitar Visibilidad de Imágenes (Solución Error 404)

Por defecto, Laravel guarda los archivos en `storage/app/public`, una carpeta que **no es accesible** desde el navegador por seguridad. Para hacerlas públicas, se debe crear un "enlace simbólico" (acceso directo) hacia la carpeta `public`.

Si al intentar ver una imagen obtienes un error **404 Not Found**, ejecuta este comando:

**En entorno Docker:**
```bash
docker-compose exec app php artisan storage:link
```

**En entorno Local (sin Docker):**
```bash
php artisan storage:link
```

> **Nota:** Este comando solo necesita ejecutarse **una vez** en la vida del proyecto (o cada vez que se despliegue en un servidor nuevo).

---

## 2. Cómo probar subida de archivos en Postman

Dado que los endpoints de creación (`POST`) ahora esperan archivos binarios, no se puede usar el formato JSON crudo (`raw`).

**Configuración correcta en Postman:**
1.  **Método:** `POST`
2.  **Headers:** `Accept: application/json`
3.  **Body:** Seleccionar la pestaña **form-data**.
4.  **Campo de Imagen:**
    *   Escribir el nombre del campo (ej: `imagen` o `archivo`).
    *   Pasar el mouse sobre la celda "Key" y cambiar el selector de **Text** a **File**.
    *   Seleccionar la imagen desde tu computadora.