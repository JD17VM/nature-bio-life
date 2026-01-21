# ==============================================================================
# STAGE 1: Construcción de dependencias (Composer)
# Objetivo: Optimizar caché de capas Docker separando la instalación de vendors
# ==============================================================================
FROM composer:2.6 as vendor

WORKDIR /app

# Copiamos solo archivos de definición primero para aprovechar caché de Docker
COPY composer.json composer.lock ./

# Instalamos dependencias (sin scripts ni autoloader por ahora para velocidad)
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# ==============================================================================
# STAGE 2: Imagen Final de Aplicación (PHP-FPM)
# Objetivo: Imagen ligera y segura para ejecución
# ==============================================================================
FROM php:8.1-fpm-alpine

# Argumentos de construcción
ARG UID=1000
ARG GID=1000

# Instalamos dependencias del sistema requeridas para extensiones PHP
# libpng-dev, libzip-dev, oniguruma-dev son necesarios para compilar extensiones
RUN apk add --no-cache \
    linux-headers \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    shadow \
    curl

# Instalamos extensiones PHP requeridas por Laravel y el proyecto
# pdo_mysql: Conexión a BD
# mbstring: Manejo de strings multibyte
# pcntl: Necesario para workers de colas (si se usan en futuro)
# bcmath: Operaciones matemáticas precisas (común en e-commerce/multinivel)
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    opcache \
    xml \
    zip

# Configuración de usuario no-root para seguridad y evitar problemas de permisos
# Creamos grupo y usuario 'laravel' coincidiendo con UID/GID del host (usualmente 1000)
RUN groupadd -g "${GID}" laravel && \
    useradd -u "${UID}" -ms /bin/sh -g laravel laravel

# Copiamos Composer desde el stage anterior (multi-stage build)
COPY --from=vendor /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Ajustes de configuración PHP recomendados para desarrollo
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=50M" > /usr/local/etc/php/conf.d/post.ini

# Creamos directorios necesarios y asignamos permisos
# Es importante que el usuario 'laravel' sea dueño de donde escribirá logs y caché
RUN chown -R laravel:laravel /var/www/html

# Cambiamos al usuario sin privilegios
USER laravel

# Exponemos puerto 9000 (FPM default)
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]