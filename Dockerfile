FROM php:8.2-fpm

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
    && docker-php-ext-install pdo_pgsql pgsql bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir directorio de trabajo
WORKDIR /var/www

# Copiar el código del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Dar permisos a las carpetas de almacenamiento y caché
RUN chmod -R 777 storage bootstrap/cache

# Exponer el puerto
EXPOSE 10000

# Script de entrada
CMD ["sh", "docker-entrypoint.sh"]