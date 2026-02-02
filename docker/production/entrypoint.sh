#!/bin/sh
set -e

# Fix storage permissions (mounted volumes from host)
chown -R www:www /var/www/html/storage/app /var/www/html/storage/logs 2>/dev/null || true
chmod -R 775 /var/www/html/storage 2>/dev/null || true

# Ensure framework directories exist (not mounted - container-local)
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views

# Cache everything for production performance
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache

# Run the main command
exec "$@"
