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

# Cache config, routes, and events
php artisan config:cache
php artisan route:cache
php artisan event:cache

# Precompile views (don't delete - old container may still need them during deploy)
php artisan view:cache

# Run the main command
exec "$@"
