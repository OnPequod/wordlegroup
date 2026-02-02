# =============================================================================
# Stage 1: Composer dependencies
# =============================================================================
FROM composer:2 AS composer-deps

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .

RUN composer dump-autoload --optimize --no-dev --no-scripts

# =============================================================================
# Stage 2: Node.js asset build
# =============================================================================
FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

# Copy files needed for Tailwind class scanning
COPY resources ./resources
COPY app/View ./app/View
COPY app/Http/Livewire ./app/Http/Livewire
COPY vite.config.js ./

RUN npm run build

# =============================================================================
# Stage 3: Production image
# =============================================================================
FROM php:8.4-fpm-alpine AS production

LABEL maintainer="Wordle Group"
LABEL org.opencontainers.image.source="https://github.com/OnPequod/wordlegroup"
LABEL service="wordle-group"

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    re2c \
    dcron \
    curl

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    intl \
    mbstring \
    bcmath \
    pcntl \
    opcache

# Install PECL extensions
RUN pecl install mailparse redis \
    && docker-php-ext-enable mailparse redis

# Cleanup build deps
RUN apk del autoconf g++ make re2c

# Create app user
RUN addgroup -g 1000 -S www && adduser -u 1000 -S www -G www

# Set working directory
WORKDIR /var/www/html

# Copy configuration files
COPY docker/production/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/production/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/production/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/production/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/production/crontab /etc/crontabs/www
COPY docker/production/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Copy application from previous stages
COPY --from=composer-deps /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Copy application code
COPY --chown=www:www . .

# Remove development files and cached configs
RUN rm -rf \
    docker/php \
    docker/nginx \
    tests \
    .git \
    .github \
    node_modules \
    .env.example \
    phpunit.xml \
    .phpunit.cache \
    bootstrap/cache/*.php \
    storage/framework/cache/* \
    storage/framework/views/* \
    storage/framework/sessions/* \
    public/hot

# Set permissions
RUN chown -R www:www /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create required directories
RUN mkdir -p /run/nginx /var/log/supervisor /var/log/nginx

# Health check - only for web containers (job/scheduler don't run nginx)
# If nginx not running (job/scheduler), always healthy. If nginx running (web), check /health.
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD pgrep nginx > /dev/null || exit 0; curl -f http://localhost/health || exit 1

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
