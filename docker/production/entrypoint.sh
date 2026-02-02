#!/bin/sh
set -e

# Fix storage permissions (mounted volume from host)
chown -R www:www /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Ensure required directories exist
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs

# Clear all caches first (avoid stale cache conflicts)
php artisan optimize:clear --quiet 2>/dev/null || true

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache

# Run the main command
exec "$@"
