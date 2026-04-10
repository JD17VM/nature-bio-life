### ROL ###
Actuarás como un Desarrollador Senior de Laravel y Arquitecto de Software experto. Tu objetivo es redactar un archivo de configuración e instrucciones (típicamente llamado `Claude.md`, `AI_CONTEXT.md` o `.cursorrules`) que servirá como la "biblia" de contexto para que cualquier IA (como tú o Claude) entienda cómo debe comportarse al interactuar con este proyecto.

### CONTEXTO ###
Este proyecto, llamado "Nature Bio Life", es una API REST backend para un sistema de Red de Mercadeo (MLM), gamificación y catálogo de productos. 
A continuación, te proporcionaré los tres pilares del conocimiento de este proyecto. Léelos cuidadosamente para extraer las reglas implícitas y explícitas:

1. Estructura de Directorios:
@jerarquia_archivos.md

2. Contexto General del Proyecto (Lógica de Negocio y Reglas):
# 🌳 Nature Bio Life - Contexto del Proyecto

## 1. Descripción General
API REST backend para un sistema de **Red de Mercadeo (MLM)** y gestión de ventas. El sistema administra usuarios (patrocinadores y clientes), catálogo de productos, gamificación por puntos, canje de premios y seguimiento de visualización de videos educativos.

## 2. Stack Tecnológico
* **Framework:** Laravel 10.x
* **Lenguaje:** PHP 8.1
* **Base de Datos:** MySQL 8.0
* **Autenticación:** Laravel Sanctum (Tokens Bearer)
* **Infraestructura:**
    * Local: Docker (MySQL + PHPMyAdmin)
    * Producción: cPanel (Shared Hosting) con despliegue vía Git/SSH.

## 3. Módulos Principales y Lógica de Negocio

### A. Usuarios y Red (MLM)
* **Roles:** Admin, Patrocinador (Vendedor), Cliente.
* **Lógica de Referidos:** Cada usuario tiene un `patrocinador_id` y un `codigo_referido`. El sistema debe rastrear quién invitó a quién para cálculo de comisiones.

### B. Catálogo y Tienda
* **Categorías:** Clasificación de productos.
* **Productos:** Tienen precio monetario y valor en **Puntos**.
* **Pedidos:** Registro de compras que generan puntos para el usuario.

### C. Gamificación y Premios
* **Puntos:** Moneda interna ganada por compras o acciones.
* **Premios:** Catálogo de artículos canjeables solo con puntos.
* **Reglas:** Validación estricta de saldo de puntos antes de canjear.

### D. Contenido Educativo
* **Videos:** Material audiovisual que los usuarios deben ver.
* **Tracking:** Se registra si el usuario vio el video completo (tabla `video_user`) para desbloquear beneficios o puntos.

## 4. Estructura de Código y Patrones
* **Controladores:** Organizados en subcarpetas por dominio (ej: `app/Http/Controllers/Categoria/`).
* **Validación:** Uso estricto de **FormRequests** separados para `Store` y `Update` (ej: `StoreCategoriaRequest`).
* **Respuestas:** Formato JSON estandarizado.
* **Idioma:**
    * Código (Variables/Métodos): Español (`nombre_completo`, `fecha_nacimiento`).
    * Comentarios: Español.

## 5. Base de Datos (Tablas Clave)
* `users`: Maneja auth, código de referido y saldo actual.
* `productos`: Inventario y asignación de puntos.
* `pedidos` / `detalle_pedidos`: Transacciones.
* `premios` / `canje_premios`: Salida de inventario por puntos.
* `video_user`: Tabla pivote para historial de visualizaciones.

## 6. Reglas de Despliegue (IMPORTANTE)
* El entorno de producción es **cPanel**.
* NO se debe usar `npm run dev` en producción.
* El despliegue se hace vía `git pull` en la carpeta `~/proyecto_laravel`.
* La carpeta pública real es `~/public_html/backend` conectada vía `index.php` modificado.

3. Diagrama de Base de Datos y Relaciones:
@database_diagram.md

### TAREA ###
Redactar un documento Markdown (`Claude.md`) estructurado, claro y directo. Este documento será leído por otra IA antes de cada sesión de programación, por lo que debe estar escrito en un formato de "Instrucciones del Sistema" (usando un tono imperativo: "Debes hacer X", "Nunca hagas Y").

### PASOS A SEGUIR / INSTRUCCIONES DETALLADAS ###
El archivo debe contener, como mínimo, las siguientes secciones:
1. **Resumen Ejecutivo:** ¿Qué es el proyecto y cuál es su stack técnico principal (versiones exactas de PHP y Laravel)?
2. **Contexto de Infraestructura (Docker):** Instrucciones claras sobre cómo se deben ejecutar los comandos. (Ej: Todo comando de Artisan o Composer debe ejecutarse dentro del contenedor Docker).
3. **Reglas Estrictas de Código:** Extrae del contexto proporcionado las reglas de validación (FormRequests obligatorios), respuestas JSON, idioma del código/comentarios, etc.
4. **Flujos Principales:** Un breve resumen de cómo funciona el sistema MLM (patrocinadores), el sistema de puntos/premios y la visualización de videos.
5. **Regla de Oro (Sincronización de Código):** Crea una sección muy destacada e imperativa que obligue a la IA a actualizar los archivos `PROJECT_CONTEXT.md`, `database_diagram.md` y cualquier archivo de jerarquía si realiza cambios en migraciones, modelos, controladores o la estructura de carpetas.
6. **Evolución del Contexto (NUEVO):** Instruye a la IA explícitamente para que actúe de forma proactiva: si durante una sesión de desarrollo se toman nuevas decisiones de arquitectura, se establecen nuevas reglas de negocio o surge información relevante que no estaba en el contexto original, la IA DEBE sugerir y agregar esa información al archivo de contexto correspondiente para que no se pierda en futuras interacciones.

### FORMATO DE SALIDA ###
El resultado debe ser ÚNICAMENTE el contenido del archivo Markdown, listo para ser guardado. Usa encabezados (`#`, `##`, `###`), listas, negritas para enfatizar prohibiciones u obligaciones, y bloques de código cuando sea necesario. 

### REGLAS Y RESTRICCIONES ###
- NO escribas una introducción ni una conclusión fuera del bloque de código Markdown.
- Asume que el lector de este archivo es una Inteligencia Artificial, háblale directamente (ej: "Cuando analices este código...", "Nunca modifiques...").
- Sé muy riguroso con las reglas de actualización de documentos (Pasos 5 y 6).
- Asegúrate de mencionar explícitamente que el entorno de producción es cPanel y cómo eso afecta los comandos (como la prohibición de usar `npm run dev` en producción).

**NOTA PARA EL USUARIO:** Antes de ejecutar este prompt, reemplaza `{INSERTA_AQUÍ_LA_ESTRUCTURA_DEL_PROYECTO}`, `{INSERTA_AQUÍ_EL_CONTEXTO_DEL_PROYECTO}` y `{INSERTA_AQUÍ_EL_DIAGRAMA_DE_BD}` con los textos correspondientes que tienes.