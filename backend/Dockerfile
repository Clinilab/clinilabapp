# Imagen base con PHP 7.4 y Apache
FROM php:7.4-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git unzip zip libpq-dev libzip-dev libonig-dev libxml2-dev libicu-dev \
    zlib1g-dev libjpeg-dev libpng-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_pgsql intl iconv zip gd ctype

# Activar mod_rewrite para Symfony y Angular routing
RUN a2enmod rewrite

# Cambiar DocumentRoot a /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Copiar Composer desde imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del backend (con frontend dentro de public/frontend)
COPY . .

# Instalar dependencias de Symfony
RUN composer install --no-interaction --no-scripts || true

# Limpiar caché
RUN php bin/console cache:clear || true

# Asegurar permisos
RUN chown -R www-data:www-data var

# Puerto de salida
EXPOSE 80
