FROM php:8.3.7-fpm-alpine

# Instalar dependencias del sistema y extensiones de PHP
RUN apk add --no-cache linux-headers && \
    apk --no-cache upgrade && \
    apk --no-cache add bash git sudo openssh libxml2-dev oniguruma-dev autoconf gcc g++ make npm \
    freetype-dev libjpeg-turbo-dev libpng-dev libzip-dev ssmtp icu-dev postgresql-dev && \
    pecl channel-update pecl.php.net && \
    pecl install pcov swoole && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install mbstring xml pcntl gd zip sockets pdo pdo_pgsql pgsql bcmath soap intl && \
    docker-php-ext-enable mbstring xml gd zip pcov pcntl sockets bcmath pdo pdo_pgsql pgsql soap intl swoole

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar RoadRunner
COPY --from=spiralscout/roadrunner:2.4.2 /usr/bin/rr /usr/bin/rr

# Copiar la aplicación y instalar dependencias
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader && \
    composer require laravel/octane spiral/roadrunner && \
    php artisan octane:install --server="swoole"

# Configurar entorno y permisos
COPY .env.example .env
RUN mkdir -p /app/storage/logs && \
    chmod -R 775 /app/storage

# Exponer el puerto y ejecutar la aplicación
EXPOSE 8000
CMD php artisan octane:start --server="swoole" --host="0.0.0.0"