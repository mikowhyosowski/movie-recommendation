FROM php:8.3-fpm

# Instalacja zależności systemowych i rozszerzeń PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql intl zip opcache \
    && pecl install apcu && docker-php-ext-enable apcu

# Instalacja Composera
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Konfiguracja PHP
# COPY /var/www/html/docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Ustawienie uprawnień (bez wcześniejszej instalacji zależności)
RUN chown -R www-data:www-data /var/www/html