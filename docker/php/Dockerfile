FROM php:8.2-fpm-alpine

# Install system deps including mailparse requirements
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    re2c

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql zip intl mbstring bcmath pcntl

# Install mailparse (requires pecl)
RUN pecl install mailparse && docker-php-ext-enable mailparse

# Install Redis
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js for asset compilation
RUN apk add --no-cache nodejs npm

WORKDIR /var/www/html
