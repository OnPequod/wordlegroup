#!/bin/sh
set -e

# Fix storage permissions (mounted volume from host)
chown -R www:www /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Ensure required directories exist
mkdir -p /var/www/html/storage/framework/{cache,sessions,views}
mkdir -p /var/www/html/storage/logs

# Clear compiled views on startup (ensures fresh compilation)
rm -rf /var/www/html/storage/framework/views/*

# Cache config, routes, and events
php artisan config:cache
php artisan route:cache
php artisan event:cache

# Run the main command
exec "$@"
