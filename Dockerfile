FROM php:8.2-cli

# Instalar dependencias del sistema y extensiones necesarias para PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_pgsql pgsql bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir directorio de trabajo
WORKDIR /var/www

# Copiar primero solo composer files para aprovechar la caché de capas
COPY composer.json composer.lock ./

# Instalar dependencias de PHP (sin scripts para que no falle sin .env)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copiar el resto del código
COPY . .

# Ejecutar scripts post-install manualmente
RUN php artisan package:discover --ansi || true

# Dar permisos a las carpetas de almacenamiento y caché
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Exponer el puerto
EXPOSE 10000

# Script de entrada
CMD ["sh", "docker-entrypoint.sh"]
